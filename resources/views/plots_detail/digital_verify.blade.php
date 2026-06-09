<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Plot Specification - {{ $plot->plot_number }}</title>

<style>
body {
    font-family: DejaVu Sans, sans-serif;
    color: #333;
    margin: 0;
}

.container {
    padding: 25px 30px;
}

/* Header */
.header {
    border-bottom: 3px solid #1a5c20;
    margin-bottom: 20px;
    padding-bottom: 10px;
}

.header table {
    width: 100%;
}

.logo-text {
    font-size: 22px;
    font-weight: bold;
    color: #1a5c20;
}

.subtitle {
    font-size: 11px;
    color: #777;
}

/* Section Title */
.section-title {
    margin-top: 18px;
    padding: 6px 10px;
    font-size: 13px;
    font-weight: bold;
    color: #1a5c20;
    background: #f2f6f3;
    border-left: 5px solid #1a5c20;
}

/* Table Grid */
.info-grid {
    width: 100%;
    border-collapse: collapse;
    margin-top: 8px;
}

.info-grid td {
    width: 50%;
    padding: 8px 6px;
    border-bottom: 1px solid #eee;
    vertical-align: top;
}

.label {
    font-size: 10px;
    color: #777;
    text-transform: uppercase;
}

.value {
    font-size: 13px;
    font-weight: bold;
    color: #111;
}

/* Pricing Box */
.pricing-box {
    margin-top: 12px;
    border: 1px solid #e6e6e6;
    padding: 12px;
    border-radius: 6px;
    background: #fafafa;
}

.price-main {
    font-size: 16px;
    font-weight: bold;
    color: #1a5c20;
}

/* Description */
.description {
    font-size: 11px;
    padding: 10px;
    color: #444;
    text-align: justify;
    border: 1px solid #eee;
    margin-top: 8px;
}

/* Footer */
.footer {
    margin-top: 40px;
    font-size: 9px;
    color: #999;
    text-align: center;
    border-top: 1px solid #ddd;
    padding-top: 8px;
}

/* Prevent page breaks */
table {
    page-break-inside: avoid;
}
</style>
</head>

<body>
<div class="container">

    <!-- Header -->
    <div class="header">
        <table>
            <tr>
                <td>
                    <div class="logo-text">{{ $config['name'] ?? 'ZAMAR VALLEY' }}</div>
                    <div class="subtitle">Official Property Specification Sheet</div>
                </td>
                <td style="text-align:right; font-size:11px;">
                    <strong>Plot ID:</strong> #{{ $plot->plot_number }}<br>
                    <strong>Date:</strong> {{ now()->format('d M Y') }}
                </td>
            </tr>
        </table>
    </div>

    <!-- Property Details -->
    <div class="section-title">Property Details</div>
    <table class="info-grid">

        <tr>
            <td>
                <div class="label">Plot Number</div>
                <div class="value">#{{ $plot->plot_number }}</div>
            </td>
            <td>
                <div class="label">Block / Sector</div>
                <div class="value">
                    {{ $plot->block }}
                    @if($plot->sector) ({{ $plot->sector }}) @endif
                </div>
            </td>
        </tr>

        <tr>
            <td>
                <div class="label">Category</div>
                <div class="value">{{ $plot->category->name ?? 'Residential' }}</div>
            </td>
            <td>
                <div class="label">Street</div>
                <div class="value">St #{{ $plot->street_number }} ({{ $plot->street_size }} ft)</div>
            </td>
        </tr>

        <tr>
            <td>
                <div class="label">Location</div>
                <div class="value">{{ $plot->society }}, {{ $plot->city }}</div>
            </td>
            <td>
                <div class="label">Size</div>
                <div class="value">{{ $plot->size }} {{ $plot->unit }}</div>
            </td>
        </tr>

    </table>

    <!-- Pricing -->
    <div class="section-title">Pricing Plan</div>
    <div class="pricing-box">

        <div style="margin-bottom:10px;">
            <div class="label">Total Price</div>
            <div class="price-main">PKR {{ number_format($plot->base_price) }}</div>
        </div>

        <div style="margin-bottom:10px;">
            <div class="label">Down Payment</div>
            <div class="value">PKR {{ number_format($plot->down_payment) }}</div>
        </div>

        <div>
            <div class="label">Monthly Installment</div>
            <div class="value">PKR {{ number_format($plot->installment_amount) }}</div>
        </div>

    </div>

    <!-- Installments -->
    <table class="info-grid">

        <tr>
            <td>
                <div class="label">Total Installments</div>
                <div class="value">{{ $plot->total_installments }} Months</div>
            </td>
            <td>
                <div class="label">Quarterly Payment</div>
                <div class="value">PKR {{ number_format($plot->quarterly_amount) }}</div>
            </td>
        </tr>

        <tr>
            <td colspan="2">
                <div class="label">Price Type</div>
                <div class="value">{{ ucwords($plot->price_type) }}</div>
            </td>
        </tr>

    </table>

    <!-- Description -->
    @if($plot->description)
    <div class="section-title">Description</div>
    <div class="description">
        {{ $plot->description }}
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        Generated on {{ now()->format('d M, Y h:i A') }} |
        Ref: {{ strtoupper(substr(md5($plot->id), 0, 10)) }} <br>
        Computer generated document — no signature required.
    </div>

</div>
</body>
</html>
