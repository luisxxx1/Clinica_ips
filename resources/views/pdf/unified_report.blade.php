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
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 4px solid #0066cc;
            padding-bottom: 25px;
        }
        .header-logo {
            font-size: 12px;
            color: #666;
            margin-bottom: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header-title {
            font-size: 28px;
            font-weight: bold;
            color: #0066cc;
            margin: 15px 0;
            letter-spacing: 1px;
        }
        .header-subtitle {
            font-size: 14px;
            color: #666;
            margin: 10px 0;
            font-weight: bold;
        }
        .patient-card {
            background: linear-gradient(135deg, #0066cc 0%, #004499 100%);
            color: white;
            padding: 25px;
            margin-bottom: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,102,204,0.2);
        }
        .patient-card .label {
            font-size: 11px;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 3px;
        }
        .patient-card .value {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .patient-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .patient-grid-item .label {
            font-size: 10px;
            opacity: 0.85;
            text-transform: uppercase;
        }
        .patient-grid-item .value {
            font-size: 14px;
            font-weight: bold;
        }
        .section-break {
            page-break-after: always;
            margin: 40px 0;
        }
        .specialty-section {
            margin-bottom: 35px;
            border: 2px solid #0066cc;
            padding: 20px;
            background: #f9fbff;
            border-radius: 8px;
            page-break-inside: avoid;
        }
        .specialty-header {
            background: #0066cc;
            color: white;
            padding: 15px;
            margin: -20px -20px 20px -20px;
            border-radius: 6px 6px 0 0;
            font-size: 16px;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .specialty-badge {
            background: rgba(255,255,255,0.3);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
        }
        .evaluation-content {
            font-size: 12px;
            line-height: 1.8;
        }
        .data-row {
            display: flex;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e0e0e0;
        }
        .data-label {
            font-weight: bold;
            width: 40%;
            color: #0066cc;
        }
        .data-value {
            width: 60%;
            color: #333;
        }
        .notes-box {
            background: #fff3cd;
            padding: 15px;
            border-left: 4px solid #ffc107;
            margin-top: 15px;
            border-radius: 4px;
            font-size: 11px;
            line-height: 1.7;
        }
        .notes-title {
            font-weight: bold;
            color: #856404;
            display: block;
            margin-bottom: 8px;
            text-transform: uppercase;
            font-size: 10px;
        }
        .specialist-info {
            background: #f0f0f0;
            padding: 10px;
            margin-top: 15px;
            border-radius: 4px;
            font-size: 10px;
            color: #666;
            border-left: 3px solid #0066cc;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 2px solid #e0e0e0;
            padding-top: 20px;
        }
        .completion-badge {
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 10px 20px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
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
            background: #e8f1ff;
            border: 2px solid #0066cc;
            padding: 20px;
            margin: 30px 0;
            border-radius: 8px;
            text-align: center;
        }
        .summary-title {
            font-size: 14px;
            font-weight: bold;
            color: #0066cc;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        .areas-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-top: 15px;
        }
        .area-item {
            background: white;
            padding: 10px;
            border: 1px solid #0066cc;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            text-align: center;
            color: #0066cc;
        }
        .area-item.completed::before {
            content: "✓ ";
            color: #28a745;
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
                        @foreach($result->data as $key => $value)
                            @if(!in_array($key, ['_token', 'odontograma_imagen']))
                                <div class="data-row">
                                    <div class="data-label">{{ ucfirst(str_replace(['_', 'OD', 'OI'], [' ', 'OJO DERECHO', 'OJO IZQUIERDO'], $key)) }}</div>
                                    <div class="data-value">
                                        @if(is_array($value))
                                            {{ implode(', ', $value) }}
                                        @elseif($key === 'odontograma_path' && !empty($value))
                                            <em>[Imagen adjunta]</em>
                                        @else
                                            {{ $value ?? 'No registrado' }}
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @endforeach
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
