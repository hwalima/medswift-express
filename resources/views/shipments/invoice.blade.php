<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice {{ $invoiceNumber }} — MedSwift Express</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; color: #1f2937; background: #fff; font-size: 13px; line-height: 1.5; }
        .page { max-width: 800px; margin: 0 auto; padding: 40px; }

        /* Header */
        .header { display: flex; justify-content: space-between; align-items: flex-start; padding-bottom: 24px; border-bottom: 3px solid #1697a9; }
        .brand-name { font-size: 22px; font-weight: 700; color: #1697a9; }
        .brand-sub  { font-size: 12px; color: #98aeb1; margin-top: 2px; }
        .invoice-title { text-align: right; }
        .invoice-title h1 { font-size: 28px; font-weight: 700; color: #1697a9; text-transform: uppercase; letter-spacing: 2px; }
        .invoice-title p  { font-size: 12px; color: #6b7280; margin-top: 2px; }

        /* Meta grid */
        .meta { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin: 28px 0; }
        .meta-block h3 { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #98aeb1; margin-bottom: 6px; }
        .meta-block p  { color: #374151; font-size: 13px; }
        .meta-block .highlight { font-weight: 600; color: #1697a9; }

        /* Shipment details */
        .section-title { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #98aeb1; margin-bottom: 10px; margin-top: 24px; }
        .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; background: #f9fafb; border-radius: 8px; padding: 16px; margin-bottom: 24px; }
        .detail-item label { font-size: 10px; font-weight: 600; text-transform: uppercase; color: #98aeb1; display: block; }
        .detail-item span  { font-size: 13px; color: #374151; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 999px; font-size: 11px; font-weight: 600; }
        .badge-urgent { background: #fee2e2; color: #b91c1c; }
        .badge-biohazard { background: #fef3c7; color: #b45309; }
        .badge-delivered { background: #d1fae5; color: #065f46; }
        .badge-pending   { background: #e5e7eb; color: #374151; }

        /* Line items table */
        table { width: 100%; border-collapse: collapse; }
        thead th { background: #1697a9; color: #fff; padding: 10px 14px; text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; }
        thead th:last-child { text-align: right; }
        tbody td { padding: 10px 14px; border-bottom: 1px solid #f3f4f6; }
        tbody td:last-child { text-align: right; }
        tbody tr:nth-child(even) { background: #f9fafb; }
        tfoot td { padding: 10px 14px; font-weight: 600; }
        .total-row { background: #1697a9 !important; color: #fff; font-size: 14px; }
        .total-row td { border: none; }

        /* Compliance note */
        .compliance { background: #eff9ff; border-left: 4px solid #1697a9; padding: 12px 16px; border-radius: 0 8px 8px 0; margin: 24px 0; font-size: 12px; color: #0f5474; }
        .compliance strong { display: block; margin-bottom: 4px; }

        /* Footer */
        .footer { text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px solid #e5e7eb; font-size: 11px; color: #9ca3af; }
        .footer a { color: #1697a9; text-decoration: none; }

        /* Print styles */
        @media print {
            body { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
            .no-print { display: none !important; }
            .page { padding: 20px; }
        }
    </style>
</head>
<body>
<div class="page">

    {{-- Print / Download buttons --}}
    <div class="no-print" style="display:flex; gap:10px; margin-bottom:24px;">
        <button onclick="window.print()"
                style="background:#1697a9;color:#fff;border:none;padding:8px 18px;border-radius:8px;cursor:pointer;font-size:13px;font-weight:600;">
            🖨 Print / Save PDF
        </button>
        <a href="{{ url()->previous() }}"
           style="background:#f3f4f6;color:#374151;border:none;padding:8px 18px;border-radius:8px;cursor:pointer;font-size:13px;font-weight:600;text-decoration:none;display:inline-block;">
            ← Back
        </a>
    </div>

    {{-- Header --}}
    <div class="header">
        <div>
            <div class="brand-name">MedSwift Express</div>
            <div class="brand-sub">Medical Courier &amp; Logistics · medswift.express</div>
        </div>
        <div class="invoice-title">
            <h1>Invoice</h1>
            <p># {{ $invoiceNumber }}</p>
            <p>Date: {{ now()->format('d F Y') }}</p>
        </div>
    </div>

    {{-- Bill To / Invoice Details --}}
    <div class="meta">
        <div class="meta-block">
            <h3>Bill To</h3>
            <p><strong>{{ $shipment->client?->name }}</strong></p>
            @if ($shipment->client?->organisation)
                <p>{{ $shipment->client->organisation }}</p>
            @endif
            <p>{{ $shipment->client?->email }}</p>
            @if ($shipment->client?->phone)
                <p>{{ $shipment->client->phone }}</p>
            @endif
        </div>
        <div class="meta-block">
            <h3>Payment Details</h3>
            <p><span class="highlight">{{ $invoiceNumber }}</span></p>
            <p>Invoice Date: {{ now()->format('d M Y') }}</p>
            <p>Due Date: {{ now()->addDays(30)->format('d M Y') }}</p>
            <p>Currency: ZAR (South African Rand)</p>
        </div>
    </div>

    {{-- Shipment details --}}
    <div class="section-title">Shipment Details</div>
    <div class="detail-grid">
        <div class="detail-item">
            <label>Tracking Number</label>
            <span class="highlight">{{ $shipment->tracking_number }}</span>
        </div>
        <div class="detail-item">
            <label>Status</label>
            <span>
                @php
                    $badgeClass = match($shipment->current_status) {
                        'delivered' => 'badge-delivered',
                        'pending'   => 'badge-pending',
                        default     => '',
                    };
                @endphp
                <span class="badge {{ $badgeClass }}">{{ $shipment->statusLabel() }}</span>
                @if ($shipment->priority === 'urgent')
                    <span class="badge badge-urgent ml-1">Urgent</span>
                @endif
                @if ($shipment->is_biohazard)
                    <span class="badge badge-biohazard ml-1">☣ Biohazard</span>
                @endif
            </span>
        </div>
        <div class="detail-item">
            <label>Origin</label>
            <span>{{ $shipment->origin_address }}</span>
        </div>
        <div class="detail-item">
            <label>Destination</label>
            <span>{{ $shipment->destination_address }}</span>
        </div>
        <div class="detail-item">
            <label>Temperature Class</label>
            <span style="text-transform: capitalize;">{{ $shipment->temperature_class }}</span>
        </div>
        <div class="detail-item">
            <label>Courier</label>
            <span>{{ $shipment->courier?->name ?? 'Not assigned' }}</span>
        </div>
        @if ($shipment->picked_up_at)
            <div class="detail-item">
                <label>Picked Up</label>
                <span>{{ $shipment->picked_up_at->format('d M Y, H:i') }}</span>
            </div>
        @endif
        @if ($shipment->delivered_at)
            <div class="detail-item">
                <label>Delivered</label>
                <span>{{ $shipment->delivered_at->format('d M Y, H:i') }}</span>
            </div>
        @endif
    </div>

    {{-- Line items --}}
    <div class="section-title">Services Rendered</div>
    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th style="text-align:right;">Amount (ZAR)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($lineItems as $item)
                <tr>
                    <td>{{ $item['description'] }}</td>
                    <td>R {{ number_format($item['amount'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td style="text-align:right; color:#6b7280; font-weight:400; font-size:12px;">Subtotal (excl. VAT)</td>
                <td style="text-align:right;">R {{ number_format($subtotalExclVat, 2) }}</td>
            </tr>
            <tr>
                <td style="text-align:right; color:#6b7280; font-weight:400; font-size:12px;">VAT (15%)</td>
                <td style="text-align:right;">R {{ number_format($vat, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td style="text-align:right;">Total Due (ZAR)</td>
                <td>R {{ number_format($total, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- Compliance note --}}
    @if ($shipment->temperature_class !== 'ambient' || $shipment->is_biohazard)
        <div class="compliance">
            <strong>⚠ Cold-Chain &amp; Compliance Notice</strong>
            @if ($shipment->temperature_class === 'frozen')
                This shipment required frozen chain management (≤ −20 °C). All handling followed WHO/SANS 10235 standards for cryogenic specimen transport.
            @elseif ($shipment->temperature_class === 'refrigerated')
                This shipment was transported under refrigerated conditions (2–8 °C) in compliance with SANS/ISO medical specimen transit guidelines.
            @endif
            @if ($shipment->is_biohazard)
                Biohazardous materials were handled under Category B UN 3373 packaging and IATA P650 compliance protocols.
            @endif
        </div>
    @endif

    {{-- Banking details --}}
    <div class="section-title">Banking Details (EFT Payment)</div>
    <div style="background:#f9fafb; border-radius:8px; padding:16px; font-size:12px; line-height:1.8;">
        <strong>Account Name:</strong> MedSwift Express (Pty) Ltd<br>
        <strong>Bank:</strong> First National Bank (FNB)<br>
        <strong>Account Number:</strong> 000 000 0000<br>
        <strong>Branch Code:</strong> 250 655<br>
        <strong>Reference:</strong> {{ $invoiceNumber }}
    </div>

    {{-- Footer --}}
    <div class="footer">
        <p>MedSwift Express (Pty) Ltd · VAT Reg: 4XXXXXXXXXX · <a href="https://medswift.express">medswift.express</a></p>
        <p style="margin-top:4px;">This invoice was generated on {{ now()->format('d M Y \a\t H:i') }} · Thank you for your business.</p>
    </div>

</div>
</body>
</html>
