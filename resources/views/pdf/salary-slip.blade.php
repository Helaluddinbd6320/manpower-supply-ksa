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
        .slip-title {
            font-size: 16px;
            font-weight: bold;
            text-align: right;
            margin-top: -30px;
        }
        table.info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.info-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
        }
        table.info-table td.label {
            width: 35%;
            font-weight: bold;
            color: #374151;
            background: #f9fafb;
        }
        .amount-box {
            margin-top: 24px;
            padding: 16px;
            background: #f0fdfa;
            border: 1px solid #0f766e;
            border-radius: 6px;
            text-align: center;
        }
        .amount-label {
            font-size: 12px;
            color: #6b7280;
        }
        .amount-value {
            font-size: 24px;
            font-weight: bold;
            color: #0f766e;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
        }
        .status-paid {
            background: #dcfce7;
            color: #15803d;
        }
        .status-pending {
            background: #fef3c7;
            color: #b45309;
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
        <div class="slip-title">SALARY SLIP</div>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Worker ID</td>
            <td>{{ $workerId }}</td>
        </tr>
        <tr>
            <td class="label">Worker Name</td>
            <td>{{ $workerName }}</td>
        </tr>
        <tr>
            <td class="label">Client Company</td>
            <td>{{ $clientCompanyName }}</td>
        </tr>
        <tr>
            <td class="label">Period</td>
            <td>{{ $periodLabel }}</td>
        </tr>
        <tr>
            <td class="label">Duty Days</td>
            <td>{{ $dutyDays }} / {{ $daysInMonth }} days</td>
        </tr>
        <tr>
            <td class="label">Payout Rate</td>
            <td>SAR {{ number_format($payoutRate, 2) }} / month</td>
        </tr>
        <tr>
            <td class="label">Payment Status</td>
            <td>
                <span class="status-badge {{ $isPaid ? 'status-paid' : 'status-pending' }}">
                    {{ $isPaid ? 'Paid' : 'Pending' }}
                </span>
                @if ($paidAt)
                    &nbsp; ({{ $paidAt }})
                @endif
            </td>
        </tr>
    </table>

    <div class="amount-box">
        <div class="amount-label">Total Amount for This Period</div>
        <div class="amount-value">SAR {{ number_format($amount, 2) }}</div>
    </div>

    <div class="footer-note">
        This salary slip is issued by ChapaiHR based on recorded duty days for the period stated above.
        For any queries, please contact your office staff or call +966 54 308 8658.
    </div>
</body>
</html>