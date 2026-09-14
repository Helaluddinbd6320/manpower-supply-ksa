{{-- Raw CSS only — no <style> tags, no <html>/<head>. --}}
{{-- Loaded ONCE via $mpdf->WriteHTML($css, \Mpdf\HTMLParserMode::HEADER_CSS) --}}

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

/* ---------- Status badge ---------- */
.status-row {
    margin-bottom: 14px;
}
.badge {
    display: inline-block;
    font-size: 10px;
    font-weight: bold;
    padding: 4px 12px;
    border-radius: 10px;
    letter-spacing: 0.3px;
}
.badge-available {
    background: #d1fae5;
    color: #065f46;
    border: 1px solid #6ee7b7;
}
.badge-working {
    background: #fef3c7;
    color: #92400e;
    border: 1px solid #fcd34d;
}

/* ---------- Photo / Iqama frames ---------- */
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
.photos-table td.iqama-cell {
    width: 62%;
}

/* Passport-style photo, fixed standard size 35mm x 45mm */
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

/* ID-card standard size (CR80): 85.6mm x 53.98mm */
.iqama-frame {
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

/* ---------- Footer content set separately via SetHTMLFooter ---------- */