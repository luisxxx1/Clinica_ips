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
            border-bottom: 3px solid #0066cc;
            padding-bottom: 20px;
        }
        .header-logo {
            font-size: 12px;
            color: #666;
            margin-bottom: 10px;
        }
        .header-title {
            font-size: 24px;
            font-weight: bold;
            color: #0066cc;
            margin: 10px 0;
        }
        .header-subtitle {
            font-size: 12px;
            color: #999;
            margin: 5px 0;
        }
        .patient-info {
            background: #f9f9f9;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 4px solid #0066cc;
        }
        .info-row {
            display: flex;
            margin-bottom: 8px;
            font-size: 11px;
        }
        .info-label {
            font-weight: bold;
            width: 150px;
            color: #666;
        }
        .info-value {
            flex: 1;
            color: #333;
        }
        .specialty-section {
            margin-bottom: 30px;
            border: 1px solid #ddd;
            padding: 20px;
            background: #fafafa;
            border-radius: 8px;
        }
        .specialty-title {
            font-size: 16px;
            font-weight: bold;
            color: #0066cc;
            margin-bottom: 15px;
            border-bottom: 2px solid #0066cc;
            padding-bottom: 8px;
        }
        .evaluation-data {
            font-size: 11px;
            line-height: 1.8;
        }
        .evaluation-data p {
            margin-bottom: 10px;
        }
        .evaluation-data label {
            font-weight: bold;
            color: #555;
            display: inline-block;
            width: 120px;
        }
        .evaluation-data span {
            color: #333;
        }
        .notes-section {
            background: #fff3cd;
            padding: 12px;
            border-left: 4px solid #ffc107;
            margin-top: 15px;
            font-size: 11px;
            border-radius: 4px;
        }
        .notes-label {
            font-weight: bold;
            color: #856404;
            display: block;
            margin-bottom: 5px;
        }

        .notes-section p {
            margin: 0 0 5px 0;
            line-height: 1.5;
        }

        .notes-heading {
            font-weight: 900;
            text-transform: uppercase;
            margin: 8px 0 4px 0;
            color: #704c00;
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
            color: #704c00;
        }
        .specialist-info {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #999;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 11px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background: #e8f1ff;
            font-weight: bold;
            color: #0066cc;
        }
        .diagnosis {
            background: #e8f1ff;
            padding: 12px;
            margin: 10px 0;
            border-radius: 4px;
            font-weight: bold;
            color: #0066cc;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="container">
        {{-- Header --}}
        <div class="header">
            <div class="header-logo">IPS CREAR INTEGRAL S.A.S</div>
            <div class="header-title">EXÁMENES DE INGRESO ESCOLAR</div>
            <div class="header-subtitle">Reporte de Evaluación por Especialidad</div>
            <div class="header-subtitle">Realizado en: {{ now()->format('d/m/Y H:i') }}</div>
        </div>

        {{-- Patient Info --}}
        <div class="patient-info">
            <div class="info-row">
                <span class="info-label">Paciente:</span>
                <span class="info-value">{{ $exam->student->full_name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Identificación:</span>
                <span class="info-value">{{ $exam->student->document_type }}: {{ $exam->student->document_number }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Edad:</span>
                <span class="info-value">{{ $exam->student->age ?? 'N/A' }} años</span>
            </div>
            <div class="info-row">
                <span class="info-label">Grado:</span>
                <span class="info-value">{{ $exam->student->grade }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Fecha de Nacimiento:</span>
                <span class="info-value">{{ $exam->student->birth_date ? $exam->student->birth_date->format('d/m/Y') : 'N/A' }}</span>
            </div>
        </div>

        {{-- Specialty Evaluations (Ordered) --}}
        @php
            // Define exact priority order: audiometria first, then odontologia
            $orderPriority = [
                'audiometria' => 1,
                'audiometría' => 1,
                'odontologia' => 2,
                'odontología' => 2,
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

                    if (preg_match('/^([A-ZÁÉÍÓÚÑ]{2,25})\s*:\s*(.+)$/u', $trimmed, $matches)) {
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

            $orderedResults = $exam->results->sortBy(function($result) use ($orderPriority) {
                $areaNormalized = mb_strtolower(trim($result->area));
                return $orderPriority[$areaNormalized] ?? 999;
            })->values();
        @endphp
            <div class="specialty-section">
                <div class="specialty-title">
                    {{ ucfirst(str_replace('_', ' ', $result->area)) }}
                </div>

                <div class="evaluation-data">
                    @if(is_array($result->data) && count($result->data) > 0)
                        @foreach($result->data as $key => $value)
                            @if(!in_array($key, ['_token', 'odontograma_imagen']))
                                <p>
                                    <label>{{ ucfirst(str_replace('_', ' ', $key)) }}:</label>
                                    <span>
                                        @if(is_array($value))
                                            {{ implode(', ', $value) }}
                                        @elseif($key === 'odontograma_path' && !empty($value))
                                            <em>[Imagen adjunta]</em>
                                        @else
                                            {{ $value ?? 'N/A' }}
                                        @endif
                                    </span>
                                </p>
                            @endif
                        @endforeach
                    @else
                        <p><em>No hay datos de evaluación registrados</em></p>
                    @endif
                </div>

                @if(!empty($result->notes))
                    <div class="notes-section">
                        <span class="notes-label">Observaciones/Notas del Especialista:</span>
                        {!! $formatClinicalText($result->notes) !!}
                    </div>
                @endif

                <div class="specialist-info">
                    <strong>Especialista:</strong> {{ $result->specialist->name ?? 'No registrado' }}<br>
                    <strong>Fecha de Evaluación:</strong> {{ $result->created_at->format('d/m/Y H:i') }}<br>
                    <strong>Rol:</strong> {{ $result->specialist->role->name ?? 'N/A' }}
                </div>
            </div>
        @endforeach

        {{-- Footer --}}
        <div class="footer">
            <p>Documento generado automáticamente por el Sistema de Gestión Clínica IPS Crear Integral</p>
            <p>{{ date('d/m/Y H:i:s') }}</p>
        </div>
    </div>
</body>
</html>
