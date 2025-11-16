<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>@yield('title','Dashboard') — PayFlow</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/applayout.css') }}">
</head>
<body>
<div class="container text-center py-5">
    <h2 class="fw-bold mb-3">QR Attendance Scanner</h2>
    <p class="text-muted">Scan your QR — attendance will be recorded automatically.</p>

    <div id="reader" style="width: 350px; margin: 0 auto;"></div>
    <div id="status" class="mt-4 fs-5 fw-semibold text-muted">Ready for scan...</div>

    <input type="text" id="barcodeInput" autofocus style="opacity:0; position:absolute; left:-9999px;">
</div>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    const status = document.getElementById("status");
    const barcodeInput = document.getElementById("barcodeInput");
    let canScan = true;
    let barcodeBuffer = "";
    let typingTimer;

    function handleScan(decodedText) {
        if (!canScan) return;
        canScan = false;

        fetch("{{ route('attendance.scanner.store') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "X-Requested-With": "XMLHttpRequest",
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ qr_data: decodedText.trim() })
        })
        .then(res => res.json())
        .then(data => {
            status.textContent = data.message;
            status.style.color = data.status === "success" ? "green" : "red";
        })
        .catch(() => {
            status.textContent = "⚠️ Error recording attendance";
            status.style.color = "red";
        })
        .finally(() => {
            setTimeout(() => {
                canScan = true;
                status.textContent = "Ready for next scan...";
                status.style.color = "gray";
                barcodeInput.value = "";
                barcodeInput.focus();
            }, 3000);
        });
    }

    // Camera QR scanner
    const html5QrcodeScanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: 250 });
    html5QrcodeScanner.render((decodedText) => handleScan(decodedText));

    // Bluetooth/Wired scanner support
    document.addEventListener("keydown", (e) => {
        barcodeInput.focus();

        if (e.key === "Enter" && barcodeBuffer.length > 0) {
            handleScan(barcodeBuffer);
            barcodeBuffer = "";
        } else if (e.key.length === 1) {
            barcodeBuffer += e.key;
            clearTimeout(typingTimer);
            typingTimer = setTimeout(() => barcodeBuffer = "", 500);
        }
    });
});
</script>
</body>
</html>
