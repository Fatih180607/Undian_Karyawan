<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KategoriBeasiswa;
use App\Models\PesertaBeasiswa;
use App\Models\Plant;
use App\Models\BeasiswaKuotaPlant;
use Illuminate\Support\Facades\Response;

class BeasiswaController extends Controller
{
    // Tampilan Dashboard Admin
    public function admin() {
        $kategori = KategoriBeasiswa::with('plant')->orderBy('created_at', 'desc')->get();
        $peserta = PesertaBeasiswa::with('plant')->orderBy('created_at', 'desc')->get();
        $plants = Plant::all();

        return view('beasiswa_dashboard', compact('kategori', 'peserta', 'plants'));
    }

    // Simpan Peserta Baru (Manual)
    public function simpanPeserta(Request $request) {
        $request->validate([
            'nama_anak' => 'required',
            'jenjang_sekolah' => 'required',
            'npk_orang_tua' => 'required',
            'nama_orang_tua' => 'required',
            'plant_id' => 'required'
        ]);

        PesertaBeasiswa::create([
            'kode_peserta' => 'BEA-' . time(),
            'nama_anak' => $request->nama_anak,
            'jenjang_sekolah' => $request->jenjang_sekolah,
            'npk_orang_tua' => $request->npk_orang_tua,
            'nama_orang_tua' => $request->nama_orang_tua,
            'plant_id' => $request->plant_id,
            'is_winner' => false
        ]);

        return back()->with('success', 'Peserta berhasil ditambah!');
    }

