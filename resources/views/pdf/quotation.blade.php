<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #1f2937;
        }
        .header-image {
            width: 100%;
            margin-bottom: 15px;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 15px;
        }
        .meta-table td {
            padding: 2px 0;
        }
        .meta-label {
            font-weight: bold;
            width: 140px;
        }
        .section-title {
            font-size: 13px;
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 6px;
            border-bottom: 1px solid #d1d5db;
            padding-bottom: 3px;
        }
        .client-info, .terms-content, .signature-content, .footer-content {
            margin-bottom: 10px;
        }
        table.items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        table.items-table th {
            background-color: #f3f4f6;
            border: 1px solid #d1d5db;
            padding: 6px;
            text-align: left;
            font-size: 11px;
        }
        table.items-table td {
            border: 1px solid #d1d5db;
            padding: 6px;
            font-size: 11px;
        }
        .text-right {
            text-align: right;
        }
        .grand-total-row td {
            font-weight: bold;
            background-color: #f9fafb;
        }
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            color: #ffffff;
            background-color: #6b7280;
        }
        .footer-content {
            margin-top: 20px;
            border-top: 1px solid #d1d5db;
            padding-top: 8px;
            font-size: 10px;
            color: #6b7280;
        }
    </style>
</head>
<body>

    @if ($quotation->header_image_path)
        <img class="header-image" src="{{ $headerImagePath }}">
    @endif

    <table class="meta-table">
        <tr>
            <td class="meta-label">Quotation No:</td>
            <td>{{ $quotation->quotation_number }}</td>
            <td class="meta-label">Date:</td>
            <td>{{ $quotation->quotation_date?->format('d M, Y') }}</td>
        </tr>
        <tr>
            <td class="meta-label">Client:</td>
            <td>{{ $quotation->client_company_name }}</td>
            <td class="meta-label">Status:</td>
            <td><span class="status-badge">{{ $quotation->status }}</span></td>
        </tr>
    </table>

    @if ($quotation->client_info_content)
        <div class="client-info">
            {!! \Illuminate\Support\Str::markdown($quotation->client_info_content) !!}
        </div>
    @endif

    <div class="section-title">Quotation Items</div>
    <table class="items-table">
        <thead>
            <tr>
                <th>Category</th>
                <th>Nationality</th>
                <th>Gender</th>
                <th>Pricing</th>
                <th>Qty</th>
                <th class="text-right">Rate / Worker (SAR)</th>
                <th class="text-right">Total / Month (SAR)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($quotation->items as $item)
                <tr>
                    <td>{{ $item->jobCategory?->name_en ?? '-' }}</td>
                    <td>{{ $item->nationality ?? '-' }}</td>
                    <td>{{ $item->gender ?? '-' }}</td>
                    <td>
                        {{ ucfirst($item->pricing_type) }}
                        @if ($item->pricing_type === 'hourly' && $item->hours_per_day)
                            ({{ $item->hours_per_day }} hrs/day @ SAR {{ number_format($item->rate_per_hour, 2) }}/hr)
                        @endif
                    </td>
                    <td>{{ $item->qty }}</td>
                    <td class="text-right">{{ number_format($item->monthly_rate_per_worker, 2) }}</td>
                    <td class="text-right">{{ number_format($item->qty * $item->monthly_rate_per_worker, 2) }}</td>
                </tr>
                @if ($item->notes)
                    <tr>
                        <td colspan="7" style="font-style: italic; color: #6b7280;">{{ $item->notes }}</td>
                    </tr>
                @endif
            @endforeach
            <tr class="grand-total-row">
                <td colspan="6" class="text-right">Grand Total / Month</td>
                <td class="text-right">SAR {{ number_format($quotation->grand_total, 2) }}</td>
            </tr>
        </tbody>
    </table>

    @if ($quotation->terms_content)
        <div class="section-title">Terms &amp; Conditions</div>
        <div class="terms-content">
            {!! \Illuminate\Support\Str::markdown($quotation->terms_content) !!}
        </div>
    @endif

    @if ($quotation->signature_content)
        <div class="section-title">Signature</div>
        <div class="signature-content">
            {!! \Illuminate\Support\Str::markdown($quotation->signature_content) !!}
        </div>
    @endif

    @if ($quotation->footer_content)
        <div class="footer-content">
            {!! \Illuminate\Support\Str::markdown($quotation->footer_content) !!}
        </div>
    @endif

</body>
</html>