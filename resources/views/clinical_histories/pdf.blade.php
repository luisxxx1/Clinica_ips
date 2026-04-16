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
            color: #0f172a;
            font-size: 11px;
            margin: 0;
            padding: 14px 16px;
            line-height: 1.38;
        }
        .header {
            border-bottom: 2px solid #0f766e;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }
        .title {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 2px;
        }
        .subtitle {
            font-size: 10px;
            color: #64748b;
        }
        .patient-card {
            border: 1px solid #cbd5e1;
            background: #f8fafc;
            padding: 9px 10px;
            margin-bottom: 10px;
        }
        .patient-row {
            margin-bottom: 5px;
        }
        .patient-grid {
            width: 100%;
            border-collapse: collapse;
        }
        .patient-grid td {
            width: 50%;
            vertical-align: top;
            padding: 2px 4px;
        }
        .label {
            display: inline-block;
            min-width: 132px;
            font-weight: 700;
            color: #0f766e;
        }
        .entry {
            border: 1px solid #cbd5e1;
            margin-bottom: 8px;
            page-break-inside: avoid;
        }
        .entry-head {
            background: #f1f5f9;
            border-bottom: 1px solid #e2e8f0;
            padding: 6px 8px;
        }
        .entry-title {
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 2px;
            color: #0f172a;
        }
        .entry-meta {
            font-size: 9px;
            color: #64748b;
        }
        .entry-body {
            padding: 8px;
            white-space: pre-line;
            color: #1f2937;
            line-height: 1.42;
        }
        .graphics-title {
            margin: 12px 0 8px;
            font-size: 11px;
            font-weight: 700;
            color: #0f766e;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .graphic-card {
            border: 1px solid #cbd5e1;
            background: #f8fafc;
            margin-bottom: 8px;
            page-break-inside: avoid;
            padding: 7px;
        }
        .graphic-card-tight {
            margin-bottom: 2px;
        }
        .graphic-meta {
            font-size: 9px;
            color: #475569;
            margin-bottom: 5px;
        }
        .graphic-image {
            border: 1px solid #cbd5e1;
            background: #ffffff;
            padding: 4px;
            text-align: center;
        }
        .graphic-image img {
            max-width: 100%;
            max-height: 240px;
            display: inline-block;
        }
        .section-title {
            margin: 10px 0 6px;
            font-size: 11px;
            font-weight: 700;
            color: #0f766e;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            page-break-after: avoid;
        }
        .section-text-block {
            page-break-inside: avoid;
        }
        .section-flow {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .section-flow td {
            vertical-align: top;
            padding: 0;
        }
        .section-title-cell {
            padding: 0 0 8px 0;
        }
        .section-graphic-cell {
            padding: 0;
        }
        .section-history-cell {
            padding-top: 2px;
        }
        .section-visual-anchor {
            page-break-inside: avoid;
            break-inside: avoid-page;
            margin-bottom: 6px;
        }
        .section-subtitle {
            margin: 6px 0 5px;
            font-size: 10px;
            font-weight: 700;
            color: #0f172a;
            text-transform: uppercase;
        }
        .audio-main {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2px;
        }
        .audio-left {
            width: 40%;
            vertical-align: top;
            padding-right: 8px;
        }
        .audio-right {
            width: 60%;
            vertical-align: top;
            text-align: center;
        }
        .audio-table {
            width: 100%;
            border-collapse: collapse;
        }
        .audio-table th,
        .audio-table td {
            border: 1px solid #334155;
            padding: 3px;
            text-align: center;
        }
        .audio-head {
            background: #e2e8f0;
            font-size: 9pt;
            font-weight: 700;
            color: #0f172a;
        }
        .audio-graph-box {
            border: 1px solid #334155;
            background: #fff;
            padding: 3px;
        }
        .audio-graph-box img {
            width: 100%;
            max-height: 180px;
            height: auto;
            display: block;
        }
        .empty {
            border: 1px dashed #94a3b8;
            background: #f8fafc;
            padding: 12px;
            text-align: center;
            color: #475569;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }
        .footer {
            margin-top: 12px;
            padding-top: 8px;
            border-top: 1px solid #cbd5e1;
            font-size: 9px;
            color: #64748b;
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
        @php
            $birthDate = $student->birth_date
                ?? $student->date_of_birth
                ?? $student->fecha_nacimiento
                ?? null;
        @endphp
        <table class="patient-grid">
            <tr>
                <td><span class="label">Paciente:</span> {{ $student->first_name }} {{ $student->last_name }}</td>
                <td><span class="label">Documento:</span> {{ $student->document_type }} {{ $student->document_number }}</td>
            </tr>
            <tr>
                <td><span class="label">Edad:</span> {{ $student->age }} anos</td>
                <td><span class="label">Grado:</span> {{ $student->grade }}</td>
            </tr>
            <tr>
                <td><span class="label">Colegio:</span> {{ $student->previous_school ?? 'No registrado' }}</td>
                <td><span class="label">Fecha de nacimiento:</span> {{ $birthDate ? \Carbon\Carbon::parse($birthDate)->format('d/m/Y') : 'No registrada' }}</td>
            </tr>
        </table>
    </div>

    @if(isset($orderedSections) && $orderedSections->isNotEmpty())
        @foreach($orderedSections as $section)
            @php
                $supportsGraphic = in_array($section['area'], ['audiometria', 'odontologia']);
            @endphp
            @if($supportsGraphic)
                <div class="section-visual-anchor">
                    <div class="section-title">{{ $section['title'] }}</div>
                    <div class="section-graphic-cell">
                            @if(!empty($section['graphic']) && $section['area'] === 'audiometria')
                                @php
                                    $graphic = $section['graphic'];
                                    $ptaOd = $graphic['pta_od'];
                                    $ptaOi = $graphic['pta_oi'];
                                    $ptaFreqs = [500, 1000, 2000];

                                    if ($ptaOd === null) {
                                        $sumOd = 0;
                                        $countOd = 0;
                                        foreach ($ptaFreqs as $f) {
                                            $v = data_get($graphic['data'], "dB_od_{$f}");
                                            if (is_numeric($v)) {
                                                $sumOd += (float) $v;
                                                $countOd++;
                                            }
                                        }
                                        $ptaOd = $countOd > 0 ? round($sumOd / $countOd, 2) : null;
                                    }

                                    if ($ptaOi === null) {
                                        $sumOi = 0;
                                        $countOi = 0;
                                        foreach ($ptaFreqs as $f) {
                                            $v = data_get($graphic['data'], "dB_oi_{$f}");
                                            if (is_numeric($v)) {
                                                $sumOi += (float) $v;
                                                $countOi++;
                                            }
                                        }
                                        $ptaOi = $countOi > 0 ? round($sumOi / $countOi, 2) : null;
                                    }
                                @endphp

                                <div class="graphic-card graphic-card-tight">
                                    <div class="graphic-meta">
                                        Area: {{ $section['label'] }} |
                                        Profesional: {{ $graphic['specialist'] }} |
                                        Fecha: {{ $graphic['created_at'] }}
                                    </div>

                                    <table class="audio-main">
                                        <tr>
                                            <td class="audio-left">
                                                <table class="audio-table">
                                                    <tr>
                                                        <th colspan="3" class="audio-head">CONVENCIONES - SIMBOLOS AUDIOMETRICOS</th>
                                                    </tr>
                                                    <tr style="height: 90px;">
                                                        <td style="font-weight: bold; font-size: 10pt;">Via Aerea</td>
                                                        <td>
                                                            <div style="font-size: 42pt; color: #dc2626; font-weight: bold; line-height: 1;">O</div>
                                                            <div style="font-size: 9pt; font-weight: bold; color: #dc2626;">OIDO<br>DERECHO</div>
                                                        </td>
                                                        <td>
                                                            <div style="font-size: 42pt; color: #1d4ed8; font-weight: bold; line-height: 1;">X</div>
                                                            <div style="font-size: 9pt; font-weight: bold; color: #1d4ed8;">OIDO<br>IZQUIERDO</div>
                                                        </td>
                                                    </tr>
                                                </table>

                                                <table class="audio-table" style="margin-top: 14px;">
                                                    <tr>
                                                        <th rowspan="2" style="width: 40%; background: #f1f5f9;">PTA</th>
                                                        <th style="color: #dc2626;">OD</th>
                                                        <th style="color: #1d4ed8;">OI</th>
                                                    </tr>
                                                    <tr>
                                                        <td style="font-weight: bold; font-size: 12pt;">{{ $ptaOd !== null ? number_format((float) $ptaOd, 2, '.', '') : '--' }}</td>
                                                        <td style="font-weight: bold; font-size: 12pt;">{{ $ptaOi !== null ? number_format((float) $ptaOi, 2, '.', '') : '--' }}</td>
                                                    </tr>
                                                </table>
                                            </td>
                                            <td class="audio-right">
                                                <div class="audio-graph-box">
                                                    <img src="{{ $graphic['source'] }}" alt="Grafica {{ $section['label'] }}">
                                                </div>
                                                <div style="font-size: 7pt; margin-top: 5px; font-weight: bold;">AUDIOGRAMA TONAL</div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            @elseif(!empty($section['graphic']))
                                <div class="graphic-card">
                                    <div class="graphic-meta">
                                        Area: {{ $section['label'] }} |
                                        Profesional: {{ $section['graphic']['specialist'] }} |
                                        Fecha: {{ $section['graphic']['created_at'] }}
                                    </div>
                                    <div class="graphic-image">
                                        <img src="{{ $section['graphic']['source'] }}" alt="Grafica {{ $section['label'] }}">
                                    </div>
                                </div>
                            @else
                                <div class="empty" style="margin-bottom: 10px;">No se encontró gráfica para {{ $section['label'] }}.</div>
                            @endif
                    </div>
                    @if($section['entries']->isNotEmpty())
                        @php $firstEntry = $section['entries']->first(); @endphp
                        <div class="entry">
                            <div class="entry-head">
                                <div class="entry-title">{{ $firstEntry->title ?: 'Nota clinica' }}</div>
                                <div class="entry-meta">
                                    Profesional: {{ $firstEntry->specialist->name ?? 'No registrado' }} |
                                    Fecha: {{ optional($firstEntry->recorded_at)->format('d/m/Y H:i') }}
                                </div>
                            </div>
                            <div class="entry-body">{{ $firstEntry->entry }}</div>
                        </div>
                    @else
                        <div class="empty" style="margin-bottom: 12px;">No hay entradas clínicas registradas para {{ $section['label'] }}.</div>
                    @endif
                </div>

                <div class="section-history-cell">
                    @if($section['entries']->count() > 1)
                        @foreach($section['entries']->slice(1) as $entry)
                            <div class="entry">
                                <div class="entry-head">
                                    <div class="entry-title">{{ $entry->title ?: 'Nota clinica' }}</div>
                                    <div class="entry-meta">
                                        Profesional: {{ $entry->specialist->name ?? 'No registrado' }} |
                                        Fecha: {{ optional($entry->recorded_at)->format('d/m/Y H:i') }}
                                    </div>
                                </div>
                                <div class="entry-body">{{ $entry->entry }}</div>
                            </div>
                        @endforeach
                    @endif
                </div>
            @else
                @if($section['entries']->isNotEmpty())
                    @foreach($section['entries'] as $entry)
                        @if($loop->first)
                            <div class="section-text-block">
                                <div class="section-title">{{ $section['title'] }}</div>
                                <div class="entry">
                                    <div class="entry-head">
                                        <div class="entry-title">{{ $entry->title ?: 'Nota clinica' }}</div>
                                        <div class="entry-meta">
                                            Profesional: {{ $entry->specialist->name ?? 'No registrado' }} |
                                            Fecha: {{ optional($entry->recorded_at)->format('d/m/Y H:i') }}
                                        </div>
                                    </div>
                                    <div class="entry-body">{{ $entry->entry }}</div>
                                </div>
                            </div>
                        @else
                            <div class="entry">
                                <div class="entry-head">
                                    <div class="entry-title">{{ $entry->title ?: 'Nota clinica' }}</div>
                                    <div class="entry-meta">
                                        Profesional: {{ $entry->specialist->name ?? 'No registrado' }} |
                                        Fecha: {{ optional($entry->recorded_at)->format('d/m/Y H:i') }}
                                    </div>
                                </div>
                                <div class="entry-body">{{ $entry->entry }}</div>
                            </div>
                        @endif
                    @endforeach
                @else
                    <div class="section-text-block">
                        <div class="section-title">{{ $section['title'] }}</div>
                        <div class="empty" style="margin-bottom: 12px;">No hay entradas clínicas registradas para {{ $section['label'] }}.</div>
                    </div>
                @endif
            @endif
        @endforeach
    @elseif($entries->isEmpty())
        <div class="empty">El paciente no registra entradas clinicas en el sistema.</div>
    @endif

    <div class="footer">
        Documento confidencial para uso clinico autorizado.
    </div>
</body>
</html>
