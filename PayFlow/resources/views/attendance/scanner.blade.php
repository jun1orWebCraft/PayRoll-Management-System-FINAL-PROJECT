<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>@yield('title','Dashboard') — PayFlow</title>

  {{-- ✅ Bootstrap CSS --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">


  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <link rel="stylesheet" href="{{ asset('css/applayout.css') }}">

  <style>
/* 🌙 Improved Dark Mode Readability */
body.dark-mode {
    background-color: #0f0f0f !important;
    color: #f1f1f1 !important;
}

/* Cards, forms, and modals */
body.dark-mode .card,
body.dark-mode .modal-content {
    background-color: #1a1a1a !important;
    color: #ffffff !important;
    border-color: #2c2c2c !important;
}

/* Labels, headings, and muted text */
body.dark-mode label,
body.dark-mode h1,
body.dark-mode h2,
body.dark-mode h3,
body.dark-mode h4,
body.dark-mode h5,
body.dark-mode h6 {
    color: #f1f1f1 !important;
}

body.dark-mode .text-muted,
body.dark-mode small {
    color: #bbbbbb !important;
}

/* Input fields and dropdowns */
body.dark-mode .form-control,
body.dark-mode .form-select {
    background-color: #2a2a2a !important;
    color: #ffffff !important;
    border-color: #444 !important;
}

body.dark-mode .form-control::placeholder {
    color: #aaaaaa !important;
}

/* Buttons */
body.dark-mode .btn-primary {
    background-color: #0d6efd !important;
    border-color: #0d6efd !important;
    color: #fff !important;
}

body.dark-mode .btn-success {
    background-color: #198754 !important;
    border-color: #198754 !important;
    color: #fff !important;
}

/* Switch toggles */
body.dark-mode .form-check-input:checked {
    background-color: #0d6efd !important;
    border-color: #0d6efd !important;
}

/* Dropdowns and selects */
body.dark-mode option {
    background-color: #1a1a1a;
    color: #f1f1f1;
}

/* Input focus state */
body.dark-mode .form-control:focus {
    border-color: #0d6efd !important;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
}

/* Card header or title contrast */
body.dark-mode .card h5,
body.dark-mode .card-title {
    color: #ffffff !important;
}

/* ✅ FIX: Prevent Sidebar from Scrolling */
.layout {
    display: flex;
    height: 100vh;
    overflow: hidden;
}

.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: 250px;
    height: 100vh;
    overflow-y: auto;
    background-color: #0b1d39;
    z-index: 1000;
}

main {
    margin-left: 250px;
    flex-grow: 1;
    height: 100vh;
    overflow-y: auto;
    background-color: #f8f9fa;
}

.sidebar::-webkit-scrollbar {
    width: 6px;
}
.sidebar::-webkit-scrollbar-thumb {
    background-color: rgba(255, 255, 255, 0.3);
    border-radius: 4px;
}
.sidebar::-webkit-scrollbar-thumb:hover {
    background-color: rgba(255, 255, 255, 0.5);
}
  </style>

</head>
<body>
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
</body>
</html>
