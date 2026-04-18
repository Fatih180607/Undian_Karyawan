<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Prize;
use Illuminate\Support\Facades\File;

class EmployeeController extends Controller
{
    public function indexAdmin()
    {
        $employees = Employee::orderBy('id', 'desc')->get();
        $prizes = Prize::all();
        return view('admin', compact('employees', 'prizes'));
    }

    public function indexGacha(Request $request)
    {
        $employees = Employee::all();
        $prizes = Prize::all();
        $nama_hadiah_manual = $request->query('hadiah', 'Doorprize');
        return view('gacha', compact('employees', 'prizes', 'nama_hadiah_manual'));
    }

    public function addEmployee(Request $request)
    {
        $request->validate(['employee_number' => 'required', 'employee_name' => 'required']);
        Employee::create(['employee_number' => $request->employee_number, 'employee_name' => $request->employee_name]);
        return back();
    }

    public function importEmployees(Request $request)
    {
        if ($request->hasFile('file_excel')) {
            $file = $request->file('file_excel');
            $handle = fopen($file->getRealPath(), "r");

            // Skip baris pertama (header NPK;Nama Karyawan)
            fgetcsv($handle, 1000, ";");

            $count = 0;
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                if (isset($data[0]) && isset($data[1])) {
                    Employee::create([
                        'employee_number' => trim($data[0]),
                        'employee_name'   => trim($data[1]),
                    ]);
                    $count++;
                }
            }
            fclose($handle);

            return back()->with('success', $count . ' Data berhasil diimport!');
        }
        return back()->with('error', 'File tidak ditemukan');
    }

    public function updateEmployee(Request $request, $id)
    {
        $employee = Employee::find($id);
        if ($employee) {
            $employee->update([
                'employee_number' => $request->employee_number,
                'employee_name' => $request->employee_name,
            ]);
        }
        return back();
    }

    public function deleteEmployee($id)
    {
        Employee::destroy($id);
        return back();
    }

    public function addPrize(Request $request)
    {
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $nama_file = time() . "_" . $file->getClientOriginalName();
            $file->move(public_path('images'), $nama_file);
            Prize::create(['nama_hadiah' => $request->nama_hadiah, 'foto_hadiah' => $nama_file]);
        }
        return back();
    }

    public function deletePrize($id)
    {
        $prize = Prize::find($id);
        if ($prize) {
            $imagePath = public_path('images/' . $prize->foto_hadiah);
            if (File::exists($imagePath)) File::delete($imagePath);
            $prize->delete();
        }
        return back();
    }

    public function storeWinner(Request $request)
    {
        $employee = Employee::find($request->id_employee);
        if ($employee) {
            $employee->delete();
            return response()->json(['status' => 'success']);
        }
        return response()->json(['status' => 'failed'], 404);
    }
}
