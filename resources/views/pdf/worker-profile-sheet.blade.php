{{-- Single worker only — no <html>/<head>/<body>, no page-break class. --}}
{{-- The service calls $mpdf->AddPage() between workers instead. --}}

<div class="letterhead">
    <table style="width: 100%;">
        <tr>
            <td style="width: 60%; vertical-align: middle;">
                <div style="font-size: 22px; font-weight: bold; color: #0f172a; letter-spacing: 0.5px;">Manpower Supply<span style="color: #0B4F3F;"> KSA</span></div>
                <div style="font-size: 9.5px; color: #6b7280; margin-top: 2px;">Manpower Recruitment &amp; Placement — Saudi Arabia</div>
            </td>
            <td style="width: 40%; text-align: right; vertical-align: middle;">
                <div style="font-size: 12px; color: #6b7280; text-transform: uppercase; letter-spacing: 1.5px; font-weight: bold; margin-bottom: 4px;">Worker ID</div>
                <div style="font-size: 24px; font-weight: bold; color: #ffffff; background-color: #0B4F3F; border: 2px solid #C9974C; border-radius: 8px; padding: 6px 18px; display: inline-block; letter-spacing: 1px;">{{ $worker['worker_id'] }}</div>
            </td>
        </tr>
    </table>
</div>

<div class="status-row">
    <span class="badge {{ $worker['employment_status_class'] }}">{{ $worker['employment_status'] }}</span>
</div>

<table class="photos-table">
    <tr>
        <td class="photo-cell">
            <div class="img-frame photo-frame">
                @if ($worker['photo_base64'])
                    <img src="{{ $worker['photo_base64'] }}" style="width: 40mm; height: 50mm; display: block;">
                @else
                    <div class="img-placeholder">No Photo</div>
                @endif
            </div>
            <div class="frame-label">Photograph</div>
        </td>
        <td class="iqama-cell">
            <div class="img-frame iqama-frame">
                @if ($worker['iqama_base64'])
                    <img src="{{ $worker['iqama_base64'] }}" style="width: 85.6mm; height: 53.98mm; display: block;">
                @else
                    <div class="img-placeholder">No Iqama Photo</div>
                @endif
            </div>
            <div class="frame-label">Iqama / ID Card</div>
        </td>
    </tr>
</table>

{{-- Two-column data grid: label/value pairs are laid out two-per-row so a
     longer field list (10-15+ rows) still fits comfortably on one page. --}}
<table class="data-table" style="width: 100%; border-collapse: collapse;">
    <colgroup>
        <col style="width: 19%;">
        <col style="width: 31%;">
        <col style="width: 19%;">
        <col style="width: 31%;">
    </colgroup>
    @foreach (array_chunk($worker['rows'], 2) as $pair)
        <tr>
            <td class="label">{{ $pair[0]['label'] }}</td>
            <td class="value">{{ $pair[0]['value'] }}</td>
            @if (isset($pair[1]))
                <td class="label">{{ $pair[1]['label'] }}</td>
                <td class="value">{{ $pair[1]['value'] }}</td>
            @else
                <td class="label">&nbsp;</td>
                <td class="value">&nbsp;</td>
            @endif
        </tr>
    @endforeach
</table>