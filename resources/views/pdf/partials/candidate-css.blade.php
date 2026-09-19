{{-- Raw CSS only — no <style> tags, no <html>/<head>. --}}

body {
    font-family: sans-serif;
    font-size: 11px;
    color: #1f2937;
}

.letterhead {
    width: 100%;
    border-bottom: 3px solid #0B4F3F;
    padding-bottom: 10px;
    margin-bottom: 14px;
}
.letterhead table {
    width: 100%;
}

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