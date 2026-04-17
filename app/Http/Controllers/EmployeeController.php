<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Prize;

class EmployeeController extends Controller
{
    public function indexAdmin()
    {
        $employees = Employee::orderBy('id', 'desc')->get();
        $prizes = Prize::all();
        return view('admin', compact('employees', 'prizes'));
    }

    public function indexGacha()
    {
        $employees = Employee::all();
        $prizes = Prize::all();
        return view('gacha', compact('employees', 'prizes'));
    }

    public function addEmployee(Request $request)
    {
        Employee::create([
            'employee_number' => $request->employee_number,
            'employee_name' => $request->employee_name,
        ]);
        return back();
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

            Prize::create([
                'nama_hadiah' => $request->nama_hadiah,
                'foto_hadiah' => $nama_file
            ]);
        }
        return back();
    }

    public function deletePrize($id)
    {
        Prize::destroy($id);
        return back();
    }

    // LOGIKA HAPUS OTOMATIS SAAT MENANG
    public function storeWinner(Request $request)
    {
        $employee = Employee::find($request->id_employee);
        if ($employee) {
            $employee->delete(); // Hapus permanen
            return response()->json(['status' => 'success', 'message' => 'Pemenang dihapus dari daftar']);
        }
        return response()->json(['status' => 'failed'], 404);
    }
}