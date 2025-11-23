@extends('layouts.app')

@section('title', 'Employee Scheduling')

@section('content')
<div class="container py-3">

    {{--Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">Employee Schedules</h2>
            <p class="text-muted mb-0">Manage and track employee weekly schedules</p>
        </div>
        <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addScheduleModal">
            <i class="bi bi-plus-lg"></i> Add Schedule
        </button>
    </div>

    {{--Employee Schedule Table --}}
    <div class="table-responsive bg-white rounded-4 p-3 shadow-sm border">
        <table class="table table-hover align-middle text-center mb-0">
            <thead class="table-primary">
                <tr>
                    <th>Employee</th>
                    <th>Monday</th>
                    <th>Tuesday</th>
                    <th>Wednesday</th>
                    <th>Thursday</th>
                    <th>Friday</th>
                    <th>Saturday</th>
                    <th>Sunday</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($schedules as $schedule)
                <tr>
                    <td>{{ $schedule->employee->first_name }} {{ $schedule->employee->last_name }}</td>
                    <td>{{ $schedule->monday ?? '-' }}</td>
                    <td>{{ $schedule->tuesday ?? '-' }}</td>
                    <td>{{ $schedule->wednesday ?? '-' }}</td>
                    <td>{{ $schedule->thursday ?? '-' }}</td>
                    <td>{{ $schedule->friday ?? '-' }}</td>
                    <td>{{ $schedule->saturday ?? '-' }}</td>
                    <td>{{ $schedule->sunday ?? '-' }}</td>
                    <td>
                        {{--View --}}
                        <button class="btn btn-link text-primary p-0 border-0 shadow-none me-2" 
                                data-bs-toggle="modal" 
                                data-bs-target="#viewScheduleModal{{ $schedule->id }}">
                            <i class="bi bi-eye fs-5"></i>
                        </button>

                        <button class="btn btn-link text-warning p-0 border-0 shadow-none me-2"
                                data-bs-toggle="modal"
                                data-bs-target="#editScheduleModal{{ $schedule->id }}">
                            <i class="bi bi-pencil fs-5"></i>
                        </button>

                        {{--Delete --}}
                        <form action="{{ route('employeeschedule.destroy', ['employeeschedule' => $schedule->id]) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-link text-danger p-0" onclick="return confirm('Delete this schedule?')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>

                    </td>
                </tr>

                {{--View Schedule Modal --}}
                <div class="modal fade" id="viewScheduleModal{{ $schedule->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 rounded-4 shadow-lg">
                            <div class="modal-header bg-success text-white border-0">
                                <h5 class="modal-title fw-semibold">Schedule for {{ $schedule->employee->first_name }} {{ $schedule->employee->last_name }}</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <ul class="list-group list-group-flush text-start">
                                    <li class="list-group-item"><strong>Monday: </strong> {{ $schedule->monday ?? '-' }}</li>
                                    <li class="list-group-item"><strong>Tuesday: </strong> {{ $schedule->tuesday ?? '-' }}</li>
                                    <li class="list-group-item"><strong>Wednesday: </strong> {{ $schedule->wednesday ?? '-' }}</li>
                                    <li class="list-group-item"><strong>Thursday: </strong> {{ $schedule->thursday ?? '-' }}</li>
                                    <li class="list-group-item"><strong>Friday: </strong> {{ $schedule->friday ?? '-' }}</li>
                                    <li class="list-group-item"><strong>Saturday: </strong> {{ $schedule->saturday ?? '-' }}</li>
                                    <li class="list-group-item"><strong>Sunday: </strong> {{ $schedule->sunday ?? '-' }}</li>
                                </ul>
                            </div>
                            <div class="modal-footer border-0">
                                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                {{--Edit Schedule Modal --}}
                <div class="modal fade" id="editScheduleModal{{ $schedule->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 rounded-4 shadow-lg">
                            <div class="modal-header bg-warning text-white border-0">
                                <h5 class="modal-title fw-semibold">Edit Schedule</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="{{ route('employeeschedule.update', ['employeeschedule' => $schedule->id]) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Monday</label>
                                            <input type="text" name="monday" class="form-control" value="{{ $schedule->monday }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Tuesday</label>
                                            <input type="text" name="tuesday" class="form-control" value="{{ $schedule->tuesday }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Wednesday</label>
                                            <input type="text" name="wednesday" class="form-control" value="{{ $schedule->wednesday }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Thursday</label>
                                            <input type="text" name="thursday" class="form-control" value="{{ $schedule->thursday }}" >
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Friday</label>
                                            <input type="text" name="friday" class="form-control" value="{{ $schedule->friday }}" >
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Saturday</label>
                                            <input type="text" name="saturday" class="form-control" value="{{ $schedule->saturday }}" >
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Sunday</label>
                                            <input type="text" name="sunday" class="form-control" value="{{ $schedule->sunday }}" >
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
                    <td colspan="9" class="text-center text-muted py-4">No schedules found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{--Add Schedule Modal --}}
    <div class="modal fade" id="addScheduleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title fw-semibold">Add Employee Schedule</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('employeeschedule.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <label class="text-success">Time Format Example: 07:00-12:00 / 13:00-17:00 (morning/afternoon)</label>
                        <p>Note: Use 24-hour format and separate multiple time ranges with a slash (/). Keep the 00:00 when the part-time has no schedule for that time period morning or afternoon.</p>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Employee</label>
                                <select name="employee_id" class="form-select" required>
                                    <option value="" disabled selected>Select Employee</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->employee_id }}">
                                            {{ $employee->first_name }} {{ $employee->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Monday</label>
                                <input type="text" name="monday" class="form-control" >
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tuesday</label>
                                <input type="text" name="tuesday" class="form-control" >
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Wednesday</label>
                                <input type="text" name="wednesday" class="form-control" >
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Thursday</label>
                                <input type="text" name="thursday" class="form-control" >
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Friday</label>
                                <input type="text" name="friday" class="form-control" >
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Saturday</label>
                                <input type="text" name="saturday" class="form-control" >
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Sunday</label>
                                <input type="text" name="sunday" class="form-control" >
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="submit" class="btn btn-primary px-4">Save Schedule</button>
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
