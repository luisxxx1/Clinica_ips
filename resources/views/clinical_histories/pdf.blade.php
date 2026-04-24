<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        html,
        body,
        table,
        thead,
        tbody,
        tfoot,
        tr,
        th,
        td,
        div,
        span,
        p,
        a,
        strong,
        em,
        small,
        b,
        i,
        u,
        li,
        ol,
        ul,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: Arial, sans-serif !important;
        }

        body {
            margin-top: 170px;
            font-size: 8pt;
            color: #000;
            line-height: 1.28;
            background: #fff;
            padding: 0.95cm 1.35cm;
        }

        /* HEADER CON LOGO E INFO */
        .header-container {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2px;
        }

        .ips-logo-cell {
            width: 95px;
            vertical-align: middle;
        }

        .ips-info {
            padding-left: 6px;
            vertical-align: middle;
        }

        .ips-name {
            color: #000;
            font-weight: 900;
            font-size: 10.5pt;
            font-style: normal;
            letter-spacing: 0.2px;
        }

        .ips-nit {
            font-size: 8pt;
            color: #000;
            font-weight: 700;
        }

        /* TÍTULO CENTRADO */
        .main-title {
            text-align: center;
            font-weight: 900;
            font-size: 11.5pt;
            margin: 10px 0 10px;
            text-transform: uppercase;
            color: #000;
            letter-spacing: 0.3px;
        }

        /* FILA DE DATOS DE EMISIÓN */
        .top-data-row {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        .top-data-row td {
            font-size: 7.5pt;
            padding: 3px 0;
            vertical-align: top;
        }

        .label-bold {
            font-weight: 900;
            display: block;
            text-transform: uppercase;
            font-size: 7.5pt;
            margin-bottom: 1px;
            letter-spacing: 0.5px;
            color: #000;
        }

        /* SECCIÓN IDENTIFICACIÓN */
        .sub-title {
            font-weight: 900;
            font-size: 9pt;
            text-transform: uppercase;
            padding-bottom: 0;
            margin-top: 6px;
            color: #000;
            letter-spacing: 0.3px;
        }

        .id-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        .id-grid td {
            padding: 2px 0;
            font-size: 7.8pt;
            vertical-align: top;
        }

        .id-pair {
            display: block;
        }

        .id-pair-label {
            display: block;
            font-weight: 900;
            text-transform: uppercase;
            font-size: 7.5pt;
            letter-spacing: 0.4px;
            margin-bottom: 1px;
        }

        .id-pair-value {
            font-size: 7.8pt;
            font-weight: 400;
        }

        /* LEGADO: Estilos para secciones clínicas */
        .section-title {
            margin: 8px 0 6px;
            font-size: 10.5pt;
            font-weight: 900;
            color: #000;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            page-break-after: avoid;
            padding-bottom: 0;
        }

        .section-title-centered {
            text-align: center;
        }

        .section-text-block {
            page-break-inside: avoid;
        }

        .entry {
            margin-bottom: 6px;
            page-break-inside: avoid;
        }

        .entry-head {
            padding: 0 0 5px 0;
        }

        .entry-title {
            font-size: 9.5pt;
            font-weight: 900;
            margin-bottom: 2px;
            color: #000;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .entry-meta {
            font-size: 7.8pt;
            color: #000;
            font-weight: 700;
        }

        .entry-body {
            padding: 0;
            color: #000;
            line-height: 1.32;
            font-size: 7.9pt;
            text-align: justify;
            font-weight: 400;
        }

        .entry-body p {
            margin: 0 0 2px 0;
        }

        .entry-heading {
            font-family: Arial, sans-serif !important;
            font-size: 8.6pt;
            font-weight: 900;
            text-transform: uppercase;
            margin: 4px 0 1px 0;
            color: #000;
            letter-spacing: 0.4px;
        }

        .entry-list {
            margin: 0 0 2px 0;
        }

        .entry-list-row {
            margin: 0 0 0.5px 0;
        }

        .entry-list-index {
            font-weight: 900;
            margin-right: 1px;
            display: inline;
            min-width: 0;
        }

        .entry-list-text {
            font-weight: 400;
        }

        .entry-inline-label {
            font-weight: 400;
            color: #000;
        }

        .graphics-title {
            margin: 8px 0 6px;
            font-size: 10.5pt;
            font-weight: 900;
            color: #000;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            border-bottom: 1px solid #000;
            padding-bottom: 2px;
        }

        .graphic-card {
            margin-bottom: 6px;
            page-break-inside: avoid;
            padding: 0;
        }

        .graphic-card-tight {
            margin-bottom: 2px;
        }

        .graphic-meta {
            font-size: 7.8pt;
            color: #000;
            margin-bottom: 4px;
            font-weight: 700;
            border-bottom: 0;
            padding-bottom: 0;
        }

        .graphic-image {
            padding: 0;
            text-align: center;
        }

        .graphic-image img {
            max-width: 100%;
            max-height: 205px;
            display: inline-block;
        }

        .audio-main {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2px;
        }

        .audio-left {
            width: 40%;
            vertical-align: top;
            padding-right: 6px;
        }

        .audio-right {
            width: 60%;
            vertical-align: top;
            text-align: center;
        }

        .audio-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }

        .audio-table th,
        .audio-table td {
            border: 1px solid #000;
            padding: 3px;
            text-align: center;
            font-size: 7.8pt;
        }

        .audio-head {
            background: #fff;
            font-size: 8pt;
            font-weight: 900;
            color: #000;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .audio-graph-box {
            border: 1px solid #000;
            background: #fff;
            padding: 4px;
        }

        .audio-graph-box img {
            width: 100%;
            max-height: 155px;
            height: auto;
            display: block;
        }

        .empty {
            border: 1px dashed #94a3b8;
            background: #f8fafc;
            padding: 8px;
            text-align: center;
            color: #000;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .blue-footer {
            text-align: center;
            color: #000;
            font-style: normal;
            font-size: 7.8pt;
            margin-top: 12px;
            padding-top: 6px;
            border-top: 1px solid #000;
            font-weight: 600;
        }

        .report-divider {
            border: 0;
            border-top: 1px solid #000;
            margin: 6px 0 8px;
        }

        .report-metadata td,
        .report-metadata span,
        .report-metadata div {
            color: #000 !important;
        }

        .report-table-head th {
            color: #000 !important;
            font-weight: 900;
            text-transform: uppercase;
        }

        .signature-block {
            margin-top: 8px;
            width: 300px;
        }

        .signature-line {
            border-bottom: 1px solid #000;
            padding-bottom: 1px;
            min-height: 0;
            font-size: 7.8pt;
            line-height: 1.15;
        }

        .signature-img {
            height: 100px;
            width: auto;
            display: block;
            margin: 0 0 1px 0;
        }
    </style>
