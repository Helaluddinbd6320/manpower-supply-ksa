<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
            color: #1f2937;
        }

        /* ---------- Letterhead ---------- */
        .letterhead {
            width: 100%;
            border-bottom: 3px solid #0B4F3F;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }
        .letterhead table {
            width: 100%;
        }

        /* ---------- Photo / Passport frames ---------- */
        .photos-table {
            width: 100%;
            margin-bottom: 18px;
        }
        .photos-table td {
            text-align: center;
            vertical-align: top;
            padding: 0 8px;
        }
        .photos-table td.photo-cell {
            width: 38%;
        }
        .photos-table td.passport-cell {
            width: 62%;
        }

        .img-frame {
            display: inline-block;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            background: #f8fafc;
            overflow: hidden;
        }
        .photo-frame {
            width: 40mm;
            height: 50mm;
        }
        .passport-frame {
            width: 85.6mm;
            height: 53.98mm;
        }

        .img-placeholder {
            width: 100%;
            height: 100%;
            display: table-cell;
            vertical-align: middle;
            text-align: center;
            color: #9ca3af;
            font-size: 9.5px;
        }

        .frame-label {
            font-size: 8.5px;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-top: 6px;
            font-weight: bold;
        }

        /* ---------- Data table ---------- */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            overflow: hidden;
        }
        table.data-table tr {
            border-bottom: 1px solid #e5e7eb;
        }
        table.data-table tr:last-child {
            border-bottom: none;
        }
        table.data-table tr:nth-child(even) {
            background: #f9fafb;
        }
        table.data-table td {
            padding: 8px 12px;
            vertical-align: middle;
        }
        table.data-table td.label {
            width: 32%;
            font-weight: bold;
            font-size: 10px;
            color: #0B4F3F;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            border-right: 1px solid #e5e7eb;
        }
        table.data-table td.value {
            color: #1f2937;
            font-size: 12px;
        }

        .section-title {
            font-weight: bold;
            font-size: 12px;
            margin: 18px 0 8px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 4px;
            color: #0B4F3F;
        }
        .notes-box {
            background-color: #f7f7f7;
            padding: 10px;
            font-size: 10.5px;
            line-height: 1.5;
            border-radius: 6px;
        }
        .followup-table {
            width: 100%;
            border-collapse: collapse;
        }
        .followup-table td {
            padding: 5px 0;
            vertical-align: top;
        }
    </style>
</head>
<body>

    <div class="letterhead">
        <table>
            <tr>
                <td style="width: 60%; vertical-align: middle;">
                    <div style="font-size: 22px; font-weight: bold; color: #0f172a; letter-spacing: 0.5px;">Manpower Supply<span style="color: #0B4F3F;"> KSA</span></div>
                    <div style="font-size: 9.5px; color: #6b7280; margin-top: 2px;">Manpower Recruitment &amp; Placement — Saudi Arabia</div>
                </td>
                <td style="width: 40%; text-align: right; vertical-align: middle;">
                    <div style="font-size: 12px; color: #6b7280; text-transform: uppercase; letter-spacing: 1.5px; font-weight: bold; margin-bottom: 4px;">Candidate Ref</div>
                    <div style="font-size: 24px; font-weight: bold; color: #ffffff; background-color: #0B4F3F; border: 2px solid #C9974C; border-radius: 8px; padding: 6px 18px; display: inline-block; letter-spacing: 1px;">CL-{{ str_pad($candidate->id, 4, '0', STR_PAD_LEFT) }}</div>
                </td>
            </tr>
        </table>
    </div>

    <table class="photos-table">
        <tr>
            <td class="photo-cell">
                <div class="img-frame photo-frame">
                    @if ($photoBase64)
                        <img src="{{ $photoBase64 }}" style="width: 40mm; height: 50mm; display: block;">
                    @else
                        <div class="img-placeholder">No Photo</div>
                    @endif
                </div>
                <div class="frame-label">Photograph</div>
            </td>
            <td class="passport-cell">
                <div class="img-frame passport-frame">
                    @if ($passportBase64)
                        <img src="{{ $passportBase64 }}" style="width: 85.6mm; height: 53.98mm; display: block;">
                    @else
                        <div class="img-placeholder">
                            @if ($candidate->passport_copy_path)
                                Passport is a PDF file —<br>see admin panel
                            @else
                                No Passport Copy
                            @endif
                        </div>
                    @endif
                </div>
                <div class="frame-label">Passport Copy</div>
            </td>
        </tr>
    </table>

    <table class="data-table" style="width: 100%; border-collapse: collapse;">
        <colgroup>
            <col style="width: 19%;">
            <col style="width: 31%;">
            <col style="width: 19%;">
            <col style="width: 31%;">
        </colgroup>
        <tr>
            <td class="label">Name</td>
            <td class="value">{{ $candidate->name }}</td>
            <td class="label">Phone / WhatsApp</td>
            <td class="value">{{ $candidate->phone_number }}</td>
        </tr>
        <tr>
            <td class="label">Age</td>
            <td class="value">{{ $candidate->age ?? '—' }}</td>
            <td class="label">Area / District</td>
            <td class="value">{{ $candidate->area ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Destination Country</td>
            <td class="value">{{ $candidate->destinationCountry?->name ?? '—' }}</td>
            <td class="label">Interested Category</td>
            <td class="value">{{ $candidate->jobCategory?->name_en ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Source</td>
            <td class="value">{{ $candidate->source }}</td>
            <td class="label">&nbsp;</td>
            <td class="value">&nbsp;</td>
        </tr>
    </table>

    @if ($candidate->notes)
        <div class="section-title">Notes</div>
        <div class="notes-box">{{ $candidate->notes }}</div>
    @endif

    @if ($candidate->followUps->isNotEmpty())
        <div class="section-title">Follow-up History</div>
        <table class="followup-table">
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