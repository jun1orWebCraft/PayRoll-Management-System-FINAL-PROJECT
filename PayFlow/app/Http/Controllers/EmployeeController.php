<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\File;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Writer\PngWriter;



class EmployeeController extends Controller
{
    /**
     * Display a listing of the employees.
     */
  public function index(Request $request)
    {
        $query = Employee::with('position');

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by employment type
        if ($request->filled('employment_type')) {
            $query->where('employment_type', $request->employment_type);
        }

        $employees = $query->paginate(10); // paginate if many employees

        return view('pages.employees', compact('employees'));
    }


    /**
     * Show the form for creating a new employee.
     */
    public function create()
    {
        return view('pages.employees');
    }

    /**
     * Store a newly created employee in storage.
     */



public function store(Request $request)
{
    $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|email|unique:employees,email',
        'phone' => 'nullable|string|max:20',
        'address' => 'nullable|string|max:255',
        'hire_date' => 'required|date',
        'basic_salary' => 'required|numeric|min:0',
        'status' => 'required|in:Active,Inactive,On Leave',
        'employment_type' => 'required|string|max:50',
        'position_id' => 'required|integer',
        'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $employee_no = Employee::generateEmployeeNo();
    $randomPassword = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()'), 0, 8);

    $profilePicturePath = $request->hasFile('profile_picture') 
        ? $request->file('profile_picture')->store('profile_pictures', 'public') 
        : null;

    // Generate QR code using Endroid (pure PHP)
    $result = Builder::create()
        ->writer(new PngWriter())
        ->data($employee_no)
        ->size(200)
        ->encoding(new Encoding('UTF-8'))
        ->build();

    $qrCodeBase64 = 'data:image/png;base64,' . base64_encode($result->getString());

    Employee::create([
        'employee_no' => $employee_no,
        'QR_code' => $qrCodeBase64,
        'first_name' => $request->first_name,
        'last_name' => $request->last_name,
        'email' => $request->email,
        'password' => Hash::make($randomPassword),
        'phone' => $request->phone,
        'address' => $request->address,
        'hire_date' => $request->hire_date,
        'basic_salary' => $request->basic_salary,
        'status' => $request->status,
        'employment_type' => $request->employment_type,
        'position_id' => $request->position_id,
        'profile_picture' => $profilePicturePath,
    ]);

    return redirect()->route('employees.index')->with('success', 'Employee added successfully.');
}




    /**
     * Display the specified employee.
     */
    public function show(Employee $employee)
    {
        return view('pages.employees', compact('employee'));
    }

    /**
     * Show the form for editing the specified employee.
     */
    public function edit(Employee $employee)
    {
        return view('pages.employees', compact('employee'));
    }

    /**
     * Update the specified employee in storage.
     */
    public function update(Request $request, Employee $employee)
{
    $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|email|unique:employees,email,' . $employee->employee_id . ',employee_id',
        'phone' => 'nullable|string|max:20',
        'address' => 'nullable|string|max:255',
        'basic_salary' => 'required|numeric|min:0',
        'status' => 'required|in:Active,Inactive,On Leave',
        'employment_type' => 'required|string|max:50',
        'position_id' => 'required|integer',
        'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'password' => 'nullable|min:6', // ✅ allow optional password update
    ]);

    // ✅ Handle file update
    if ($request->hasFile('profile_picture')) {
        if ($employee->profile_picture) {
            Storage::disk('public')->delete($employee->profile_picture);
        }
        $employee->profile_picture = $request->file('profile_picture')->store('profile_pictures', 'public');
    }

    // ✅ Update employee data
    $employee->update([
        'first_name' => $request->first_name,
        'last_name' => $request->last_name,
        'email' => $request->email,
        'phone' => $request->phone,
        'address' => $request->address,
        'basic_salary' => $request->basic_salary,
        'status' => $request->status,
        'employment_type' => $request->employment_type,
        'position_id' => $request->position_id,
        'profile_picture' => $employee->profile_picture,
    ]);


    return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
}


    /**
     * Remove the specified employee from storage.
     */
    public function destroy(Employee $employee)
    {
        if ($employee->profile_picture) {
            Storage::disk('public')->delete($employee->profile_picture);
        }

        $employee->delete();

        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully.');
    }
}
