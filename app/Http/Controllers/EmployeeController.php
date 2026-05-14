<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Prize;
use App\Models\Plant;
use App\Models\DoorprizeWinner;
use Illuminate\Support\Facades\File;

class EmployeeController extends Controller
{
    public function indexAdmin()
    {
        $employees = Employee::with('plant')->orderBy('id', 'desc')->get();
        $prizes = Prize::all();
        $plants = Plant::all();
        return view('admin', compact('employees', 'prizes', 'plants'));
    }

    public function indexGacha(Request $request)
    {
        $employees = Employee::with('plant')->where('is_winner', 0)->get();
        $prizes = Prize::all();
        $nama_hadiah_manual = $request->query('hadiah', 'Doorprize');
        return view('gacha', compact('employees', 'prizes', 'nama_hadiah_manual'));
    }

    public function addEmployee(Request $request)
    {
        $request->validate([
            'employee_number' => 'required',
            'employee_name' => 'required',
            'plant_id' => 'required|exists:plants,id'
        ]);
        Employee::create([
            'employee_number' => $request->employee_number,
            'employee_name' => $request->employee_name,
            'plant_id' => $request->plant_id
        ]);
        return back()->with('success', 'Peserta berhasil ditambahkan!');
    }

    // FUNGSI BARU: Reset Pemenang
    public function resetWinners()
    {
        Employee::where('is_winner', 1)->update([
            'is_winner' => 0,
            'prize_won' => null
        ]);
        return back()->with('success', 'Semua pemenang telah direset menjadi peserta biasa!');
    }

    // FUNGSI BARU: Hapus Semua
    public function deleteAll()
    {
        Employee::truncate();
        return back()->with('success', 'Seluruh data peserta telah dihapus bersih!');
    }

    public function updateEmployee(Request $request, $id)
    {
        $request->validate([
            'employee_number' => 'required',
            'employee_name' => 'required',
            'plant_id' => 'required|exists:plants,id'
        ]);
        $employee = Employee::find($id);
        if ($employee) {
            $employee->update([
                'employee_number' => $request->employee_number,
                'employee_name' => $request->employee_name,
                'plant_id' => $request->plant_id,
            ]);
        }
        return back()->with('success', 'Data peserta berhasil diperbarui!');
    }

    public function deleteEmployee($id)
    {
        Employee::destroy($id);
        return back()->with('success', 'Peserta berhasil dihapus!');
    }

    public function addPrize(Request $request)
    {
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $nama_file = time() . "_" . $file->getClientOriginalName();
            $file->move(public_path('images'), $nama_file);
            Prize::create(['nama_hadiah' => $request->nama_hadiah, 'foto_hadiah' => $nama_file]);
        }
        return back()->with('success', 'Hadiah berhasil diupload!');
    }

    public function deletePrize($id)
    {
        $prize = Prize::find($id);
        if ($prize) {
            $imagePath = public_path('images/' . $prize->foto_hadiah);
            if (File::exists($imagePath)) File::delete($imagePath);
            $prize->delete();
        }
        return back()->with('success', 'Hadiah berhasil dihapus!');
    }

    public function storeWinner(Request $request)
    {
        $employee = Employee::find($request->id_employee);
        if ($employee) {
            $employee->is_winner = 1;
            $employee->prize_won = $request->nama_hadiah;
            $employee->save();

            DoorprizeWinner::create([
                'nama_hadiah' => $request->nama_hadiah,
                'nama_karyawan' => $employee->employee_name,
                'nomor_karyawan' => $employee->employee_number,
                'plant_id' => $employee->plant_id,
                'nama_plant' => $employee->plant->nama_plant ?? '-',
                'foto_hadiah' => $request->foto_hadiah,
                'waktu_menang' => now()
            ]);
            return response()->json(['status' => 'success']);
        }
        return response()->json(['status' => 'error'], 404);
    }

 public function importEmployees(Request $request)
{
    // Saya ganti 'file_excel' jadi 'file' supaya sama dengan input di admin.blade.php
    if ($request->hasFile('file')) {
        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), "r");

        // Skip header baris pertama
        fgetcsv($handle, 1000, ";");

        $count = 0;
        $missingPlants = [];

        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
            if (isset($data[0]) && isset($data[1])) {
                $plantId = null;
                if (isset($data[2]) && trim($data[2]) !== '') {
                    $plantName = trim($data[2]);
                    $plant = \App\Models\Plant::where('nama_plant', $plantName)->first();
                    if ($plant) {
                        $plantId = $plant->id;
                    } else {
                        $missingPlants[] = $plantName;
                    }
                }

                \App\Models\Employee::create([
                    'employee_number' => trim($data[0]),
                    'employee_name'   => trim($data[1]),
                    'plant_id'        => $plantId,
                ]);
                $count++;
            }
        }
        fclose($handle);

        $response = back()->with('success', $count . ' Data berhasil diimport!');
        if (!empty($missingPlants)) {
            $missingNames = implode(', ', array_unique($missingPlants));
            $response = $response->with('warning', 'Beberapa Plant tidak ditemukan: ' . $missingNames);
        }
        return $response;
    }
    return back()->with('error', 'File tidak ditemukan');
}
public function exportWinners()
{
    $winners = Employee::with('plant')->where('is_winner', 1)->get();
    $filename = "pemenang_doorprize_" . date('Ymd_His') . ".csv";

    $headers = [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=$filename",
    ];

    $callback = function() use($winners) {
        $file = fopen('php://output', 'w');
        fputcsv($file, ['NPK', 'Nama', 'Plant', 'Hadiah', 'Waktu Menang']);
        foreach ($winners as $w) {
            fputcsv($file, [
                $w->employee_number,
                $w->employee_name,
                $w->plant->nama_plant ?? '-',
                $w->prize_won,
                $w->updated_at
            ]);
        }
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}
public function exportFullPemenangTable()
{
    // Ambil SEMUA yang sudah menang
    $winners = Employee::with('plant')->where('is_winner', 1)->orderBy('updated_at', 'desc')->get();

    $filename = "rekap_seluruh_pemenang_" . date('Ymd_His') . ".csv";

    $headers = [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=$filename",
    ];

    $callback = function() use($winners) {
        $file = fopen('php://output', 'w');
        // Baris Header CSV
        fputcsv($file, ['NPK', 'Nama Karyawan', 'Plant', 'Hadiah', 'Waktu Menang']);

        foreach ($winners as $w) {
            fputcsv($file, [
                $w->employee_number,
                $w->employee_name,
                $w->plant->nama_plant ?? '-',
                $w->prize_won,
                $w->updated_at
            ]);
        }
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}   
}
