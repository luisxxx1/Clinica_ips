<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <style>
        /* Configuración de márgenes de página */
        @page {
            margin: 0; /* Control total vía body padding */
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Helvetica', Arial, sans-serif;
        }

        body {
            font-size: 8.5pt;
            color: #000;
            line-height: 1.3;
            background: white;
            /* Espacios a los lados y extremos para que se vea aireado */
            padding: 1.2cm 1.8cm; 
        }

        .page-break {
            page-break-after: always;
        }

        .page-break:last-child {
            page-break-after: auto;
        }

        /* HEADER CON LOGO E INFO */
        .header-container {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
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

        /* TÍTULO CENTRADO ABAJO DEL LOGO */
        .main-title {
            text-align: center;
            font-weight: bold;
            font-size: 12pt;
            margin: 15px 0;
            text-transform: uppercase;
            width: 100%;
            display: block;
        }

        /* FILA DE DATOS DE EMISIÓN */
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

        /* SECCIÓN IDENTIFICACIÓN */
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

        /* TEXTO JUSTIFICADO */
        .justified-text {
            text-align: justify;
            line-height: 1.4;
        }

        /* TABLAS TÉCNICAS */
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

        /* BLOQUE INFERIOR */
        .footer-content {
            margin-top: 25px;
            width: 100%;
        }

        .obs-title {
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .signature-block {
            margin-top: 50px;
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
            margin-top: 40px;
            border-top: 0.5px solid #ccc;
            padding-top: 10px;
        }
    </style>
</head>

<body>

    @php
        $titulosPersonalizados = [
            'audiometria' => 'TAMIZ AUDITIVO',
            'fonoaudiologia' => 'TAMIZ FONOAUDIOLOGÍA',
            'odontologia' => 'TAMIZ ODONTOLÓGICO',
        ];
    @endphp

    @foreach ($exam->results->sortBy('area') as $result)
        <div class="page-break">

            {{-- 1. LOGO E IPS --}}
            <table class="header-container">
                <tr>
                    <td class="ips-logo-cell">
                        @php $logoSrc = request()->routeIs('*.preview_debug') ? asset('LOGIN.png') : public_path('LOGIN.png'); @endphp
                        <img src="{{ $logoSrc }}" style="height: 65px; width: auto;">
                    </td>
                    <td class="ips-info">
                        <div class="ips-name">I.P.S CREAR INTEGRAL S.A.S</div>
                        <div class="ips-nit">NIT 900727545-8</div>
                    </td>
                </tr>
            </table>

            {{-- 2. TÍTULO CENTRADO --}}
            <div class="main-title">
                EXAMENES DE INGRESO ESCOLAR
            </div>

            {{-- 3. DATOS DE EMISIÓN --}}
            <table class="top-data-row">
                <tr>
                    <td width="33%">
                        <span class="label-bold">Fecha Emisión</span>
                        {{ \Carbon\Carbon::parse($exam->created_at)->format('d-m-Y') }}
                    </td>
                    <td width="34%" style="text-align: center;">
                        <span class="label-bold">Realizado Por</span>
                        IPS CREAR INTEGRAL SAS
                    </td>
                    <td width="33%" style="text-align: right;">
                        <span class="label-bold">Tipo Evaluación – Énfasis</span>
                        INGRESO ESCOLAR
                    </td>
                </tr>
            </table>

            {{-- 4. IDENTIFICACIÓN DEL USUARIO --}}
            @if ($loop->first)
                <div class="sub-title">IDENTIFICACION DEL USUARIO</div>
                <table class="id-grid">
                    <tr>
                        <td width="45%"><span class="label-bold">Nombres y Apellidos</span>
                            {{ strtoupper($exam->student->first_name . ' ' . $exam->student->last_name) }}</td>
                        <td width="20%"><span class="label-bold">Identificación</span>
                            {{ $exam->student->document_type }} {{ $exam->student->document_number }}</td>
                        <td width="20%"><span class="label-bold">Fecha de Nacimiento</span>
                            {{ \Carbon\Carbon::parse($exam->student->birth_date)->format('d-m-Y') }}</td>
                        <td width="15%"><span class="label-bold">Edad</span>
                            {{ \Carbon\Carbon::parse($exam->student->birth_date)->age }} AÑOS</td>
                    </tr>
                    <tr>
                        <td colspan="2"><span class="label-bold">Institución Educativa</span>
                            {{ strtoupper($exam->student->school_name ?? 'NO REGISTRA') }}</td>
                        <td colspan="2"><span class="label-bold">Grado a Ingresar</span>
                            {{ strtoupper($exam->student->grade ?? 'N/A') }}</td>
                    </tr>
                </table>
            @endif

            {{-- NOMBRE DEL EXAMEN ACTUAL --}}
            @php $areaKey = strtolower((string)$result->area); @endphp
            <div class="section-name">{{ $titulosPersonalizados[$areaKey] ?? 'TAMIZ ' . strtoupper($areaKey) }}</div>

            {{-- CONTENIDO TÉCNICO --}}
            @if (Str::contains($areaKey, 'audiometria'))
                <table width="100%" style="margin-bottom: 20px;">
                    <tr>
                        <td width="45%" style="vertical-align: top;">
                            <table class="data-table">
                                <tr class="purple-header">
                                    <th></th>
                                    <th>Oído Derecho</th>
                                    <th>Oído Izquierdo</th>
                                </tr>
                                <tr>
                                    <td style="text-align: left;">Vía aérea sin masking</td>
                                    <td><span style="color:red; font-size:13pt;">○</span></td>
                                    <td><span style="color:blue; font-size:13pt;">✕</span></td>
                                </tr>
                            </table>
                            <table class="data-table" style="width: 80%; margin: 15px auto;">
                                <tr>
                                    <th rowspan="2" style="background: #f9f9f9;">PTA</th>
                                    <th>OD</th>
                                    <th>OI</th>
                                </tr>
                                <tr>
                                    <td>{{ $result->data['pta_od'] ?? '---' }}</td>
                                    <td>{{ $result->data['pta_oi'] ?? '---' }}</td>
                                </tr>
                            </table>
                        </td>
                        <td width="55%" style="text-align: center;">
                            <div style="border: 1px solid #ccc; height: 180px; padding-top: 80px; color: #999;">
                                [Gráfica de Audiometría]
                            </div>
                        </td>
                    </tr>
                </table>
            @endif

            {{-- OBSERVACIONES Y FIRMA --}}
            <div class="footer-content">
                <div class="obs-title">OBSERVACIONES/RECOMENDACIONES:</div>
                <div class="justified-text" style="min-height: 80px;">
                    {{ $result->notes ?? ($result->observations ?? 'Sin observaciones adicionales.') }}
                </div>

                <div class="signature-block">
                    <div class="signature-line">
                        @if ($result->specialist && $result->specialist->signature_path)
                            <img src="{{ public_path('storage/'.$result->specialist->signature_path) }}" class="signature-img">
                        @endif
                        <strong>{{ strtoupper($result->specialist->name ?? 'Profesional') }}</strong><br>
                        {{ strtoupper($result->specialist->specialty ?? 'Especialista') }}<br>
                        {{ strtoupper($result->specialist->university ?? 'Universidad Santiago de Cali') }}<br>
                        <span style="font-weight: bold;">REG. PROFESIONAL: {{ $result->specialist->license_number ?? '-------' }}</span>
                    </div>
                </div>
            </div>

            <div class="blue-footer">
                Carrera 12 No 13-24 B/ Simón Bolívar - Jamundí (Valle) | Tel: 316 185 57 27
            </div>
        </div>
    @endforeach

</body>
</html>