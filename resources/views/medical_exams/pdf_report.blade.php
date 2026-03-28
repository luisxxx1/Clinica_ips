<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        /* Configuración de página para DomPDF */
        @page { margin: 1.5cm 1cm; }

        body {
            font-family: 'Helvetica', Arial, sans-serif;
            color: #1e293b;
            margin: 0;
            padding: 0;
            line-height: 1.4;
            font-size: 10px;
        }

        /* Encabezado Corporativo */
        .header {
            background: #2563eb;
            color: white;
            padding: 25px;
            text-align: center;
            border-bottom: 4px solid #1e3a8a;
        }

        .content { padding: 20px 30px; }

        .section-title {
            font-size: 11px;
            font-weight: bold;
            background: #f8fafc;
            padding: 6px 10px;
            border-left: 4px solid #2563eb;
            margin: 15px 0 8px;
            color: #1e40af;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; table-layout: fixed; }
        th, td { border: 1px solid #e2e8f0; padding: 8px 10px; text-align: left; word-wrap: break-word; }

        th {
            background: #f1f5f9;
            font-weight: bold;
            color: #64748b;
            width: 30%;
            text-transform: uppercase;
            font-size: 8px;
        }

        .badge {
            background: #dbeafe;
            color: #1e40af;
            padding: 3px 8px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
        }

        /* Estilo para el Odontograma */
        .odontograma-container {
            text-align: center;
            padding: 15px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            border-radius: 8px;
            margin-top: 10px;
        }

        .odontograma-img {
            width: 60%;
            max-width: 250px;
            height: auto;
        }

        .signature-section { margin-top: 60px; text-align: center; }
        .signature-line { border-top: 1px solid #94a3b8; width: 250px; margin: 0 auto; padding-top: 8px; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 8px; color: #94a3b8; border-top: 1px solid #f1f5f9; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0; font-size: 18px; letter-spacing: 1px;">I.P.S CREAR INTEGRAL S.A.S</h1>
        <p style="margin: 4px 0 0; font-size: 10px; opacity: 0.9; font-weight: bold; text-transform: uppercase;">
            Historia Clínica: {{ $exam->area ?? 'Valoración Médica Integral' }}
        </p>
    </div>

    <div class="content">
        <div class="section-title">Identificación del Paciente</div>
        <table>
            <tr>
                <th>Nombre Completo:</th>
                <td style="font-weight: bold; font-size: 11px;">{{ $student->first_name }} {{ $student->last_name }}</td>
            </tr>
            <tr>
                <th>Documento de Identidad:</th>
                <td>{{ $student->document_type }} {{ $student->document_number }}</td>
            </tr>
        </table>

        {{-- Lógica para Odontología --}}
        @if(isset($data['odontograma_path']))
            <div class="section-title">Registro de Odontograma</div>
            <div class="odontograma-container">
                <p style="font-size: 9px; font-weight: bold; color: #64748b; margin-bottom: 10px; text-transform: uppercase;">Vista Gráfica Dental</p>
                {{-- USAR public_path ES OBLIGATORIO PARA DomPDF --}}
                <img src="{{ public_path('storage/' . $data['odontograma_path']) }}" class="odontograma-img">
            </div>

            <div class="section-title">Observaciones Odontológicas</div>
            <table>
                <tr>
                    <th>Hallazgos Clínicos:</th>
                    <td>{{ $data['observaciones'] ?? 'Sin observaciones adicionales.' }}</td>
                </tr>
            </table>
        @endif

        {{-- Lógica para Valoración Médica (si los datos existen) --}}
        @if(isset($data['peso']))
            <div class="section-title">Parámetros Antropométricos</div>
            <table>
                <tr>
                    <th>Peso:</th><td>{{ $data['peso'] }} kg</td>
                    <th>Talla:</th><td>{{ $data['talla'] }} cm</td>
                </tr>
                <tr>
                    <th>IMC:</th><td>{{ $data['imc'] }}</td>
                    <th>Estado:</th><td><span class="badge">{{ $data['imc_status'] }}</span></td>
                </tr>
            </table>

            {{-- Aquí puedes mantener el resto de tus secciones de Antecedentes y Hallazgos --}}
        @endif

        <div class="signature-section">
            <div class="signature-line">
                <p style="margin: 0; font-size: 10px; font-weight: bold;">DR. {{ strtoupper($doctor->name ?? auth()->user()->name) }}</p>
                <p style="margin: 2px 0; font-size: 8px; color: #64748b;">REGISTRO MÉDICO PROFESIONAL</p>
                <p style="margin: 0; font-size: 8px; color: #64748b;">I.P.S CREAR INTEGRAL S.A.S</p>
            </div>
        </div>
    </div>

    <div class="footer">
        Generado electrónicamente por <strong>Snake_DEV Health System</strong> | Jamundí, Valle del Cauca.
    </div>
</body>
</html>
