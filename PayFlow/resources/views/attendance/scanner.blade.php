@extends('layouts.app')

@section('content')
<div class="container text-center py-5">
    <h2 class="fw-bold mb-3">QR Attendance Scanner</h2>
    <p class="text-muted">Use your camera to scan your QR code — attendance will be recorded automatically.</p>

    {{-- Camera Scanner --}}
    <div id="reader" style="width: 350px; margin: 0 auto;"></div>

    <div id="status" class="mt-4 fs-5 fw-semibold"></div>
</div>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    const status = document.getElementById("status");

    function onScanSuccess(decodedText, decodedResult) {
        // Send scanned QR code to Laravel via AJAX
        fetch("{{ route('attendance.store') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "X-Requested-With": "XMLHttpRequest",
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ qr_data: decodedText })
        })
        .then(res => res.json())
        .then(data => {
            status.textContent = data.message;
            status.style.color = data.status === "success" ? "green" : "red";
        })
        .catch(() => {
            status.textContent = "⚠️ Error recording attendance";
            status.style.color = "red";
        });
    }

    // Initialize camera
    const html5QrcodeScanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: 250 });
    html5QrcodeScanner.render(onScanSuccess);
});
</script>
@endsection
