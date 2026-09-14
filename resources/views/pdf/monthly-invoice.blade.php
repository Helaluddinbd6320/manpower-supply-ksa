<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #1f2937;
        }
        .header {
            border-bottom: 3px solid #0f766e;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .company-name {
            font-size: 22px;
            font-weight: bold;
            color: #0f766e;
        }
        .invoice-title {
            font-size: 16px;
            font-weight: bold;
            text-align: right;
            margin-top: -30px;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .meta-table td {
            padding: 4px 0;
            vertical-align: top;
        }
        .meta-table .label {
            color: #6b7280;
            width: 130px;
        }
        table.items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.items-table th {
            background: #0f766e;
            color: #fff;
            padding: 8px;
            text-align: left;
            font-size: 11px;
        }
        table.items-table td {
            padding: 8px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 11px;
        }
        table.items-table tr:nth-child(even) {
            background: #f9fafb;
        }
        .text-right {
            text-align: right;
        }
        .totals-table {
            width: 100%;
            margin-top: 8px;
        }
        .totals-table td {
            padding: 6px 8px;
        }
        .totals-table .total-label {
            text-align: right;
            font-weight: bold;
            color: #374151;
        }
        .totals-table .total-value {
            text-align: right;
            font-weight: bold;
            font-size: 15px;
            color: #0f766e;
            width: 150px;
        }
        .footer-note {
            margin-top: 30px;
            font-size: 10px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">ChapaiHR</div>
        <div class="invoice-title">INVOICE</div>
    </div>

    <table class="meta-table">
        <tr>
            <td class="label">Invoice No.</td>
            <td>{{ $invoiceNumber }}</td>
            <td class="label">Bill To</td>
            <td><strong>{{ $clientCompanyName }}</strong></td>
        </tr>
        <tr>
            <td class="label">Period</td>
            <td>{{ $periodLabel }}</td>
            <td class="label">Issue Date</td>
            <td>{{ $issueDate }}</td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Worker ID</th>
                <th>Worker Name</th>
                <th>Job Category</th>
                <th class="text-right">Duty Days</th>
                <th class="text-right">Rate (SAR)</th>
                <th class="text-right">Amount (SAR)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item['worker_id'] }}</td>
                    <td>{{ $item['worker_name'] }}</td>
                    <td>{{ $item['job_categories'] }}</td>
                    <td class="text-right">{{ $item['duty_days'] }}</td>
                    <td class="text-right">{{ number_format($item['rate'], 2) }}</td>
                    <td class="text-right">{{ number_format($item['amount'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals-table">
        <tr>
            <td class="total-label">Total Workers:</td>
            <td class="total-value">{{ count($items) }}</td>
        </tr>
        <tr>
            <td class="total-label">Total Amount Due:</td>
            <td class="total-value">SAR {{ number_format($totalAmount, 2) }}</td>
        </tr>
    </table>

    <div class="footer-note">
        This invoice reflects manpower services provided by ChapaiHR for the period stated above.
        For queries regarding this invoice, please contact us at +966 54 308 8658.
    </div>
</body>
</html>