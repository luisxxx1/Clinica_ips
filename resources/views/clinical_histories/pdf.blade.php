<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            color: #1f2937;
            font-size: 12px;
            margin: 0;
            padding: 20px;
            line-height: 1.45;
        }
        .header {
            border-bottom: 2px solid #2563eb;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }
        .title {
            font-size: 20px;
            font-weight: 700;
            color: #1d4ed8;
            margin-bottom: 4px;
        }
        .subtitle {
            font-size: 11px;
            color: #6b7280;
        }
        .patient-card {
            border: 1px solid #dbeafe;
            background: #f8fbff;
            padding: 12px;
            margin-bottom: 18px;
        }
        .patient-row {
            margin-bottom: 5px;
        }
        .label {
            display: inline-block;
            min-width: 150px;
            font-weight: 700;
            color: #1d4ed8;
        }
        .entry {
            border: 1px solid #e5e7eb;
            margin-bottom: 14px;
            page-break-inside: avoid;
        }
        .entry-head {
            background: #eff6ff;
            border-bottom: 1px solid #dbeafe;
            padding: 8px 10px;
        }
        .entry-title {
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 2px;
            color: #1f2937;
        }
        .entry-meta {
            font-size: 10px;
            color: #4b5563;
        }
        .entry-body {
            padding: 10px;
            white-space: pre-line;
            color: #374151;
        }
        .empty {
            border: 1px dashed #d1d5db;
            padding: 20px;
            text-align: center;
            color: #6b7280;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            font-size: 10px;
            color: #6b7280;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Historial Clinico Integral</div>
        <div class="subtitle">IPS Escolar | Documento generado automaticamente</div>
        <div class="subtitle">Fecha de emision: {{ $generatedAt }}</div>
    </div>

    <div class="patient-card">
        <div class="patient-row"><span class="label">Paciente:</span> {{ $student->first_name }} {{ $student->last_name }}</div>
        <div class="patient-row"><span class="label">Documento:</span> {{ $student->document_type }} {{ $student->document_number }}</div>
        <div class="patient-row"><span class="label">Edad:</span> {{ $student->age }} anos</div>
        <div class="patient-row"><span class="label">Grado:</span> {{ $student->grade }}</div>
    </div>

    @if($entries->isEmpty())
        <div class="empty">El paciente no registra entradas clinicas en el sistema.</div>
    @else
        @foreach($entries as $entry)
            <div class="entry">
                <div class="entry-head">
                    <div class="entry-title">{{ $entry->title ?: 'Nota clinica' }}</div>
                    <div class="entry-meta">
                        Area: {{ str_replace('_', ' ', $entry->area) }} |
                        Profesional: {{ $entry->specialist->name ?? 'No registrado' }} |
                        Fecha: {{ optional($entry->recorded_at)->format('d/m/Y H:i') }}
                    </div>
                </div>
                <div class="entry-body">{{ $entry->entry }}</div>
            </div>
        @endforeach
    @endif

    <div class="footer">
        Documento confidencial para uso clinico autorizado.
    </div>
</body>
</html>
