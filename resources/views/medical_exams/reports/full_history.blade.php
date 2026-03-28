<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte Médico - {{ $exam->student->document_number }}</title>
    <style>
        @page { margin: 1.5cm; }
        body { font-family: 'Helvetica', sans-serif; font-size: 11px; color: #1a202c; line-height: 1.4; }
        .header-table { width: 100%; border-bottom: 2px solid #2d3748; margin-bottom: 20px; }
        .logo { width: 120px; }
        .title { text-align: right; text-transform: uppercase; }

        .section-title { background: #edf2f7; padding: 6px 10px; font-weight: bold; margin-top: 20px; border-left: 4px solid #2b6cb0; font-size: 12px; }

        .data-table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        .data-table td { padding: 6px; border: 1px solid #e2e8f0; vertical-align: top; }
        .label { font-weight: bold; color: #4a5568; width: 30%; }

        .status-badge { padding: 2px 8px; border-radius: 4px; font-size: 9px; font-weight: bold; text-transform: uppercase; }
        .completed { background: #c6f6d5; color: #22543d; }
        .pending { background: #fed7d7; color: #822727; }

        .odontograma-container { text-align: center; margin-top: 10px; border: 1px solid #e2e8f0; padding: 10px; border-radius: 8px; }
        .odontograma-img { max-width: 200px; height: auto; }

        .footer { position: fixed; bottom: -30px; left: 0; right: 0; text-align: center; font-size: 9px; color: #a0aec0; }
    </style>
</head>
<body>
    {{-- Encabezado --}}
    <table class="header-table">
        <tr>
            <td>
                <img src="{{ public_path('LOGIN.png') }}" class="logo">
            </td>
            <td class="title">
                <h2 style="margin:0;">{{ $title }}</h2>
                <p style="margin:0;">I.P.S CREAR INTEGRAL - SNAKEDEV</p>
            </td>
        </tr>
    </table>

    {{-- Información del Estudiante --}}
    <div class="section-title">INFORMACIÓN DEL PACIENTE</div>
    <table class="data-table">
        <tr>
            <td class="label">Nombre Completo:</td>
            <td>{{ $exam->student->first_name }} {{ $exam->student->last_name }}</td>
            <td class="label">Documento:</td>
            <td>{{ $exam->student->document_number }}</td>
        </tr>
        <tr>
            <td class="label">Fecha de Solicitud:</td>
            <td>{{ $exam->created_at->format('d/m/Y') }}</td>
            <td class="label">Estado del Circuito:</td>
            <td>
                <span class="status-badge {{ $exam->status == 'completado' ? 'completed' : 'pending' }}">
                    {{ $exam->status }}
                </span>
            </td>
        </tr>
    </table>

    {{-- Detalle por Área --}}
    <div class="section-title">VALORACIONES MÉDICAS</div>

    @foreach($exam->requested_areas as $areaName)
        @php
            $slug = Str::slug($areaName, '_');
            $resultado = $exam->results->where('area', $slug)->first();
        @endphp

        <div style="margin-top: 15px; page-break-inside: avoid;">
            <div style="border-bottom: 1px solid #cbd5e0; padding-bottom: 4px; margin-bottom: 8px;">
                <strong style="font-size: 11px; color: #2b6cb0;">{{ strtoupper($areaName) }}</strong>
                @if(!$resultado) <span class="status-badge pending" style="float: right;">Faltante</span> @endif
            </div>

            @if($resultado)
                <table class="data-table">
                    @foreach($resultado->data as $campo => $valor)
                        @if(!is_array($valor) && $campo !== 'odontograma_path')
                            <tr>
                                <td class="label">{{ ucfirst(str_replace('_', ' ', $campo)) }}:</td>
                                <td>{{ $valor ?: 'N/A' }}</td>
                            </tr>
                        @endif
                    @endforeach
                </table>

                {{-- Caso especial: Odontograma --}}
                @if(isset($resultado->data['odontograma_path']))
                    <div class="odontograma-container">
                        <p><strong>ODONTOGRAMA REGISTRADO:</strong></p>
                        <img src="{{ storage_path('app/public/' . $resultado->data['odontograma_path']) }}" class="odontograma-img">
                    </div>
                @endif

                <div style="margin-top: 5px; background: #f7fafc; padding: 8px; border: 1px solid #e2e8f0;">
                    <strong>Observaciones:</strong> {{ $resultado->notes }} <br>
                    <small>Realizado por: {{ $resultado->specialist->name }} el {{ $resultado->created_at->format('d/m/Y h:i A') }}</small>
                </div>
            @else
                <div style="padding: 10px; color: #a0aec0; border: 1px dashed #cbd5e0; text-align: center;">
                    Este examen aún no ha sido reportado por el especialista.
                </div>
            @endif
        </div>
    @endforeach

    <div class="footer">
        Cali, Valle del Cauca - Reporte generado por SnakeDEV System v2.0 - {{ $date }}
    </div>
</body>
</html>
