<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #1a1a1a;
        }

        .header-image {
            width: 100%;
            margin-bottom: 12px;
        }

        .meta-row {
            margin-bottom: 12px;
        }

        .meta-row td {
            padding: 2px 0;
            vertical-align: top;
        }

        .quotation-title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            text-decoration: underline;
            margin: 16px 0 12px;
        }

        table.items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        table.items-table th {
            background-color: #1a3a5c;
            color: #ffffff;
            font-size: 10px;
            padding: 6px 4px;
            border: 1px solid #1a3a5c;
            text-align: center;
        }

        table.items-table td {
            border: 1px solid #999;
            padding: 6px 4px;
            font-size: 10px;
            text-align: center;
        }

        table.items-table tfoot td {
            font-weight: bold;
            background-color: #f2f2f2;
        }

        .section-title {
            font-weight: bold;
            font-size: 12px;
            margin: 14px 0 6px;
        }

        .content-block {
            font-size: 10.5px;
            line-height: 1.5;
        }

        .content-block ol,
        .content-block ul {
            margin: 4px 0;
            padding-left: 18px;
        }

        .footer-block {
            margin-top: 20px;
            border-top: 2px solid #1a3a5c;
            padding-top: 8px;
            font-size: 9.5px;
            color: #444;
        }
    </style>
</head>

<body>

    @if ($headerImageBase64)
        <img src="{{ $headerImageBase64 }}" class="header-image">
    @endif

    <table class="meta-row" width="100%">
        <tr>
            <td width="70%">
                <div class="content-block">
                    {!! $quotation->client_info_content !!}
                </div>
            </td>
            <td width="30%" style="text-align: right;">
                <strong>Quotation No:</strong> {{ $quotation->quotation_number }}<br>
                <strong>Date:</strong> {{ $quotation->quotation_date->format('M d, Y') }}
            </td>
        </tr>
    </table>

    <div class="quotation-title">Quotation</div>

    <table class="items-table">
        <thead>
            <tr>
                <th>SN</th>
                <th>Category</th>
                <th>Nationality</th>
                <th>Gender</th>
                <th>Hours Duty</th>
                <th>Qty</th>
                <th>Rate/Hour</th>
                <th>Rate/Day</th>
                <th>Total Monthly</th>
                <th>Grand Total/Month</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($quotation->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->jobCategory?->name_en }}</td>
                    <td>{{ $item->nationality }}</td>
                    <td>{{ $item->gender }}</td>
                    <td>
                        @if ($item->pricing_type === 'hourly')
                            {{ $item->hours_per_day }} hours a day
                        @else
                            —
                        @endif
                    </td>
                    <td>{{ number_format($item->qty) }}</td>
                    <td>
                        @if ($item->pricing_type === 'hourly')
                            {{ number_format($item->rate_per_hour, 2) }}
                        @else
                            —
                        @endif
                    </td>
                    <td>
                        @if ($item->pricing_type === 'hourly')
                            {{ number_format($item->rate_per_day, 2) }}
                        @else
                            —
                        @endif
                    </td>
                    <td>{{ number_format($item->monthly_rate_per_worker, 2) }}</td>
                    <td>{{ number_format($item->grand_total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="9" style="text-align: right;">Overall Grand Total / Month (SAR)</td>
                <td>{{ number_format($quotation->grand_total, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="section-title">TERMS & CONDITION</div>
    <div class="content-block">
        {!! $quotation->terms_content !!}
    </div>

    <div class="content-block" style="margin-top: 16px;">
        {!! $quotation->signature_content !!}
    </div>

    <div class="footer-block">
        {!! $quotation->footer_content !!}
    </div>

</body>

</html>
