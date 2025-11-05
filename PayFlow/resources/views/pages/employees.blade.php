@extends('layouts.app')

@section('title', 'Employee Management')

@section('content')
<div class="container py-3">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">Employees</h2>
            <p class="text-muted mb-0">Manage and track all employee information</p>
        </div>
        <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
            <i class="bi bi-plus-lg"></i> Add Employee
        </button>
    </div>

<form method="GET" action="{{ route('employees.index') }}">
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <input type="text" name="search" class="form-control shadow-sm" placeholder="Search employees..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select shadow-sm" onchange="this.form.submit()">
                <option value="">All Status</option>
                <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Active</option>
                <option value="Inactive" {{ request('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="On Leave" {{ request('status') == 'On Leave' ? 'selected' : '' }}>On Leave</option>
            </select>
        </div>
        <div class="col-md-3">
            <select name="employment_type" class="form-select shadow-sm" onchange="this.form.submit()">
                <option value="">Employment Type</option>
                <option value="Full-Time" {{ request('employment_type') == 'Full-Time' ? 'selected' : '' }}>Full-Time</option>
                <option value="Part-Time" {{ request('employment_type') == 'Part-Time' ? 'selected' : '' }}>Part-Time</option>
            </select>
        </div>
    </div>
</form>


    {{-- Employee Table --}}
    <div class="table-responsive bg-white rounded-4 p-3 shadow-sm border">
        <table class="table table-hover align-middle text-center mb-0">
            <thead class="table-primary">
                <tr>
                    <th>Employee No</th>
                    <th>Full Name</th>
                    <th>Email</th> 
                    <th>Status</th>
                    <th>Employment Type</th>
                    <th>Position</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($employees as $employee)
                <tr>
                    <td>{{ $employee->employee_no }}</td>
                    <td class="fw-semibold">{{ $employee->first_name }} {{ $employee->last_name }}</td>
                    <td>{{ $employee->email }}</td>
                    <td>
                        @if ($employee->status == 'Active')
                            <span class="badge bg-success">Active</span>
                        @elseif ($employee->status == 'On Leave')
                            <span class="badge bg-warning text-dark">On Leave</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </td>
                    <td>{{ $employee->employment_type }}</td>
                    <td>{{ $employee->position->position_name }}</td>
                    <td>
                        {{-- View --}}
                        <button class="btn btn-link text-primary p-0 border-0 shadow-none me-2" 
                                data-bs-toggle="modal" 
                                data-bs-target="#employeeDetailsModal{{ $employee->employee_id }}">
                            <i class="bi bi-eye fs-5"></i>
                        </button>

                        {{-- Edit --}}
                        <button class="btn btn-link text-warning p-0 border-0 shadow-none me-2"
                                data-bs-toggle="modal"
                                data-bs-target="#editEmployeeModal{{ $employee->employee_id }}">
                            <i class="bi bi-pencil fs-5"></i>
                        </button>

                        {{-- Delete --}}
                        <form action="{{ route('employees.destroy', $employee->employee_id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-link text-danger p-0" onclick="return confirm('Delete this employee?')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>

                {{-- View Employee Modal --}}
                <div class="modal fade" id="employeeDetailsModal{{ $employee->employee_id }}" tabindex="-1" aria-labelledby="employeeDetailsModalLabel{{ $employee->employee_id }}" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content border-0 rounded-4 shadow-lg">
                            <div class="modal-header bg-success text-white border-0">
                                <h5 class="modal-title fw-semibold">Employee Details</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted">Employee No</label>
                                        <p class="text-dark">{{ $employee->employee_no }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted">Full Name</label>
                                        <p class="text-dark">{{ $employee->first_name }} {{ $employee->last_name }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted">Email</label>
                                        <p class="text-dark">{{ $employee->email }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted">Phone</label>
                                        <p class="text-dark">{{ $employee->phone ?? 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted">Address</label>
                                        <p class="text-dark">{{ $employee->address ?? 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted">Bithday</label>
                                        <p class="text-dark">{{ $employee->birthday }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted">Age</label>
                                        <p class="text-dark">{{ $employee->age }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted">Hire Date</label>
                                        <p class="text-dark">{{ $employee->hire_date }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted">Position</label>
                                        <p class="text-dark">{{ $employee->position->position_name ?? 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted">Status</label>
                                        <p class="text-dark">
                                            @if ($employee->status == 'Active')
                                                <span class="badge bg-success">Active</span>
                                            @elseif ($employee->status == 'On Leave')
                                                <span class="badge bg-warning text-dark">On Leave</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted">Employment Type</label>
                                        <p class="text-dark">{{ $employee->employment_type }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted">Basic Salary</label>
                                        <p class="text-dark">₱{{ number_format($employee->basic_salary, 2) }}</p>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold text-muted">QR Code</label>
                                        @if($employee->QR_code)
                                            <div class="border p-2 d-flex justify-content-center align-items-center" style="background:#f8f9fa;">
                                                <img src="{{ $employee->QR_code }}" alt="QR Code" style="width:150px; height:150px;">
                                            </div>
                                        @else
                                            <p class="text-dark">QR Code not generated</p>
                                        @endif
                                    </div>


                                </div>
                            </div>
                            <div class="modal-footer border-0">
                                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ✏️ Edit Employee Modal --}}
                <div class="modal fade" id="editEmployeeModal{{ $employee->employee_id }}" tabindex="-1" aria-labelledby="editEmployeeModalLabel{{ $employee->employee_id }}" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content border-0 rounded-4 shadow-lg">
                            <div class="modal-header bg-warning text-white border-0">
                                <h5 class="modal-title fw-semibold">Edit Employee Details</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="{{ route('employees.update', $employee->employee_id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Email</label>
                                            <input type="email" name="email" class="form-control" value="{{ $employee->email }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">First Name</label>
                                            <input type="text" name="first_name" class="form-control" value="{{ $employee->first_name }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Last Name</label>
                                            <input type="text" name="last_name" class="form-control" value="{{ $employee->last_name }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Phone</label>
                                            <input type="text" name="phone" class="form-control" value="{{ $employee->phone }}">
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label">Address</label>
                                            <input type="text" name="address" class="form-control" value="{{ $employee->address }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Age</label>
                                            <input type="number"  name="age" class="form-control" value="{{ $employee->age }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Basic Salary</label>
                                            <input type="number" step="0.01" name="basic_salary" class="form-control" value="{{ $employee->basic_salary }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Status</label>
                                            <select name="status" class="form-select" required>
                                                <option value="Active" {{ $employee->status == 'Active' ? 'selected' : '' }}>Active</option>
                                                <option value="Inactive" {{ $employee->status == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                                <option value="On Leave" {{ $employee->status == 'On Leave' ? 'selected' : '' }}>On Leave</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Employment Type</label>
                                            <select name="employment_type" class="form-select" required>
                                                <option value="Full-Time" {{ $employee->employment_type == 'Full-Time' ? 'selected' : '' }}>Full-Time</option>
                                                <option value="Part-Time" {{ $employee->employment_type == 'Part-Time' ? 'selected' : '' }}>Part-Time</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Position</label>
                                            <select name="position_id" class="form-select" required>
                                                <option value="" disabled>Select Position</option>
                                                @foreach($positions as $position)
                                                    <option value="{{ $position->position_id }}"
                                                        {{ isset($employee) && $employee->position_id == $position->position_id ? 'selected' : '' }}>
                                                        {{ $position->position_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer border-0">
                                    <button type="submit" class="btn btn-warning text-white px-4">Update</button>
                                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                @empty
                <tr>
                    <td colspan="10" class="text-center text-muted py-4">No employees found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Add Employee Modal --}}
    <div class="modal fade" id="addEmployeeModal" tabindex="-1" aria-labelledby="addEmployeeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title fw-semibold">Add New Employee</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('employees.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">First Name</label>
                                <input type="text" name="first_name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="last_name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Address</label>
                                <input type="text" name="address" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Birthday</label>
                                <input type="date" name="birthday" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Age</label>
                                <input type="number" name="age" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Hire Date</label>
                                <input type="date" name="hire_date" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Basic Salary</label>
                                <input type="number" step="0.01" name="basic_salary" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select" required>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                    <option value="On Leave">On Leave</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Employment Type</label>
                                <select name="employment_type" class="form-select" required>
                                    <option value="" disabled selected>Select Employment Type</option>
                                    <option value="Full-Time">Full-Time</option>
                                    <option value="Part-Time">Part-Time</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Position</label>
                                <select name="position_id" class="form-select" required>
                                    <option value="" disabled selected>Select Position</option>
                                    @foreach($positions as $position)
                                        <option value="{{ $position->position_id }}"
                                            {{ isset($employee) && $employee->position_id == $position->position_id ? 'selected' : '' }}>
                                            {{ $position->position_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="submit" class="btn btn-primary px-4">Save Employee</button>
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
