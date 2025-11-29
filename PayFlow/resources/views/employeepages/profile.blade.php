@extends('layouts.employeeapp')

@section('content')

<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-3">
        <!-- Dashboard Title -->
        <h3 class="fw-bold mb-0">Profile</h3>

        <div class="d-flex flex-wrap align-items-center gap-2">
            <!-- Current Date & Time -->
            <div class="d-flex align-items-center gap-2 px-2 px-md-3 py-1 rounded-3 bg-bold shadow-sm">
                <i class="bi bi-clock fs-5 text-secondary"></i>
                <span class="fw-medium" id="currentDateTime"></span>
            </div>
        </div>
    </div>
    <div class="row">
        <!-- LEFT PROFILE CARD -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div style="height: 120px;"></div>
                <div class="card-body text-center">
                <img src="{{ $employee->profile_picture && file_exists(storage_path('app/public/' . $employee->profile_picture)) 
                            ? asset('storage/' . $employee->profile_picture) 
                            : asset('images/default-profile.png') }}"
                    alt="Profile Picture"
                    class="rounded-circle border border-3 border-white shadow-sm position-absolute"
                    style="width:180px; height:170px; top:30px; left:50%; transform:translateX(-50%);">

                    <div class="mt-5 pt-5">
                        <h4 class="fw-bold">{{ $employee->full_name }}</h4>
                        <p class="text-muted mb-1">{{ $employee->position ? $employee->position->name : '-' }}</p>
                        <small class="text-muted">{{ $employee->email }}</small><br>
                        <span class="badge {{ $employee->status === 'Active' ? 'bg-success' : 'bg-secondary' }} px-3 py-2">{{ $employee->status }}</span>
                    </div>

                    <hr class="my-4">

                    <div class="text-start ps-3">
                        <p><strong>Employee No:</strong> {{ $employee->employee_no }}</p>
                        <p><strong>Employment Type:</strong> {{ $employee->employment_type }}</p>
                    </div>

                    <!-- QR CODE BELOW PROFILE INFO -->
                    <div class="col-md-12">
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
        </div>

        <!-- RIGHT DETAILS -->
        <div class="col-lg-8">
            <div class="row g-3">
                <!-- Email -->
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 rounded-4 p-3">
                        <h6 class="text-muted"><i class="bi bi-envelope me-2"></i>Email Address</h6>
                        <p class="fw-semibold mb-0">{{ $employee->email }}</p>
                        <small class="text-muted">Primary contact email</small>
                    </div>
                </div>

                <!-- Phone -->
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 rounded-4 p-3">
                        <h6 class="text-muted"><i class="bi bi-telephone me-2"></i>Phone Number</h6>
                        <p class="fw-semibold mb-0">{{ $employee->phone }}</p>
                        <small class="text-muted">Mobile contact</small>
                    </div>
                </div>

                <!-- Address -->
                <div class="col-md-12">
                    <div class="card shadow-sm border-0 rounded-4 p-3">
                        <h6 class="text-muted"><i class="bi bi-geo-alt me-2"></i>Address</h6>
                        <p class="fw-semibold mb-0">{{ $employee->address }}</p>
                        <small class="text-muted">Residential address</small>
                    </div>
                </div>

                <!-- Birthday -->
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 rounded-4 p-3">
                        <h6 class="text-muted"><i class="bi bi-calendar-event me-2"></i>Birthday</h6>
                        <p class="fw-semibold mb-0">{{ $employee->birthday ? $employee->birthday->format('F d, Y') : 'N/A' }}</p>
                        <small class="text-muted">{{ $employee->age ?? 'N/A' }} years old</small>
                    </div>
                </div>

                <!-- Hire Date -->
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 rounded-4 p-3">
                        <h6 class="text-muted"><i class="bi bi-briefcase me-2"></i>Hire Date</h6>
                        <p class="fw-semibold mb-0">{{ $employee->hire_date ? $employee->hire_date->format('F d, Y') : 'N/A' }}</p>
                        <small class="text-muted">{{ $employee->hire_date ? $employee->hire_date->age : 'N/A' }} years of service</small>
                    </div>
                </div>

                <!-- Salary & Position -->
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 rounded-4 p-3">
                        <h6 class="text-muted"><i class="bi bi-cash-stack me-2"></i>Basic Salary</h6>
                        <p class="fw-semibold mb-0">₱{{ number_format($employee->basic_salary, 2) }}</p>
                        <small class="text-muted">Annual base salary</small>
                        <small class="text-success">Position: {{ $employee->position->position_name ?? '-' }}</small>
                    </div>
                </div>

                <!-- Employee Details -->
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 rounded-4 p-3">
                        <h6 class="text-muted"><i class="bi bi-person-badge me-2"></i>Employee Details</h6>
                        <p class="fw-semibold mb-0">Employee No: {{ $employee->employee_no }} </p>
                        <p class="fw-semibold mb-0">Position: {{ $employee->position->position_name ?? '-' }}</p>
                        <small class="text-success">Status: {{ $employee->status }}</small>
                    </div>
                </div>
            </div>

            <!-- QUICK ACTIONS -->
            <div class="card shadow-sm border-0 rounded-4 mt-4 p-3">
                <h6 class="fw-bold mb-3">Quick Actions</h6>
                <div class="d-flex gap-2">
                    <a href="#" class="btn btn-outline-success flex-fill" data-bs-toggle="modal" data-bs-target="#editProfileModal">Edit Profile</a>
                    <a href="#" class="btn btn-outline-secondary flex-fill" data-bs-toggle="modal" data-bs-target="#downloadIDModal">Download ID</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Download ID Modal -->
