<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Portfolio | Zamar Valley</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background-color: #0f172a; margin: 0; padding: 15px; color: #fff; display: flex; flex-direction: column; align-items: center; min-height: 100vh; }

        .portfolio-container { width: 100%; max-width: 450px; }

        /* Profile Header */
        .profile-card { background: white; color: #1e293b; border-radius: 24px; overflow: hidden; margin-bottom: 20px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.3); text-align: center; padding-bottom: 20px; }
        .card-header-bg { background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%); height: 100px; }
        .avatar { width: 100px; height: 100px; background: #f1f5f9; border-radius: 50%; margin: -50px auto 10px; border: 4px solid white; overflow: hidden; display: flex; align-items: center; justify-content: center; font-size: 40px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .avatar img { width: 100%; height: 100%; object-fit: cover; }

        /* Plot Cards */
        .plot-card { background: #ffffff; color: #1e293b; border-radius: 20px; margin-bottom: 20px; overflow: hidden; border: 1px solid #e2e8f0; }
        .plot-header { background: #f8fafc; padding: 15px 20px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; }
        .plot-title { font-weight: 800; font-size: 16px; color: #1e3a8a; }

        /* Status Badges */
        .status-pill { padding: 4px 10px; border-radius: 20px; font-size: 10px; font-weight: 800; text-transform: uppercase; }
        .status-active { background: #dcfce7; color: #166534; }
        .status-transferred { background: #f3e8ff; color: #6b21a8; }
        .status-cancelled { background: #fee2e2; color: #991b1b; }

        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1px; background: #f1f5f9; }
        .info-item { background: white; padding: 12px 20px; }
        .label { font-size: 10px; color: #64748b; text-transform: uppercase; font-weight: 700; display: block; margin-bottom: 2px; }
        .value { font-size: 14px; font-weight: 700; color: #0f172a; }

        /* Financial Highlight */
        .equity-section { padding: 20px; background: #eff6ff; }
        .progress-bar { background: #dbeafe; height: 8px; border-radius: 10px; margin: 10px 0; overflow: hidden; }
        .progress-fill { background: #2563eb; height: 100%; border-radius: 10px; }

        .history-btn { width: 100%; padding: 12px; background: #f8fafc; border: none; font-size: 12px; font-weight: 700; color: #2563eb; cursor: pointer; border-top: 1px solid #f1f5f9; }
        .history-panel { display: none; padding: 10px 20px; background: white; }

        .footer { text-align: center; font-size: 11px; opacity: 0.6; margin-top: 20px; padding-bottom: 40px; color: #94a3b8; line-height: 1.5; }
    </style>
</head>
<body>

 <div class="portfolio-container">

    <div class="profile-card">
        <div class="card-header-bg"></div>
        <div class="avatar">
            @if($customer->customer_pic)
                <img src="{{ asset($customer->customer_pic) }}" alt="Owner">
            @else 👤 @endif
        </div>
        <h2 style="margin:0; font-size: 20px;">{{ $customer->name }}</h2>
        <p style="margin:5px 0; font-size: 13px; color: #64748b;">CNIC: {{ $customer->cnic }}</p>
        <div style="margin-top: 10px;">
            <span class="status-pill status-active">
                Verified Portfolio: {{ $customer->booking->count() }} Plots
            </span>
        </div>
    </div>

    <h3 style="font-size: 14px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; padding-left: 5px;">Investment Portfolio</h3>

    @forelse($customer->booking as $b)
        @php
            $totalPaid = $b->payments->where('status', 'paid')->sum('amount_paid');
            $percent = $b->total_price > 0 ? min(($totalPaid / $b->total_price) * 100, 100) : 0;
            $instCount = $b->payments->where('payment_category', 'installment')->where('status', 'paid')->count();
        @endphp

        <div class="plot-card">
            <div class="plot-header">
                <span class="plot-title">Plot #{{ $b->plot->plot_number }} ({{ $b->plot->block }})</span>
                <span class="status-pill status-{{ $b->status }}">{{ str_replace('_', ' ', $b->status) }}</span>
            </div>

            <div class="info-grid">
                <div class="info-item"><span class="label">Size</span><span class="value">{{ $b->plot->size }} {{ $b->plot->unit }}</span></div>
                <div class="info-item"><span class="label">Category</span><span class="value">{{ $b->plot->category->name ?? 'Residential' }}</span></div>
                <div class="info-item"><span class="label">Booking ID</span><span class="value">{{ $b->customer_booking_id }}</span></div>
                <div class="info-item"><span class="label">Installments</span><span class="value">{{ $instCount }} / {{ $b->total_installments }}</span></div>
            </div>

            <div class="equity-section">
                <div style="display: flex; justify-content: space-between;">
                    <span class="label">Current Equity</span>
                    <span class="label" style="color: #1e40af;">PKR {{ number_format($totalPaid) }}</span>
                </div>
                <div class="progress-bar"><div class="progress-fill" style="width: {{ $percent }}%;"></div></div>
                <div style="display: flex; justify-content: space-between; font-size: 11px; font-weight: 700;">
                    <span style="color: #2563eb;">{{ number_format($percent, 1) }}% Paid</span>
                    <span style="color: #64748b;">Rem: {{ number_format($b->total_price - $totalPaid) }}</span>
                </div>
            </div>

            <button class="history-btn" onclick="toggleHistory('{{ $b->id }}')">VIEW RECENT PAYMENTS ↓</button>
            <div id="history-{{ $b->id }}" class="history-panel">
                @foreach($b->payments->where('status', 'paid')->take(5) as $p)
                <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-size: 12px;">
                    <span>{{ ucwords(str_replace('_', ' ', $p->payment_category)) }}<br><small style="color: #94a3b8;">{{ date('d M, Y', strtotime($p->paid_date)) }}</small></span>
                    <span style="font-weight: 700; color: #166534;">+ {{ number_format($p->amount_paid) }}</span>
                </div>
                @endforeach
            </div>
        </div>
    @empty
        <div style="text-align: center; padding: 20px; color: #94a3b8; background: rgba(255,255,255,0.05); border-radius: 20px;">
            No active bookings found for this customer.
        </div>
    @endforelse

    <div class="footer">
        <strong>Zamar Valley Real Estate ERP</strong><br>
        Secure Digital Verification System<br>
        Validated: {{ now()->format('d M, Y h:i A') }}
    </div>
</div>

    <script>
        function toggleHistory(id) {
            var x = document.getElementById("history-" + id);
            x.style.display = (x.style.display === "block") ? "none" : "block";
        }
    </script>
</body>
</html>
