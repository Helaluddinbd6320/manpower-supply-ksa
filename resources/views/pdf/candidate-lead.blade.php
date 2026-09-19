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
        .header {
            background-color: #0e8c4d;
            color: #ffffff;
            padding: 14px 16px;
            margin-bottom: 16px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0;
        }
        .header p {
            font-size: 10px;
            margin: 2px 0 0;
            opacity: 0.9;
        }
        .layout-table {
            width: 100%;
        }
        .photo-box {
            width: 110px;
            height: 130px;
            border: 1px solid #ccc;
            text-align: center;
            vertical-align: middle;
        }
        .photo-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 5px 0;
            font-size: 11px;
            vertical-align: top;
        }
        .info-table td.label {
            width: 140px;
            color: #555;
            font-weight: bold;
        }
        .status-badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 10px;
            background-color: #eefdf3;
            color: #0e8c4d;
            font-weight: bold;
            font-size: 10px;
        }
        .section-title {
            font-weight: bold;
            font-size: 12px;
            margin: 18px 0 8px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 4px;
        }
        .passport-img {
            width: 100%;
            max-width: 400px;
            border: 1px solid #ccc;
            margin-top: 6px;
        }
        .notes-box {
            background-color: #f7f7f7;
            padding: 10px;
            font-size: 10.5px;
            line-height: 1.5;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Candidate Profile</h1>
        <p>Manpower Supply KSA — Internal Reference Sheet</p>
    </div>

    <table class="layout-table">
        <tr>
            <td width="120" style="vertical-align: top;">
                <div class="photo-box">
                    @if ($photoBase64)
                        <img src="{{ $photoBase64 }}">
                    @else
                        <span style="font-size: 9px; color: #999;">No Photo</span>
                    @endif
                </div>
            </td>
            <td style="vertical-align: top; padding-left: 16px;">
                <table class="info-table">
                    <tr>
                        <td class="label">Name</td>
                        <td><strong>{{ $candidate->name }}</strong></td>
                    </tr>
                    <tr>
                        <td class="label">Phone / WhatsApp</td>
                        <td>{{ $candidate->phone_number }}</td>
                    </tr>
                    <tr>
                        <td class="label">Age</td>
                        <td>{{ $candidate->age ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Area / District</td>
                        <td>{{ $candidate->area ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Destination Country</td>
                        <td>{{ $candidate->destinationCountry?->name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Interested Job Category</td>
                        <td>{{ $candidate->jobCategory?->name_en ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Source</td>
                        <td>{{ $candidate->source }}</td>
                    </tr>
                    <tr>
                        <td class="label">Status</td>
                        <td><span class="status-badge">{{ $candidate->status }}</span></td>
                    </tr>
                    <tr>
                        <td class="label">Next Follow-up</td>
                        <td>{{ $candidate->next_follow_up_date?->format('d M, Y') ?? '—' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    @if ($candidate->notes)
        <div class="section-title">Notes</div>
        <div class="notes-box">{{ $candidate->notes }}</div>
    @endif

    @if ($passportBase64)
        <div class="section-title">Passport Copy</div>
        <img src="{{ $passportBase64 }}" class="passport-img">
    @elseif ($candidate->passport_copy_path)
        <div class="section-title">Passport Copy</div>
        <p style="font-size: 10px; color: #777;">
            পাসপোর্ট কপি একটা PDF ফাইল হিসেবে সিস্টেমে আপলোড করা আছে — এটা অ্যাডমিন প্যানেল থেকে আলাদাভাবে দেখুন।
        </p>
    @endif

    @if ($candidate->followUps->isNotEmpty())
        <div class="section-title">Follow-up History</div>
        <table class="info-table" style="border-collapse: collapse;">
            @foreach ($candidate->followUps as $followUp)
                <tr>
                    <td style="width: 100px; font-size: 9.5px; color: #777;">
                        {{ $followUp->contacted_at->format('d M, Y') }}
                    </td>
                    <td style="font-size: 10px;">{{ $followUp->note }}</td>
                </tr>
            @endforeach
        </table>
    @endif

</body>
</html>