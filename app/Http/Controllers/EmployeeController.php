<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Prize;
use App\Models\Plant;
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
        $query = Employee::query();
        if ($request->has('plant_id') && $request->plant_id && $request->plant_id !== 'all') {
            $query->where('plant_id', $request->plant_id);
        }
        $employees = $query->get();
        $prizes = Prize::all();
        $nama_hadiah_manual = $request->query('hadiah', 'Doorprize');
        $selected_plant = $request->query('plant_id');
        return view('gacha', compact('employees', 'prizes', 'nama_hadiah_manual', 'selected_plant'));
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
        return back();
    }

    public function importEmployees(Request $request)
    {
        if ($request->hasFile('file_excel')) {
            $file = $request->file('file_excel');
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
                        $plant = Plant::where('nama_plant', $plantName)->first();
                        if ($plant) {
                            $plantId = $plant->id;
                        } else {
                            $missingPlants[] = $plantName;
                        }
                    }

                    Employee::create([
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
                $response = $response->with('warning', 'Plant tidak ditemukan untuk: ' . $missingNames);
            }
            return $response;
        }
        return back()->with('error', 'File tidak ditemukan');
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
