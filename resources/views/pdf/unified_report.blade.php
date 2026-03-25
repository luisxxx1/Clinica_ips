<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            color: #333;
            line-height: 1.6;
            background: #f5f5f5;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 30px 40px;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 3px solid #0066cc;
            padding-bottom: 20px;
        }
        .header-logo {
            font-size: 11px;
            color: #666;
            margin-bottom: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header-title {
            font-size: 26px;
            font-weight: bold;
            color: #0066cc;
            margin: 12px 0 8px 0;
            letter-spacing: 0.5px;
        }
        .header-subtitle {
            font-size: 13px;
            color: #666;
            margin: 8px 0;
            font-weight: normal;
        }
        .patient-card {
            background: linear-gradient(135deg, #0066cc 0%, #004499 100%);
            color: white;
            padding: 20px;
            margin-bottom: 25px;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0,102,204,0.15);
        }
        .patient-card .label {
            font-size: 10px;
            opacity: 0.85;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
            font-weight: bold;
        }
        .patient-card .value {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 12px;
        }
        .patient-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .patient-grid-item .label {
            font-size: 9px;
            opacity: 0.8;
            text-transform: uppercase;
        }
        .patient-grid-item .value {
            font-size: 13px;
            font-weight: bold;
        }
        .section-break {
            page-break-after: always;
            margin: 30px 0;
        }
        .specialty-section {
            margin-bottom: 30px;
            border: 1px solid #d0d0d0;
            padding: 18px;
            background: #fafafa;
            border-radius: 6px;
            page-break-inside: avoid;
        }
        .specialty-header {
            background: #0066cc;
            color: white;
            padding: 12px 15px;
            margin: -18px -18px 15px -18px;
            border-radius: 5px 5px 0 0;
            font-size: 15px;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .specialty-badge {
            background: rgba(255,255,255,0.25);
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 10px;
            font-weight: bold;
        }
        .evaluation-content {
            font-size: 12px;
            line-height: 1.8;
        }
        .evaluation-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 11px;
        }
        .evaluation-table th {
            background: #0066cc;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: bold;
            border: none;
        }
        .evaluation-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #e0e0e0;
        }
        .evaluation-table tr:nth-child(even) {
            background: #f9fbff;
        }
        .evaluation-table tr:hover {
            background: #f0f5ff;
        }
        .data-label-cell {
            width: 35%;
            font-weight: bold;
            color: #0066cc;
        }
        .data-value-cell {
            width: 65%;
            color: #333;
        }
        .notes-box {
            background: #fffbea;
            padding: 12px;
            border-left: 3px solid #ffc107;
            margin-top: 12px;
            border-radius: 3px;
            font-size: 10px;
            line-height: 1.6;
        }
        .notes-title {
            font-weight: bold;
            color: #856404;
            display: block;
            margin-bottom: 6px;
            text-transform: uppercase;
            font-size: 9px;
        }
        .specialist-info {
            background: #f5f5f5;
            padding: 10px 12px;
            margin-top: 12px;
            border-radius: 4px;
            font-size: 10px;
            color: #555;
            border-left: 3px solid #0066cc;
            line-height: 1.5;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 9px;
            color: #999;
            border-top: 2px solid #e0e0e0;
            padding-top: 15px;
            line-height: 1.6;
        }
        .completion-badge {
            display: block;
            background: linear-gradient(135deg, #28a745 0%, #1fa833 100%);
            color: white;
            padding: 12px 20px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
            width: 100%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 11px;
            background: white;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background: #e8f1ff;
            font-weight: bold;
            color: #0066cc;
        }
        .summary-box {
            background: linear-gradient(135deg, #e8f1ff 0%, #f0f5ff 100%);
            border: 1px solid #0066cc;
            padding: 18px;
            margin: 25px 0;
            border-radius: 6px;
            text-align: center;
        }
        .summary-title {
            font-size: 13px;
            font-weight: bold;
            color: #0066cc;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .areas-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
        }
        .area-item {
            background: white;
            padding: 10px;
            border: 1px solid #0066cc;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-align: center;
            color: #0066cc;
        }
        .area-item.completed {
            background: #d4edda;
            border-color: #28a745;
            color: #155724;
        }
        .area-item.completed::before {
            content: "✓ ";
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        {{-- PORTADA --}}
        <div class="header">
            <div class="header-logo">🏥 IPS CREAR INTEGRAL S.A.S</div>
            <div class="header-title">REPORTE INTEGRAL DE SALUD</div>
            <div class="header-subtitle">Evaluación Clínica Completa del Ingreso Escolar</div>
            <div class="header-subtitle" style="color: #0066cc; margin-top: 20px;">
                Generado: {{ now()->format('d de F de Y - H:i') }}
            </div>
        </div>

        {{-- INFORMACIÓN DEL PACIENTE --}}
        <div class="patient-card">
            <div class="patient-grid">
                <div class="patient-grid-item">
                    <div class="label">Nombre del Paciente</div>
                    <div class="value">{{ $exam->student->full_name }}</div>
                </div>
                <div class="patient-grid-item">
                    <div class="label">Documento de Identidad</div>
                    <div class="value">{{ $exam->student->document_type }}: {{ $exam->student->document_number }}</div>
                </div>
                <div class="patient-grid-item">
                    <div class="label">Fecha de Nacimiento</div>
                    <div class="value">{{ $exam->student->birth_date ? $exam->student->birth_date->format('d/m/Y') : 'N/A' }}</div>
                </div>
                <div class="patient-grid-item">
                    <div class="label">Edad</div>
                    <div class="value">{{ $exam->student->age ?? 'N/A' }} años</div>
                </div>
                <div class="patient-grid-item">
                    <div class="label">Grado Académico</div>
                    <div class="value">{{ $exam->student->grade }}</div>
                </div>
                <div class="patient-grid-item">
                    <div class="label">Estado Clínico</div>
                    <div class="value" style="color: #28a745;">✓ COMPLETADO</div>
                </div>
            </div>
        </div>

        {{-- RESUMEN DE EVALUACIONES --}}
        <div class="summary-box">
            <div class="summary-title">Circuito de Evaluaciones</div>
            <div class="areas-grid">
                @foreach(['valoracion_medica' => 'Medicina General', 'odontologia' => 'Odontología', 'optometria' => 'Optometría', 'audiometria' => 'Audiometría', 'fonoaudiologia' => 'Fonoaudiología', 'psicologia' => 'Psicología'] as $slug => $name)
                    @php
                        $hasResult = $exam->results->where('area', $slug)->count() > 0;
                    @endphp
                    <div class="area-item {{ $hasResult ? 'completed' : '' }}">
                        {{ $name }}
                    </div>
                @endforeach
            </div>
        </div>

        {{-- EVALUACIONES POR ESPECIALIDAD --}}
        @foreach($exam->results as $index => $result)
            @if($index > 0)
                <div class="section-break"></div>
            @endif

            <div class="specialty-section">
                <div class="specialty-header">
                    <span>{{ ucfirst(str_replace('_', ' ', $result->area)) }}</span>
                    <span class="specialty-badge">{{ $result->created_at->format('d/m/Y H:i') }}</span>
                </div>

                <div class="evaluation-content">
                    @if(is_array($result->data) && count($result->data) > 0)
                        <table class="evaluation-table">
                            <thead>
                                <tr>
                                    <th>Campo</th>
                                    <th>Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($result->data as $key => $value)
                                    @if(!in_array($key, ['_token', 'odontograma_imagen']))
                                        <tr>
                                            <td class="data-label-cell">{{ ucfirst(str_replace(['_', 'OD', 'OI'], [' ', 'OJO DERECHO', 'OJO IZQUIERDO'], $key)) }}</td>
                                            <td class="data-value-cell">
                                                @if($key === 'odontograma_path' && !empty($value))
                                                    <em>[Imagen adjunta]</em>
                                                @elseif(is_array($value))
                                                    @php
                                                        $formatted = [];
                                                        foreach($value as $v) {
                                                            if (is_array($v)) {
                                                                $formatted[] = json_encode($v);
                                                            } else {
                                                                $formatted[] = (string)$v;
                                                            }
                                                        }
                                                        echo implode(', ', $formatted);
                                                    @endphp
                                                @else
                                                    {{ $value ?? 'No especificado' }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p style="color: #999;"><em>No hay datos de evaluación registrados</em></p>
                    @endif
                </div>

                @if(!empty($result->notes))
                    <div class="notes-box">
                        <span class="notes-title">📝 Observaciones del Especialista</span>
                        <p>{{ $result->notes }}</p>
                    </div>
                @endif

                <div class="specialist-info">
                    <strong>👨‍⚕️ Especialista:</strong> {{ $result->specialist->name ?? 'No registrado' }}<br>
                    <strong>📋 Rol:</strong> {{ $result->specialist->role->name ?? 'N/A' }}<br>
                    <strong>📅 Fecha:</strong> {{ $result->created_at->format('d/m/Y \a \l\a\s H:i') }}
                </div>
            </div>
        @endforeach

        {{-- RESUMEN FINAL --}}
        <div class="section-break"></div>
        <div class="completion-badge" style="display: block; width: 100%;">
            ✅ Este reporte integra todas las evaluaciones médicas realizadas
        </div>

        {{-- PIE DE PÁGINA --}}
        <div class="footer">
            <p><strong>IPS CREAR INTEGRAL S.A.S</strong></p>
            <p>Sistema de Gestión Clínica Escolar - Reporte Automático</p>
            <p>Documento confidencial - Uso exclusivo de profesionales autorizados</p>
            <p>Generado: {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>
</body>
</html>
