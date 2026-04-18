<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KategoriBeasiswa;
use App\Models\PesertaBeasiswa;
use App\Models\Plant;
use App\Models\BeasiswaKuotaPlant; // Model baru untuk tabel kuota

class BeasiswaController extends Controller
{
    // Tampilan Dashboard Admin
    public function admin() {
        $kategori = KategoriBeasiswa::all();
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

    // Layar Undian Utama
    public function layarUndian() {
        $kategori = KategoriBeasiswa::all();
        return view('beasiswa_undi', compact('kategori'));
    }

    // Logika Pengacakan (API untuk AJAX)
    public function kocokBeasiswa(Request $request) {
        $jenjang = $request->jenjang;
        $jumlah = $request->jumlah;
        $plant_id = $request->plant_id; // Jika ingin filter per plant saat mengundi

        $query = PesertaBeasiswa::where('jenjang_sekolah', $jenjang)
                                ->where('is_winner', false);

        if($plant_id) {
            $query->where('plant_id', $plant_id);
        }

        $pemenang = $query->inRandomOrder()->limit($jumlah)->get();

        foreach ($pemenang as $p) {
            $p->update(['is_winner' => true]);
        }

        return response()->json($pemenang);
    }
}