</head>
<body>

<!--  HEADER FIJO PARA TODAS LAS PAGINAS -->
<div style="
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    background: #fff;
    padding: 0.95cm 1.35cm 0 1.35cm;
    z-index: 9999;
">

    <table class="header-container">
        <tr>
            <td class="ips-logo-cell">
                <img src="{{ public_path('LOGIN.png') }}" style="height: 120px; width: auto;">
            </td>
            <td class="ips-info">
                <div class="ips-name">I.P.S CREAR INTEGRAL S.A.S</div>
                <div class="ips-nit">NIT 900727545-8</div>
            </td>
        </tr>
    </table>
</div>
<div class="main-title">HISTORIAL CLINICO INTEGRAL</div>
<hr class="report-divider">

<div style="
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: #fff;
    padding: 6px 1.35cm;
    text-align: center;
    font-size: 13pt;
    color: #2f57b8;
    font-style: italic;
    font-weight: 500;
    line-height: 1.12;
    z-index: 9999;
">
    Dirección: Carrera 12 No 13-24 B/ Simón Bolívar - Jamundí (Valle)<br>
    Teléfono: 316 185 57 27
</div>

    {{-- 3. DATOS DE EMISIÓN --}}
    <table class="top-data-row report-metadata">
        <tr>
            <td width="33%">
                <span class="label-bold">Fecha Emisión</span>
                {{ $generatedAt ? substr($generatedAt, 0, 10) : now()->format('d-m-Y') }}
            </td>
            <td width="34%" style="text-align: center;">
                <span class="label-bold">Realizado Por</span>
                IPS CREAR INTEGRAL SAS
            </td>
            <td width="33%" style="text-align: right;">
                <span class="label-bold">Documento Clínico</span>
                HISTORIAL INTEGRAL
            </td>
        </tr>
    </table>

    @php
        $cleanField = function ($value, bool $uppercase = false): string {
            $text = trim((string) ($value ?? ''));
            $text = ltrim($text, "|:;,- \t\n\r\0\x0B");

            if ($text === '') {
                return 'N/A';
            }

            return $uppercase ? strtoupper($text) : $text;
        };
    @endphp

    {{-- 4. IDENTIFICACIÓN DEL USUARIO --}}
    <div class="sub-title">IDENTIFICACION DEL USUARIO</div>
    <table class="id-grid">
        <tr>
            <td width="45%">
                <div class="id-pair">
                    <span class="id-pair-label">Nombres y Apellidos</span>
                    <span class="id-pair-value">{{ $cleanField($student->first_name . ' ' . $student->last_name, true) }}</span>
                </div>
            </td>
            <td width="20%">
                <div class="id-pair">
                    <span class="id-pair-label">Identificación</span>
                    <span class="id-pair-value">{{ $cleanField(($student->document_type ?? '') . ' ' . ($student->document_number ?? '')) }}</span>
                </div>
            </td>
            <td width="20%">
                @php
                    $birthDate = $student->birth_date ?? $student->date_of_birth ?? $student->fecha_nacimiento ?? null;
                @endphp
                <div class="id-pair">
                    <span class="id-pair-label">Fecha de Nacimiento</span>
                    <span class="id-pair-value">{{ $birthDate ? \Carbon\Carbon::parse($birthDate)->format('d-m-Y') : 'N/A' }}</span>
                </div>
            </td>
            <td width="15%">
                <div class="id-pair">
                    <span class="id-pair-label">Edad</span>
                    <span class="id-pair-value">{{ $student->age ?? 'N/A' }} AÑOS</span>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <div class="id-pair">
                    <span class="id-pair-label">Institución Educativa</span>
                    <span class="id-pair-value">{{ $cleanField($student->previous_school ?? $student->school_name ?? 'NO REGISTRA', true) }}</span>
                </div>
            </td>
            <td colspan="2">
                <div class="id-pair">
                    <span class="id-pair-label">Grado</span>
                    <span class="id-pair-value">{{ $cleanField($student->grade ?? 'N/A', true) }}</span>
                </div>
            </td>
        </tr>
    </table>

    @php
        $getAreaSignaturePath = function (?string $area): ?string {
            $normalized = strtolower(trim((string) $area));

            $explicitMap = [
                'psicologia' => 'Firma de psicologa.png',
                'valoracion_medica' => 'Firma medico general.png',
                'medicina_general' => 'Firma medico general.png',
                'medico' => 'Firma medico general.png',
                'odontologia' => 'Firma odontologa.png',
                'optometria' => 'Firma optometra.png',
                'audiometria' => 'Firma para fonoaudiologia y audiometria.png',
                'fonoaudiologia' => 'Firma para fonoaudiologia y audiometria.png',
            ];

            if (isset($explicitMap[$normalized])) {
                $publicRootSignature = public_path($explicitMap[$normalized]);
                if (file_exists($publicRootSignature)) {
                    return $publicRootSignature;
                }
            }

            $alternatives = [
                $normalized,
                str_replace('í', 'i', $normalized),
            ];

            if ($normalized === 'fonoaudiologia') {
                $alternatives[] = 'audiometria';
            }

            foreach (array_unique($alternatives) as $name) {
                $candidates = [
                    "firmas/{$name}.png",
                    "firmas/{$name}.jpg",
                    "firmas/{$name}.jpeg",
                    "signatures/{$name}.png",
                    "signatures/{$name}.jpg",
                    "signatures/{$name}.jpeg",
                ];

                foreach ($candidates as $relativePath) {
                    $absolutePath = public_path($relativePath);
                    if (file_exists($absolutePath)) {
                        return $absolutePath;
                    }
                }
            }

            return null;
        };

        $formatClinicalEntry = function ($text) {
            $lines = preg_split('/\R/u', (string) $text) ?: [];
            $html = '';
            $listItems = [];
            $forceBulletList = false;

            $flushList = function () use (&$html, &$listItems) {
                if (empty($listItems)) {
                    return;
                }

                $html .= '<div class="entry-list">';
                foreach ($listItems as $item) {
                    $html .= '<div class="entry-list-row">'
                        . '<span class="entry-list-index">•</span>'
                        . '<span class="entry-list-text">' . e($item) . '</span>'
                        . '</div>';
                }
                $html .= '</div>';
                $listItems = [];
            };

            foreach ($lines as $line) {
                $trimmed = trim((string) $line);

                if ($trimmed === '') {
                    $flushList();
                    continue;
                }

                if (preg_match('/^(OBSERVACIONES\/RECOMENDACIONES|OBSERVACIONES|RECOMENDACIONES)\s*:?$/iu', $trimmed, $matches)) {
                    $flushList();
                    $heading = mb_strtoupper($matches[1], 'UTF-8');
                    $html .= '<div class="entry-heading">' . e($heading) . ':</div>';
                    $forceBulletList = true;
                    continue;
                }

                if ($forceBulletList) {
                    $normalized = preg_replace('/^([•\-*]|\d+[\.)])\s*/u', '', $trimmed);
                    $listItems[] = trim((string) $normalized);
                    continue;
                }

                if (preg_match('/^[-*]\s+(.+)$/u', $trimmed, $matches)) {
                    $listItems[] = trim($matches[1]);
                    continue;
                }

                if (preg_match('/^\d+[\.)]\s+(.+)$/u', $trimmed, $matches)) {
                    $listItems[] = trim($matches[1]);
                    continue;
                }

                $flushList();

                if (preg_match('/^([A-ZÁÉÍÓÚÑ]{2,25})\s*:\s*(.+)$/u', $trimmed, $matches)) {
                    $html .= '<p>' . e($matches[1]) . ': ' . e($matches[2]) . '</p>';
                    continue;
                }

                $html .= '<p>' . e($trimmed) . '</p>';
            }

            $flushList();

            return $html;
        };
    @endphp

    @if(isset($orderedSections) && $orderedSections->isNotEmpty())
        @foreach($orderedSections as $section)
            @php
                $supportsGraphic = in_array($section['area'], ['audiometria', 'odontologia']);
                $isTamizSection = stripos((string) ($section['title'] ?? ''), 'tamiz') !== false;
            @endphp
            @if($supportsGraphic)
                <div class="section-visual-anchor">
                    <div class="section-title {{ $isTamizSection ? 'section-title-centered' : '' }}">{{ $section['title'] }}</div>
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

                                <table width="76%" style="margin: 0 auto 10px auto; border-collapse: collapse;">
                                    <tr>
                                        <td width="41%" style="vertical-align: top; padding-right: 8px;">
                                            <table class="report-table-head" style="border-collapse: collapse; text-align: center; font-size: 6.5pt; width: 100%;">
                                                <tr>
                                                    <th colspan="3" style="border: 1px solid #000; font-size: 7.5pt; padding: 6px; background: #f1f5f9;">
                                                        CONVENCIONES - SIMBOLOS AUDIOMETRICOS
                                                    </th>
                                                </tr>
                                                <tr style="height: 36px;">
                                                    <td style="border: 1px solid #000; text-align: center; font-weight: bold; font-size: 6.8pt; vertical-align: middle; color: #000;">Via Aerea</td>
                                                    <td style="border: 1px solid #000; text-align: center; vertical-align: middle;">
                                                        <div style="font-size: 30pt; color: #ef4444; font-weight: bold; line-height: 1;">O</div>
                                                        <div style="font-size: 6.8pt; font-weight: bold; color: #ef4444; margin-top: 1px;">OIDO<br>DERECHO</div>
                                                    </td>
                                                    <td style="border: 1px solid #000; text-align: center; vertical-align: middle;">
                                                        <div style="font-size: 30pt; color: #2563eb; font-weight: bold; line-height: 1;">X</div>
                                                        <div style="font-size: 6.8pt; font-weight: bold; color: #2563eb; margin-top: 1px;">OIDO<br>IZQUIERDO</div>
                                                    </td>
                                                </tr>
                                            </table>

                                            <table class="report-table-head" style="border-collapse: collapse; text-align: center; font-size: 6.5pt; width: 100%; margin-top: 8px;">
                                                <tr>
                                                    <th rowspan="2" style="border: 1px solid #000; background: #f1f5f9; width: 40%; font-size: 7.5pt;">PTA</th>
                                                    <th style="border: 1px solid #000; color: red;">OD</th>
                                                    <th style="border: 1px solid #000; color: blue;">OI</th>
                                                </tr>
                                                <tr>
                                                    <td style="border: 1px solid #000; font-size: 7.5pt; font-weight: bold;">{{ $ptaOd !== null ? number_format((float) $ptaOd, 2, '.', '') : '--' }}</td>
                                                    <td style="border: 1px solid #000; font-size: 7.5pt; font-weight: bold;">{{ $ptaOi !== null ? number_format((float) $ptaOi, 2, '.', '') : '--' }}</td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td width="59%" style="text-align: center; vertical-align: top;">
                                            <div style="border: 1px solid #000; padding: 3px; background-color: #fff;">
                                                <img src="{{ $graphic['source'] }}" style="width: 100%; max-height: 170px; height: auto; display: block;" alt="Grafica {{ $section['label'] }}">
                                            </div>
                                            <div style="font-size: 6.2pt; margin-top: 3px; font-weight: bold; color: #000;">AUDIOGRAMA TONAL</div>
                                        </td>
                                    </tr>
                                </table>
                            @elseif(!empty($section['graphic']))
                                <div class="graphic-card">
                                    <div class="graphic-image">
                                        <img src="{{ $section['graphic']['source'] }}" alt="Grafica {{ $section['label'] }}">
                                    </div>
                                </div>
                            @else
                                <div class="empty" style="margin-bottom: 10px;">No se encontró gráfica para {{ $section['label'] }}.</div>
                            @endif
                    </div>
                    @if($section['entries']->isNotEmpty())
                        @php
                            $firstEntry = $section['entries']->first();
                            $signaturePath = $getAreaSignaturePath($section['area']);
                        @endphp
                        <div class="entry">
                            <div class="entry-body">{!! $formatClinicalEntry($firstEntry->entry) !!}</div>
                        </div>
                        @if($signaturePath)
                            <div class="signature-block">
                                <div class="signature-line">
                                    <img src="{{ $signaturePath }}" class="signature-img" alt="Firma {{ $section['label'] }}">
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="empty" style="margin-bottom: 12px;">No hay entradas clínicas registradas para {{ $section['label'] }}.</div>
                    @endif
                </div>

                <div class="section-history-cell">
                    @if($section['entries']->count() > 1)
                        @foreach($section['entries']->slice(1) as $entry)
                            <div class="entry">
                                <div class="entry-body">{!! $formatClinicalEntry($entry->entry) !!}</div>
                            </div>
                        @endforeach
                    @endif
                </div>
            @else
                @if($section['entries']->isNotEmpty())
                    @foreach($section['entries'] as $entry)
                        @if($loop->first)
                            @php $signaturePath = $getAreaSignaturePath($section['area']); @endphp
                            <div class="section-text-block">
                                <div class="section-title {{ $isTamizSection ? 'section-title-centered' : '' }}">{{ $section['title'] }}</div>
                                <div class="entry">
                                    <div class="entry-body">{!! $formatClinicalEntry($entry->entry) !!}</div>
                                </div>
                                @if($signaturePath)
                                    <div class="signature-block">
                                        <div class="signature-line">
                                            <img src="{{ $signaturePath }}" class="signature-img" alt="Firma {{ $section['label'] }}">
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="entry">
                                <div class="entry-body">{!! $formatClinicalEntry($entry->entry) !!}</div>
                            </div>
                        @endif
                    @endforeach
                @else
                    <div class="section-text-block">
                        <div class="section-title {{ $isTamizSection ? 'section-title-centered' : '' }}">{{ $section['title'] }}</div>
                        <div class="empty" style="margin-bottom: 12px;">No hay entradas clínicas registradas para {{ $section['label'] }}.</div>
                    </div>
                @endif
            @endif
        @endforeach
    @elseif($entries->isEmpty())
        <div class="empty">El paciente no registra entradas clinicas en el sistema.</div>
    @endif

</body>
</html>