    public function importPeserta(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|file|mimes:csv,txt'
        ]);

        $file = $request->file('file_excel');
        $handle = fopen($file->getRealPath(), 'r');
        if (!$handle) {
            return back()->with('error', 'Tidak bisa membaca file CSV.');
        }

        $firstLine = fgets($handle);
        if ($firstLine === false) {
            fclose($handle);
            return back()->with('error', 'File CSV kosong.');
        }

        $delimiter = strpos($firstLine, ';') !== false ? ';' : ',';
        $headerRow = str_getcsv(trim($firstLine), $delimiter);
        $headerMap = [];
        $normalizedHeaders = array_map(function ($value) {
            $value = preg_replace('/^\xEF\xBB\xBF/', '', $value); // hapus BOM jika ada
            $value = trim($value);
            $value = strtolower($value);
            $value = preg_replace('/[^a-z0-9]+/', '_', $value);
            $value = trim($value, '_');
            return $value;
        }, $headerRow);

        $fieldNames = [
            'nama_anak' => ['nama_anak', 'nama anak', 'nama-anak', 'nama_anak'],
            'jenjang_sekolah' => ['jenjang_sekolah', 'jenjang sekolah', 'jenjang-sekolah', 'jenjang'],
            'npk_orang_tua' => ['npk_orang_tua', 'npk orang tua', 'npk-orang-tua', 'npk'],
            'nama_orang_tua' => ['nama_orang_tua', 'nama orang tua', 'nama-orang-tua'],
            'nama_plant' => ['nama_plant', 'nama plant', 'plant']
        ];

        foreach ($normalizedHeaders as $index => $header) {
            foreach ($fieldNames as $field => $aliases) {
                if (in_array($header, $aliases, true)) {
                    $headerMap[$field] = $index;
                    break;
                }
            }
        }

        $hasHeader = count($headerMap) >= 3;
        if ($hasHeader && !isset($headerMap['nama_anak'])) {
            $hasHeader = false;
        }

        $row = 0;
        $imported = 0;
        $errors = [];

        if (!$hasHeader) {
            rewind($handle);
        }

        while (($data = fgetcsv($handle, 1000, $delimiter)) !== false) {
            $row++;
            if (count($data) === 1 && trim($data[0]) === '') {
                continue;
            }

            if ($hasHeader && $row === 1) {
                continue;
            }

            $namaAnak = $hasHeader ? trim($data[$headerMap['nama_anak']] ?? '') : trim($data[0] ?? '');
            $jenjang = $hasHeader ? trim($data[$headerMap['jenjang_sekolah']] ?? '') : trim($data[1] ?? '');
            $npk = $hasHeader ? trim($data[$headerMap['npk_orang_tua']] ?? '') : trim($data[2] ?? '');
            $namaOrtu = $hasHeader ? trim($data[$headerMap['nama_orang_tua']] ?? '') : trim($data[3] ?? '');
            $namaPlant = $hasHeader ? trim($data[$headerMap['nama_plant']] ?? '') : trim($data[4] ?? '');

            if ($namaAnak === '' && $jenjang === '' && $npk === '' && $namaOrtu === '' && $namaPlant === '') {
                continue;
            }

            if (!$namaAnak || !$jenjang || !$npk || !$namaOrtu || !$namaPlant) {
                $errors[] = "Baris $row: data tidak lengkap. Pastikan semua kolom terisi.";
                continue;
            }

            $plant = Plant::whereRaw('LOWER(nama_plant) = ?', [strtolower($namaPlant)])->first();
            if (!$plant) {
                $errors[] = "Baris $row: Plant '$namaPlant' tidak ditemukan.";
                continue;
            }

            PesertaBeasiswa::create([
                'kode_peserta' => 'BEA-' . time() . '-' . $row,
                'nama_anak' => $namaAnak,
                'jenjang_sekolah' => $jenjang,
                'npk_orang_tua' => $npk,
                'nama_orang_tua' => $namaOrtu,
                'plant_id' => $plant->id,
                'is_winner' => false,
            ]);

            $imported++;
        }

        fclose($handle);

        if (!empty($errors)) {
            $message = "Import selesai dengan $imported baris berhasil dan " . count($errors) . " baris bermasalah.";
            return back()->with('error', implode(' | ', $errors))->with('info', $message);
        }

        return back()->with('success', "$imported peserta berhasil diimport.");
    }

    // Update Kuota per Plant & Jenjang (No. 4)
    public function updateKuotaPlant(Request $request) {
        $kuotaData = $request->input('kuota'); // Mengambil array kuota[plant][kategori]

        if ($kuotaData) {
            foreach ($kuotaData as $plantId => $kategoris) {
                foreach ($kategoris as $kategoriId => $jumlah) {
                    BeasiswaKuotaPlant::updateOrCreate(
                        ['plant_id' => $plantId, 'kategori_id' => $kategoriId],
                        ['jumlah_slot' => $jumlah ?? 0]
                    );
                }
            }
            return back()->with('success', 'Settingan kuota per plant berhasil disimpan!');
        }

        return back()->with('error', 'Data kuota kosong!');
    }

    // Simpan kategori / jenjang baru
    public function simpanKategori(Request $request)
    {
        $request->validate([
            'plant_id' => 'required|exists:plants,id',
            'kategori_id' => 'nullable|array',
            'kategori_id.*' => 'nullable|integer|exists:KategoriBeasiswa,id',
            'jenjang_sekolah' => 'required|array|min:1',
            'jenjang_sekolah.*' => 'required|string|max:255',
            'nominal' => 'required|array',
            'nominal.*' => 'required|integer|min:0',
            'kuota' => 'nullable|array',
            'kuota.*' => 'nullable|integer|min:0',
        ]);

        $plantId = $request->plant_id;
        $existingIds = KategoriBeasiswa::where('plant_id', $plantId)->pluck('id')->toArray();
        $submittedIds = array_filter($request->kategori_id ?? [], fn($id) => !empty($id));

        $deletedIds = array_diff($existingIds, $submittedIds);
        if (!empty($deletedIds)) {
            BeasiswaKuotaPlant::where('plant_id', $plantId)->whereIn('kategori_id', $deletedIds)->delete();
            KategoriBeasiswa::whereIn('id', $deletedIds)->delete();
        }

        $updated = 0;
        foreach ($request->jenjang_sekolah as $index => $jenjang) {
            $jenjang = trim($jenjang);
            if ($jenjang === '') {
                continue;
            }

            $nominal = $request->nominal[$index] ?? 0;
            $kuota = $request->kuota[$index] ?? 0;
            $kategoriId = $request->kategori_id[$index] ?? null;

            if ($kategoriId) {
                $kategori = KategoriBeasiswa::find($kategoriId);
                if ($kategori) {
                    $kategori->update([
                        'jenjang_sekolah' => $jenjang,
                        'nominal' => $nominal,
                        'kuota' => $kuota,
                    ]);
                }
            } else {
                $kategori = KategoriBeasiswa::create([
                    'plant_id' => $plantId,
                    'kode_sekolah' => strtoupper(str_replace(' ', '_', $jenjang)) . '-' . time() . $index,
                    'jenjang_sekolah' => $jenjang,
                    'nominal' => $nominal,
                    'kuota' => $kuota,
                ]);
            }

            if ($kategori) {
                BeasiswaKuotaPlant::updateOrCreate(
                    ['plant_id' => $plantId, 'kategori_id' => $kategori->id],
                    ['jumlah_slot' => $kuota]
                );
                $updated++;
            }
        }

        return back()->with('success', "$updated jenjang berhasil disimpan untuk plant ini!");
    }

    // Layar Undian Utama
    public function layarUndian() {
        $kategori = KategoriBeasiswa::all();
        $plants = Plant::all();
        $kuotas = BeasiswaKuotaPlant::with('kategori')->get();

        $quotaData = [];
        foreach ($kuotas as $item) {
            $jenjang = $item->kategori->jenjang_sekolah ?? $item->kategori_id;
            $slug = strtolower(str_replace(' ', '_', $jenjang));
            $slug = preg_replace('/[^a-z0-9_\-]/', '', $slug);

            $quotaData[$item->plant_id][] = [
                'kategori_id' => $item->kategori_id,
                'jenjang_sekolah' => $jenjang,
                'jenjang_slug' => $slug,
                'jumlah_slot' => $item->jumlah_slot,
            ];
        }

        return view('beasiswa_undi', compact('kategori', 'plants', 'quotaData'));
    }

    // Logika Pengacakan (API untuk AJAX)
    public function kocokBeasiswa(Request $request) {
        $jenjang = $request->jenjang;
        $jumlah = $request->jumlah;
        $plant_id = $request->plant_id; // Jika ingin filter per plant saat mengundi

        $query = PesertaBeasiswa::where('jenjang_sekolah', $jenjang);

        if ($plant_id) {
            $query->where('plant_id', $plant_id);
        }

        $pemenang = $query->inRandomOrder()->limit($jumlah)->get();
        $pemenangIds = $pemenang->pluck('id')->toArray();

        if (!empty($pemenangIds)) {
            PesertaBeasiswa::whereIn('id', $pemenangIds)->delete();
        }

        return response()->json($pemenang);
    }

    // Export pemenang ke CSV
    public function exportPemenang(Request $request)
    {
        $plantId = $request->plant_id;
        if (!$plantId) {
            return back()->with('error', 'Plant ID diperlukan untuk export.');
        }

        $pemenang = PesertaBeasiswa::with('plant')
            ->where('plant_id', $plantId)
            ->where('is_winner', true)
            ->orderBy('jenjang_sekolah')
            ->get();

        if ($pemenang->isEmpty()) {
            return back()->with('warning', 'Tidak ada pemenang untuk plant ini.');
        }

        $filename = 'pemenang_beasiswa_' . $pemenang->first()->plant->nama_plant . '_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($pemenang) {
            $file = fopen('php://output', 'w');

            // Header CSV
            fputcsv($file, ['Plant', 'Nama Anak', 'Nama Orang Tua', 'NPK Orang Tua', 'Jenjang']);

            // Data
            foreach ($pemenang as $p) {
                fputcsv($file, [
                    $p->plant->nama_plant ?? 'N/A',
                    $p->nama_anak,
                    $p->nama_orang_tua,
                    $p->npk_orang_tua,
                    $p->jenjang_sekolah,
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function updatePeserta(Request $request, $id)
    {
        $request->validate([
            'nama_anak' => 'required|string|max:255',
            'jenjang_sekolah' => 'required|string|max:255',
            'npk_orang_tua' => 'required|string|max:255',
            'nama_orang_tua' => 'required|string|max:255',
            'plant_id' => 'required|exists:plants,id',
        ]);

        $peserta = PesertaBeasiswa::findOrFail($id);
        $peserta->update([
            'nama_anak' => $request->nama_anak,
            'jenjang_sekolah' => $request->jenjang_sekolah,
            'npk_orang_tua' => $request->npk_orang_tua,
            'nama_orang_tua' => $request->nama_orang_tua,
            'plant_id' => $request->plant_id,
        ]);

        return back()->with('success', 'Data peserta berhasil diperbarui!');
    }

    public function deletePeserta($id)
    {
        $peserta = PesertaBeasiswa::findOrFail($id);
        $peserta->delete();

        return back()->with('success', 'Peserta berhasil dihapus!');
    }

    public function resetPeserta()
    {
        PesertaBeasiswa::truncate();

        return back()->with('success', 'Semua data peserta beasiswa telah direset!');
    }
}
