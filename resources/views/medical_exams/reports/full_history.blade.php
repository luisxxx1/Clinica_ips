<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Examenes de Ingreso Escolar - {{ $exam->student->document_number }}</title>
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

        body {
            font-size: 8.5pt;
            color: #000;
            line-height: 1.3;
            background: #fff;
            padding: 1.2cm 1.8cm;
        }

        .page-break {
            page-break-after: always;
        }

        .page-break:last-child {
            page-break-after: auto;
        }

        .header-container {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0px;
        }

        .ips-logo-cell {
            width: 110px;
            vertical-align: middle;
        }

        .ips-info {
            padding-left: 10px;
            vertical-align: middle;
        }

        .ips-name {
            color: #3b82f6;
            font-weight: bold;
            font-size: 11.5pt;
            font-style: italic;
        }

        .ips-nit {
            font-size: 9pt;
            color: #1d4ed8;
            font-weight: bold;
        }

        .main-title {
            text-align: center;
            font-weight: bold;
            font-size: 12pt;
            margin: 3px 0 6px;
            text-transform: uppercase;
            width: 100%;
            display: block;
        }

        .top-data-row {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
        }

        .top-data-row td {
            font-size: 8pt;
            padding: 6px 0;
            vertical-align: top;
        }

        .label-bold {
            font-weight: bold;
            display: block;
            text-transform: uppercase;
            font-size: 7.5pt;
            margin-bottom: 2px;
        }

        .sub-title {
            font-weight: bold;
            font-size: 9.5pt;
            text-transform: uppercase;
            border-bottom: 1px solid #000;
            padding-bottom: 2px;
            margin-top: 10px;
        }

        .id-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .id-grid td {
            padding: 5px 0;
            font-size: 8.5pt;
        }

        .section-name {
            text-align: center;
            font-weight: bold;
            font-size: 11pt;
            margin: 15px 0;
            text-transform: uppercase;
        }

        .data-table {
            border-collapse: collapse;
            text-align: center;
            font-size: 8pt;
            width: 100%;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #000;
            padding: 5px;
        }

        .purple-header {
            background-color: #800080;
            color: white;
        }

        .footer-content {
            margin-top: 20px;
            width: 100%;
        }

        .obs-title {
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .justified-text {
            text-align: justify;
            line-height: 1.4;
        }

        .justified-text p {
            margin: 0 0 5px 0;
            line-height: 1.5;
        }

        .notes-heading {
            font-weight: 900;
            text-transform: uppercase;
            margin: 8px 0 4px 0;
            color: #000;
            letter-spacing: 0.4px;
        }

        .notes-list {
            margin: 2px 0 8px 0;
        }

        .notes-list-row {
            margin: 0 0 4px 0;
        }

        .notes-list-index {
            font-weight: 900;
            margin-right: 4px;
        }

        .notes-list-text {
            font-weight: 400;
        }

        .notes-inline-label {
            font-weight: 900;
            color: #000;
        }

        .signature-block {
            margin-top: 40px;
            width: 350px;
        }

        .signature-line {
            border-top: 1.2px solid #000;
            padding-top: 5px;
            position: relative;
        }

        .signature-img {
            height: 70px;
            position: absolute;
            top: -65px;
            left: 20px;
        }

        .blue-footer {
            text-align: center;
            color: #1d4ed8;
            font-style: italic;
            font-size: 8.5pt;
            margin-top: 35px;
            border-top: 0.5px solid #ccc;
            padding-top: 10px;
        }
    </style>
</head>

<body>

    @php
        $publicRoots = [
            public_path(),
            base_path('public_html'),
            dirname(base_path()) . DIRECTORY_SEPARATOR . 'public_html',
        ];

        $resolveFromPublicRoots = function (array $relativeCandidates) use ($publicRoots): ?string {
            foreach ($relativeCandidates as $relativeCandidate) {
                $relativePath = ltrim((string) $relativeCandidate, '/\\');
                if ($relativePath === '') {
                    continue;
                }

                $relativePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath);

                foreach ($publicRoots as $root) {
                    if (!is_string($root) || $root === '') {
                        continue;
                    }

                    $absolutePath = rtrim($root, '/\\') . DIRECTORY_SEPARATOR . $relativePath;
                    if (file_exists($absolutePath)) {
                        return $absolutePath;
                    }
                }
            }

            return null;
        };

        $titulosPersonalizados = [
            'audiometria' => 'TAMIZ AUDITIVO',
            'fonoaudiologia' => 'TAMIZ FONOAUDIOLOGIA',
            'odontologia' => 'TAMIZ ODONTOLOGICO',
            'optometria' => 'TAMIZ VISUAL',
            'psicologia' => 'VALORACION PSICOLOGICA',
            'valoracion_medica' => 'CERTIFICADO MEDICO',
        ];

        $formatClinicalText = function ($text) {
            $lines = preg_split('/\R/u', (string) $text) ?: [];
            $html = '';
            $listItems = [];
            $listIndex = 1;

            $flushList = function () use (&$html, &$listItems, &$listIndex) {
                if (empty($listItems)) {
                    return;
                }

                $html .= '<div class="notes-list">';
                foreach ($listItems as $item) {
                    $html .= '<div class="notes-list-row">'
                        . '<span class="notes-list-index">' . $listIndex . '.</span>'
                        . '<span class="notes-list-text">' . e($item) . '</span>'
                        . '</div>';
                    $listIndex++;
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

                if (preg_match('/^(OBSERVACIONES|RECOMENDACIONES)\s*:?$/iu', $trimmed, $matches)) {
                    $flushList();
                    $listIndex = 1;
                    $heading = mb_strtoupper($matches[1], 'UTF-8');
                    $html .= '<div class="notes-heading">' . e($heading) . ':</div>';
                    continue;
                }

                if (preg_match('/^[-*]\s+(.+)$/u', $trimmed, $matches)) {
                    $listItems[] = trim($matches[1]);
                    continue;
                }

                $flushList();

                if (preg_match('/^([A-ZA-ZÁÉÍÓÚÑ]{2,25})\s*:\s*(.+)$/u', $trimmed, $matches)) {
                    $label = e($matches[1]) . ':';
                    $value = e($matches[2]);
                    $html .= '<p><span class="notes-inline-label">' . $label . '</span> ' . $value . '</p>';
                    continue;
                }

                $html .= '<p>' . e($trimmed) . '</p>';
            }

            $flushList();

            return $html;
        };
    @endphp

    @foreach ($exam->results->sortBy('area') as $result)
        <div class="page-break">
            <table class="header-container">
                <tr>
                    <td class="ips-logo-cell">
                        @php $logoSrc = $resolveFromPublicRoots(['LOGIN.png', 'login.png']); @endphp
                        @if($logoSrc)
                            <img src="{{ $logoSrc }}" style="height: 120px; width: auto;">
                        @endif
                    </td>
                    <td class="ips-info">
                        <div class="ips-name">I.P.S CREAR INTEGRAL S.A.S</div>
                        <div class="ips-nit">NIT 900727545-8</div>
                    </td>
                </tr>
            </table>

            <div class="main-title">EXAMENES DE INGRESO ESCOLAR</div>

            <table class="top-data-row">
                <tr>
                    <td width="33%">
                        <span class="label-bold">Fecha Emision</span>
                        {{ $result->created_at->format('d-m-Y') }}
                    </td>
                    <td width="34%" style="text-align: center;">
                        <span class="label-bold">Realizado Por</span>
                        IPS CREAR INTEGRAL SAS
                    </td>
                    <td width="33%" style="text-align: right;">
                        <span class="label-bold">Tipo Evaluacion - Enfasis</span>
                        INGRESO ESCOLAR
                    </td>
                </tr>
            </table>

            <div class="sub-title">IDENTIFICACION DEL USUARIO</div>
            <table class="id-grid">
                <tr>
                    <td width="45%"><span class="label-bold">Nombres y Apellidos</span>
                        {{ strtoupper(trim(($exam->student->first_name ?? '') . ' ' . ($exam->student->last_name ?? ''))) }}</td>
                    <td width="20%"><span class="label-bold">Identificacion</span>
                        {{ $exam->student->document_type }} {{ $exam->student->document_number }}</td>
                    <td width="20%"><span class="label-bold">Fecha de Nacimiento</span>
                        {{ $exam->student->birth_date ? \Carbon\Carbon::parse($exam->student->birth_date)->format('d-m-Y') : 'N/A' }}</td>
                    <td width="15%"><span class="label-bold">Edad</span>
                        {{ $exam->student->birth_date ? \Carbon\Carbon::parse($exam->student->birth_date)->age : 'N/A' }} ANOS</td>
                </tr>
            </table>

            @php
                $areaKey = (string) $result->area;
                $areaTitle = $titulosPersonalizados[$areaKey] ?? 'TAMIZ ' . strtoupper(str_replace('_', ' ', $areaKey));
                $resultData = is_array($result->data) ? $result->data : [];
            @endphp

            <div class="section-name">{{ $areaTitle }}</div>

            @if ($areaKey === 'audiometria')
                <table width="100%" style="margin-bottom: 18px; border-collapse: collapse;">

                    <tr>
                        <td width="36%" style="vertical-align: top; padding-right: 12px;">
                            <table class="data-table">
                                <tr class="purple-header">
                                    <th colspan="3" style="font-size: 8.5pt; padding: 6px;">Oido</th>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold; font-size: 8pt;">Via aerea enmasing</td>
                                    <td style="color: #dc2626; font-size: 12pt; font-weight: bold;">O</td>
                                    <td style="color: #1d4ed8; font-size: 12pt; font-weight: bold;">X</td>
                                </tr>
                            </table>

                            @php
                                $ptaOd = $result->pta_od;
                                $ptaOi = $result->pta_oi;
                                $ptaFreqs = [500, 1000, 2000];

                                if ($ptaOd === null) {
                                    $sumOd = 0;
                                    $countOd = 0;
                                    foreach ($ptaFreqs as $f) {
                                        $v = data_get($resultData, "dB_od_{$f}");
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
                                        $v = data_get($resultData, "dB_oi_{$f}");
                                        if (is_numeric($v)) {
                                            $sumOi += (float) $v;
                                            $countOi++;
                                        }
                                    }
                                    $ptaOi = $countOi > 0 ? round($sumOi / $countOi, 2) : null;
                                }
                            @endphp

                            <table class="data-table" style="margin-top: 16px; width: 72%; margin-left: 24px;">
                                <tr>
                                    <th>PTA</th>
                                    <th>OD</th>
                                    <th>OI</th>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td style="font-weight: bold;">{{ $ptaOd !== null ? number_format((float) $ptaOd, 2, '.', '') : '--' }}</td>
                                    <td style="font-weight: bold;">{{ $ptaOi !== null ? number_format((float) $ptaOi, 2, '.', '') : '--' }}</td>
                                </tr>
                            </table>
                        </td>
                        <td width="64%" style="text-align: center; vertical-align: top;">
                            @php
                                $audiogramPath = $result->chart_path ?? data_get($resultData, 'audiogram_path');
                                $audiogramImagePath = null;
                                if ($audiogramPath) {
                                    $storageAppPath = storage_path('app/public/' . ltrim($audiogramPath, '/'));
                                    if (file_exists($storageAppPath)) {
                                        $audiogramImagePath = $storageAppPath;
                                    } else {
                                        $audiogramImagePath = $resolveFromPublicRoots([
                                            'storage/' . ltrim($audiogramPath, '/'),
                                            ltrim($audiogramPath, '/'),
                                        ]);
                                    }
                                }
                            @endphp

                            <div style="border: 1px solid #9ca3af; padding: 4px; min-height: 230px;">
                                @if($audiogramImagePath)
                                    <img src="{{ $audiogramImagePath }}" style="width: 100%; max-height: 220px; object-fit: contain; display: block;">
                                @endif
                            </div>

                        </td>
                    </tr>
                </table>
            @elseif ($areaKey === 'odontologia')
                @php
                    $odontogramaPath = $result->chart_path ?? data_get($resultData, 'odontograma_path');
                    $imagePath = null;
                    if ($odontogramaPath) {
                        $storageAppPath = storage_path('app/public/' . ltrim($odontogramaPath, '/'));
                        if (file_exists($storageAppPath)) {
                            $imagePath = $storageAppPath;
                        } else {
                            $imagePath = $resolveFromPublicRoots([
                                'storage/' . ltrim($odontogramaPath, '/'),
                                ltrim($odontogramaPath, '/'),
                            ]);
                        }
                    }
                @endphp

                @if ($imagePath)
                    <div style="text-align: center; margin-bottom: 12px;">
                        <img src="{{ $imagePath }}" style="max-height: 220px; width: auto; max-width: 100%;">
                    </div>
                @endif
            @endif

            @if (!empty($resultData))
                <div style="margin-bottom: 10px;">
                    @foreach ($resultData as $campo => $valor)
                        @if (!is_array($valor) && !in_array($campo, ['odontograma_path', 'audiogram_path'], true))
                            <p style="margin-bottom: 2px;">
                                {{ ucfirst(str_replace('_', ' ', $campo)) }}: {{ $valor !== null && $valor !== '' ? $valor : 'N/A' }}
                            </p>
                        @endif
                    @endforeach
                </div>
            @endif

            <div class="footer-content">
                <div class="obs-title">OBSERVACIONES/RECOMENDACIONES:</div>
                <div class="justified-text" style="min-height: 90px;">
                    {!! $formatClinicalText($result->notes ?? 'Sin observaciones adicionales.') !!}
                </div>

                <p style="margin-top: 8px;">Atentamente,</p>

                <div class="signature-block">
                    <div class="signature-line">
                        @php
                            $signatureImagePath = null;
                            if ($result->specialist && $result->specialist->signature_path) {
                                $signatureRelative = trim((string) $result->specialist->signature_path);
                                if ($signatureRelative !== '' && strtolower($signatureRelative) !== 'signature_path') {
                                    $signatureImagePath = $resolveFromPublicRoots([
                                        'storage/' . ltrim($signatureRelative, '/'),
                                        ltrim($signatureRelative, '/'),
                                    ]);
                                }
                            }
                        @endphp
                        @if ($signatureImagePath)
                            <img src="{{ $signatureImagePath }}" class="signature-img">
                        @endif
                        <strong>{{ strtoupper($result->specialist->name ?? 'PROFESIONAL DE LA SALUD') }}</strong><br>
                        {{ strtoupper($result->specialist->specialty ?? 'ESPECIALISTA') }}<br>
                        REG. PROFESIONAL {{ $result->specialist->license_number ?? '-------' }}
                    </div>
                </div>
            </div>

            <div class="blue-footer">
                Direccion: Carrera 12 No 13-24 B/ Simon Bolivar - Jamundi (Valle)<br>
                Telefono: 316 185 57 27
            </div>
        </div>
    @endforeach

</body>

</html>
