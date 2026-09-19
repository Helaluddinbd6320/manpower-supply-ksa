{{-- Single candidate only — no <html>/<head>/<body>, no page-break class. --}}
{{-- The service calls $mpdf->AddPage() between candidates instead. --}}

<div class="letterhead">
    <table>
        <tr>
            <td style="width: 60%; vertical-align: middle;">
                <div style="font-size: 22px; font-weight: bold; color: #0f172a; letter-spacing: 0.5px;">Manpower
                    Supply<span style="color: #0B4F3F;"> KSA</span></div>
                <div style="font-size: 9.5px; color: #6b7280; margin-top: 2px;">Manpower Recruitment &amp; Placement —
                    Saudi Arabia</div>
            </td>
            <td style="width: 40%; text-align: right; vertical-align: middle;">
                <div
                    style="font-size: 12px; color: #6b7280; text-transform: uppercase; letter-spacing: 1.5px; font-weight: bold; margin-bottom: 4px;">
                    Candidate Ref</div>
                <div
                    style="font-size: 24px; font-weight: bold; color: #ffffff; background-color: #0B4F3F; border: 2px solid #C9974C; border-radius: 8px; padding: 6px 18px; display: inline-block; letter-spacing: 1px;">
                    CL-{{ str_pad($candidate->id, 4, '0', STR_PAD_LEFT) }}</div>
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
        @if ($includePhone)
            <td class="label">Phone / WhatsApp</td>
            <td class="value">{{ $candidate->phone_number }}</td>
        @else
            <td class="label">Age</td>
            <td class="value">{{ $candidate->age ?? '—' }}</td>
        @endif
    </tr>
    @if ($includePhone)
        <tr>
            <td class="label">Age</td>
            <td class="value">{{ $candidate->age ?? '—' }}</td>
            <td class="label">Area / District</td>
            <td class="value">{{ $candidate->area ?? '—' }}</td>
        </tr>
    @else
        <tr>
            <td class="label">Area / District</td>
            <td class="value">{{ $candidate->area ?? '—' }}</td>
            <td class="label">&nbsp;</td>
            <td class="value">&nbsp;</td>
        </tr>
    @endif
    <tr>
        <td class="label">Destination Country</td>
        <td class="value">{{ $candidate->destinationCountry?->name ?? '—' }}</td>
        <td class="label">Interested Category</td>
        <td class="value">{{ $candidate->jobCategory?->name_en ?? '—' }}</td>
        <td class="value">{{ $candidate->nationality?->getLabel() ?? ($candidate->nationality ?? '—') }}</td>
        <td class="value">{{ $candidate->nationalityCountry?->name ?? '—' }}</td>
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
