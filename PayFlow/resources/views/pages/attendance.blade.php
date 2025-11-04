@extends('layouts.app')

@section('title', 'Attendance Management')

@section('content')
<div class="container py-3">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">Attendances</h2>
            <p class="text-muted mb-0">Manage and track all Employee Attendance</p>  
        </div>
    </div>

    <form method="GET" action="{{ route('attendance.index') }}">
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control shadow-sm" placeholder="Search employees..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select shadow-sm">
                    <option value="">All Status</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="employment_type" class="form-select shadow-sm">
                </select>
            </div>
        </div>
    </form>

    {{-- Attendance Table --}}
    <div class="table-responsive bg-white rounded-4 p-3 shadow-sm border">
        <table class="table table-hover align-middle text-center mb-0">
            <thead class="table-primary">
                <tr>
                    
                    <th>Employee No</th>
                    <th>Employee Name</th>
                    <th>Time In</th> 
                    <th>Time Out</th>
                    <th>Total Work Hour</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($attendances as $attendance)
                <tr>
                    
                    <td>{{ $attendance->employee->employee_no ?? '-' }}</td>
                    <td class="fw-semibold">{{ $attendance->employee->first_name ?? '' }} {{ $attendance->employee->last_name ?? '' }}</td>
                    <td>{{ $attendance->time_in ? \Carbon\Carbon::parse($attendance->time_in)->format('h:i A') : '-' }}</td>
                    <td>{{ $attendance->time_out ? \Carbon\Carbon::parse($attendance->time_out)->format('h:i A') : '-' }}</td>
                    <td>{{ $attendance->total_hours }}</td>
                    <td>
                        @php
                            $status = $attendance->time_in ? ($attendance->time_out ? 'Present' : 'Late') : 'Absent';
                        @endphp
                        @if ($status == 'Present')
                            <span class="badge bg-success">Present</span>
                        @elseif ($status == 'Late')
                            <span class="badge bg-warning text-dark">Late</span>
                        @else
                            <span class="badge bg-secondary">Absent</span>
                        @endif
                    </td>
                    <td>
                        {{-- View --}}
                        <button class="btn btn-link text-primary p-0 border-0 shadow-none me-2" 
                                data-bs-toggle="modal" 
                                data-bs-target="#employeeDetailsModal{{ $attendance->employee->id ?? '' }}">
                            <i class="bi bi-eye fs-5"></i>
                        </button>

                        {{-- Edit --}}
                        <button class="btn btn-link text-warning p-0 border-0 shadow-none me-2"
                                data-bs-toggle="modal"
                                data-bs-target="#editAttendanceModal{{ $attendance->id }}">
                            <i class="bi bi-pencil fs-5"></i>
                        </button>

                        {{-- Delete --}}
                        <form action="{{ route('attendance.destroy', $attendance->attendance_id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-link text-danger p-0" onclick="return confirm('Delete this attendance?')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">No attendance records found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
