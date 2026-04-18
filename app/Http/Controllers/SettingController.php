<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plant; // Pastikan model di-import

class SettingController extends Controller
{
    /**
     * Menampilkan halaman setting utama
     */
    public function index()
    {
        // Ambil semua data plant dari database
        $plants = Plant::all();

        // Variabel background (masih null sesuai permintaan sebelumnya)
        $background = null;

        return view('settings', compact('plants', 'background'));
    }

    /**
     * Simpan Plant Baru
     */
    public function addPlant(Request $request)
    {
        $request->validate([
            'nama_plant' => 'required|string|max:255',
        ]);

        Plant::create([
            'nama_plant' => $request->nama_plant
        ]);

        return back()->with('success', 'Plant berhasil ditambahkan!');
    }

    /**
     * Update Nama Plant (Fitur Edit)
     */
    public function updatePlant(Request $request, $id)
    {
        $request->validate([
            'nama_plant' => 'required|string|max:255',
        ]);

        $plant = Plant::find($id);
        if ($plant) {
            $plant->update([
                'nama_plant' => $request->nama_plant
            ]);
            return back()->with('success', 'Nama plant berhasil diperbarui!');
        }

        return back()->with('error', 'Plant tidak ditemukan!');
    }

    /**
     * Hapus Plant
     */
    public function deletePlant($id)
    {
        $plant = Plant::find($id);
        if ($plant) {
            $plant->delete();
            return back()->with('success', 'Plant berhasil dihapus!');
        }

        return back()->with('error', 'Plant tidak ditemukan!');
    }

    /**
     * Update Background Global (Masih Pending)
     */
    public function updateBackground(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        return back()->with('info', 'Fitur upload background belum diaktifkan.');
    }
}