<div class="modal fade" id="downloadIDModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Employee ID Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <!-- The ID Card (This will be downloaded as image) -->
                <div id="idCard" class="p-3 mx-auto" 
                     style="width: 300px; background: white; border: 2px solid #ddd; border-radius: 10px;">

                    <div class="text-center">
                        <img src="{{ $employee->profile_picture && file_exists(storage_path('app/public/' . $employee->profile_picture)) 
                                    ? asset('storage/' . $employee->profile_picture) 
                                    : asset('images/default-profile.png') }}"
                             class="rounded-circle mb-3"
                             style="width: 100px; height: 100px; border: 3px solid #0d6efd;">
                        
                        <h4 class="fw-bold mb-0">{{ $employee->full_name }}</h4>
                        <p class="text-muted mb-1">{{ $employee->position ? $employee->position->name : '-' }}</p>

                        <span><p class="fw-semibold mb-0">{{ $employee->position->position_name ?? '-' }}</p></span>
                    </div>

                    <hr>

                    <div class="text-center">
                        @if($employee->QR_code)
                            <img src="{{ $employee->QR_code }}" style="width: 140px; height: 140px;">
                        @else
                            <p>No QR code</p>
                        @endif
                    </div>
                </div>
                <!-- END ID CARD -->

            </div>

            <div class="modal-footer d-flex justify-content-between">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" id="downloadIDBtn">Download</button>
            </div>
        </div>
    </div>
</div>


<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 shadow-sm">
      <div class="modal-header">
        <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('employee.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
            <div class="text-center mb-3">
                <img id="profilePreview" 
                    src="{{ $employee->profile_picture && file_exists(storage_path('app/public/' . $employee->profile_picture)) 
                            ? asset('storage/' . $employee->profile_picture) 
                            : asset('images/default-profile.png') }}"
                    alt="Profile Picture"
                    class="rounded-circle"
                    style="width:120px; height:120px;">

            </div>

            <div class="mb-3">
                <label for="profile_picture" class="form-label">Profile Picture</label>
                <input type="file" name="profile_picture" class="form-control" onchange="previewImage(event)">
            </div>

            <div class="mb-3">
                <label for="first_name" class="form-label">First Name</label>
                <input type="text" name="first_name" value="{{ old('first_name', $employee->first_name) }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="last_name" class="form-label">Last Name</label>
                <input type="text" name="last_name" value="{{ old('last_name', $employee->last_name) }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">Phone Number</label>
                <input type="text" name="phone" value="{{ old('phone', $employee->phone) }}" class="form-control">
            </div>

            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea name="address" class="form-control">{{ old('address', $employee->address) }}</textarea>
            </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success w-100">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
document.getElementById('downloadIDBtn').addEventListener('click', function () {
    const card = document.getElementById('idCard');

    html2canvas(card).then(canvas => {
        const link = document.createElement('a');
        link.download = "Employee-ID-{{ $employee->employee_no }}.png";
        link.href = canvas.toDataURL();
        link.click();
    });
});
function previewImage(event) {
    const input = event.target;
    const reader = new FileReader();
    reader.onload = function() {
        document.getElementById('profilePreview').src = reader.result;
    }
    reader.readAsDataURL(input.files[0]);
}
</script>
@endsection
