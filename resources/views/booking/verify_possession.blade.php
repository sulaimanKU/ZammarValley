<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verification | {{ $sc['name'] ?? 'Society Management' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root { --navy: #1e3a8a; --gold: #fbbf24; }
        body { background: #f1f5f9; padding-top: 30px; font-family: 'Segoe UI', Roboto, sans-serif; }
        .verify-card {
            border-radius: 20px;
            border: none;
            box-shadow: 0 15px 35px rgba(30, 58, 138, 0.15);
            max-width: 500px;
            margin: auto;
            overflow: hidden;
        }
        .card-accent { height: 6px; background: linear-gradient(90deg, var(--navy), var(--gold)); }
        .status-icon { font-size: 3.5rem; }
        .table th { color: #64748b; font-weight: 600; font-size: 0.85rem; text-transform: uppercase; width: 35%; }
        .table td { color: #1e293b; font-weight: 700; }
        .society-logo { max-height: 90px; width: auto; object-fit: contain; }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="card verify-card">
            <div class="card-accent"></div>
            <div class="card-body text-center p-4">

               <div class="mb-3 text-center">
  <div class="mb-3 text-center">

                <h4 class="fw-bold mb-1" style="color: var(--navy);">{{ $sc['name'] ?? 'Society Management' }}</h4>
                <p class="text-muted small text-uppercase tracking-wider">Official Possession Verification</p>

                <hr class="my-4" style="opacity: 0.1;">

                @if($booking->status === 'completed' && $dues['cleared'])
                    <div class="status-icon text-success mb-2">
                        <i class="bi bi-patch-check-fill"></i>
                    </div>
                    <div class="alert alert-success border-0 py-3" style="border-radius: 12px;">
                        <h5 class="fw-bold mb-0">VALID POSSESSION</h5>
                    </div>
                @else
                    <div class="status-icon text-danger mb-2">
                        <i class="bi bi-exclamation-octagon-fill"></i>
                    </div>
                    <div class="alert alert-danger border-0 py-3" style="border-radius: 12px;">
                        <h5 class="fw-bold mb-0">INVALID OR PENDING</h5>
                    </div>
                @endif

                <table class="table table-borderless text-start mt-4">
                    <tr><th>Ref No:</th><td>{{ $booking->customer_booking_id }}</td></tr>
                    <tr><th>Customer:</th><td>{{ $booking->customer->name }}</td></tr>
                    <tr><th>Plot:</th><td>#{{ $booking->plot->plot_number }} (Block {{ $booking->plot->block }})</td></tr>
                    <tr>
                        <th>Finances:</th>
                        <td>
                            @if($dues['cleared'])
                                <span class="badge bg-success px-3">ALL DUES CLEARED</span>
                            @else
                                <span class="badge bg-danger px-3">OUTSTANDING: PKR {{ number_format($dues['outstanding']) }}</span>
                            @endif
                        </td>
                    </tr>
                </table>

                <div class="mt-4 pt-4 border-top">
                    <p class="small text-muted mb-1">{{ $sc['address'] ?? '' }}</p>
                    @if(!empty($sc['phone']))
                        <p class="small fw-bold mb-0" style="color: var(--navy);">
                            <i class="bi bi-telephone-fill me-1"></i> {{ $sc['phone'] }}
                        </p>
                    @endif
                    <p class="x-small text-muted mt-3" style="font-size: 0.75rem;">
                        Verified via Secure QR on {{ now()->format('d-M-Y h:i A') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
