<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KategoriBeasiswa;
use App\Models\PesertaBeasiswa;
use App\Models\Plant;
use App\Models\BeasiswaKuotaPlant;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log; // FIX: Biar VS Code gak merah lagi

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
        $request->validate(['file_excel' => 'required|file|mimes:csv,txt']);
        $file = $request->file('file_excel');
        $handle = fopen($file->getRealPath(), 'r');
        $firstLine = fgets($handle);
        $delimiter = strpos($firstLine, ';') !== false ? ';' : ',';
        rewind($handle);

        $headerRow = fgetcsv($handle, 1000, $delimiter);
        $headerMap = [];
        $fieldNames = [
            'nama_anak'      => ['nama_anak', 'nama anak'],
            'jenjang_sekolah'=> ['jenjang_sekolah', 'jenjang sekolah', 'jenjang'],
            'npk_orang_tua'  => ['npk_orang_tua', 'npk'],
            'nama_orang_tua' => ['nama_orang_tua', 'nama orang tua', 'nama karyawan'],
            'nama_plant'     => ['nama_plant', 'nama plant', 'plant']
        ];

        foreach ($headerRow as $index => $h) {
            $cleanHeader = strtolower(trim(preg_replace('/^\xEF\xBB\xBF/', '', $h)));
            foreach ($fieldNames as $field => $aliases) {
                if (in_array($cleanHeader, $aliases)) { $headerMap[$field] = $index; }
            }
        }

        $row = 0; $imported = 0;
        while (($data = fgetcsv($handle, 1000, $delimiter)) !== false) {
            $row++;
            if (empty(array_filter($data))) continue;
            $namaPlant = mb_convert_encoding(trim($data[$headerMap['nama_plant']] ?? ''), 'UTF-8', 'UTF-8');
            $plant = Plant::where('nama_plant', $namaPlant)->first();

            if ($plant) {
                PesertaBeasiswa::create([
                    'kode_peserta'    => 'BEA-' . time() . '-' . $row,
                    'nama_anak'       => preg_replace('/[[:^print:]]/', '', mb_convert_encoding($data[$headerMap['nama_anak']], 'UTF-8', 'UTF-8')),
                    'jenjang_sekolah' => trim($data[$headerMap['jenjang_sekolah']]),
                    'npk_orang_tua'   => trim($data[$headerMap['npk_orang_tua']]),
                    'nama_orang_tua'  => preg_replace('/[[:^print:]]/', '', mb_convert_encoding($data[$headerMap['nama_orang_tua']], 'UTF-8', 'UTF-8')),
                    'plant_id'        => $plant->id,
                    'is_winner'       => false,
                ]);
                $imported++;
            }
        }
        fclose($handle);
        return back()->with('success', "$imported peserta berhasil diimport.");
    }

    public function simpanKategori(Request $request)
    {
        $plantId = $request->plant_id;
        foreach ($request->jenjang_sekolah as $index => $jenjang) {
            $jenjang = trim($jenjang);
            if ($jenjang === '') continue;

            $kategori = KategoriBeasiswa::updateOrCreate(
                ['plant_id' => $plantId, 'jenjang_sekolah' => $jenjang],
                [
                    'kode_sekolah' => strtoupper(str_replace(' ', '_', $jenjang)) . '-' . time() . $index,
                    'nominal' => $request->nominal[$index] ?? 0,
                    'kuota' => $request->kuota[$index] ?? 0,
                ]
            );

            BeasiswaKuotaPlant::updateOrCreate(
                ['plant_id' => $plantId, 'kategori_id' => $kategori->id],
                ['jumlah_slot' => $request->kuota[$index] ?? 0]
            );
        }
        return back()->with('success', "Kategori berhasil disimpan!");
    }

    public function layarUndian() {
        $kategori = KategoriBeasiswa::all();
        $plants = Plant::all();
        $kuotas = BeasiswaKuotaPlant::with('kategori')->get();
        $quotaData = [];
        foreach ($kuotas as $item) {
            $jenjang = $item->kategori->jenjang_sekolah ?? $item->kategori_id;
            $slug = preg_replace('/[^a-z0-9_]/', '', strtolower(str_replace(' ', '_', $jenjang)));
            $quotaData[$item->plant_id][] = [
                'kategori_id' => $item->kategori_id,
                'jenjang_sekolah' => $jenjang,
                'jenjang_slug' => $slug,
                'jumlah_slot' => $item->jumlah_slot,
            ];
        }
        return view('beasiswa_undi', compact('kategori', 'plants', 'quotaData'));
    }

    // LOGIKA KOCOK UTAMA (SUDAH FIX ANIMASI)
    public function kocokBeasiswa(Request $request) {
        $jenjang = $request->jenjang;
        $plant_id = $request->plant_id;

        $pemenang = PesertaBeasiswa::where('jenjang_sekolah', $jenjang)
                    ->where('plant_id', $plant_id)
                    ->where('is_winner', false)
                    ->inRandomOrder()
                    ->limit($request->jumlah)
                    ->get();

        if ($pemenang->isNotEmpty()) {
            PesertaBeasiswa::whereIn('id', $pemenang->pluck('id'))->update(['is_winner' => true]);
        }
        return response()->json($pemenang);
    }

    // API POOL PESERTA (SUDAH FIX AGAR POOL TETAP BANYAK)
    public function getPesertaList(Request $request) {
        $peserta = PesertaBeasiswa::where('jenjang_sekolah', 'LIKE', trim($request->jenjang))
                    ->where('plant_id', $request->plant_id)
                    ->get(); // Tidak pakai filter is_winner false agar animasi ramai

        Log::info("Cek Pool: " . $request->jenjang . " - Hasil: " . $peserta->count());
        return response()->json($peserta);
    }

    public function exportPemenang(Request $request)
    {
        $pemenang = PesertaBeasiswa::with('plant')->where('plant_id', $request->plant_id)->where('is_winner', true)->get();
        $filename = 'pemenang_beasiswa_' . time() . '.csv';
        $callback = function () use ($pemenang) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Plant', 'Nama Anak', 'Nama Orang Tua', 'NPK', 'Jenjang']);
            foreach ($pemenang as $p) {
                fputcsv($file, [$p->plant->nama_plant ?? 'N/A', $p->nama_anak, $p->nama_orang_tua, $p->npk_orang_tua, $p->jenjang_sekolah]);
            }
            fclose($file);
        };
        return Response::stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function updatePeserta(Request $request, $id) {
        $peserta = PesertaBeasiswa::findOrFail($id);
        $peserta->update($request->all());
        return back()->with('success', 'Data diperbarui!');
    }

    public function deletePeserta($id) {
        PesertaBeasiswa::findOrFail($id)->delete();
        return back()->with('success', 'Peserta dihapus!');
    }

    public function resetPeserta() {
        PesertaBeasiswa::truncate();
        return back()->with('success', 'Data direset!');
    }
}
