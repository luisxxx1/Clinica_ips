<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        /* Optimizamos para DomPDF */
        @page { margin: 0.3cm; }

        body {
            font-family: 'Helvetica', Arial, sans-serif;
            font-size: 9px;
            line-height: 1.3;
            padding: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 8px;
        .brand-name {
            color: #1e3a8a;
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }

        .nit { font-size: 9px; color: #64748b; font-weight: bold; margin-bottom: 8px; }

        .title {
            font-weight: bold;
            font-size: 13px;
            text-transform: uppercase;
            background-color: #f1f5f9;
            display: inline-block;
            padding: 4px 20px;
            border-radius: 4px;
            color: #1e293b;
        }

        .section-title {
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 10px 0 5px;
        /* Contenedor del Odontograma */
        .odontograma-container {
            width: 100%;
            text-align: center;
            margin: 2px 0;
            border: none;
            padding: 2px;
            border-radius: 0;
            background-color: transparent;
        }

        .odontograma-img {
            width: 15%; /* Mínimo */
            max-height: 50px;
            display: block;
            margin: 0 auto;
        }

        /* Tabla de Información */
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        .info-table td {
            padding: 3px 5px;
            border: 1px solid #f1f5f9;
        }
        .label { font-weight: bold; color: #64748b; text-transform: uppercase; font-size: 8px; }
        .value { font-weight: bold; color: #1e293b; font-size: 10px; }

        .habitos-grid {
            margin: 10px 0;
            width: 100%;
        }

        .habito-item {
            display: inline-block;
            width: 30%;
            margin-bottom: 5px;
            font-size: 9px;
        }

        .observations {
            margin-top: 25px;
            padding: 15px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background-color: #f8fafc;
        }

        .footer {
            position: fixed;
            bottom: -10px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="brand-name">I.P.S CREAR INTEGRAL S.A.S</div>
        <div class="nit">NIT 900.727.545-8</div>
        <div class="title">TAMIZ VISUAL Y ODONTOLÓGICO</div>
    </div>

    <table class="info-table">
        <tr>
            <td width="50%">
                <span class="label">Paciente:</span><br>
                <span class="value">{{ $student->full_name }}</span>
            </td>
            <td width="50%">
                <span class="label">Documento:</span><br>
                <span class="value">{{ $student->document_number }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="label">Fecha de Evaluación:</span><br>
                <span class="value">{{ $date->format('d/m/Y h:i A') }}</span>
            </td>
            <td>
                <span class="label">Sede de Atención:</span><br>
                <span class="value">Jamundí (Valle del Cauca)</span>
            </td>
        </tr>
    </table>

    <div class="section-title">Registro Gráfico: Odontograma</div>

    <div class="odontograma-container">
        @if($odontograma_img)
            <img src="{{ $odontograma_img }}" class="odontograma-img">
        @else
            <div style="padding: 60px 0; color: #cbd5e1; font-style: italic;">
                No se adjuntó captura visual del registro odontológico.
            </div>
        @endif
    </div>

    @if(!empty($habitos))
        <div class="section-title">Hábitos y Hallazgos Registrados</div>
        <div class="habitos-grid">
            @foreach($habitos as $habito)
                <div class="habito-item">
                    <span style="color: #2563eb;">•</span> {{ ucfirst(str_replace('_', ' ', $habito)) }}
                </div>
            @endforeach
        </div>
    @endif

    <div class="observations">
        <div class="label" style="margin-bottom: 5px; font-size: 9px; color: #1e3a8a;">Observaciones y Recomendaciones Clínicas:</div>
        <p style="margin: 0; font-size: 10px; color: #334155; line-height: 1.6;">
            {{ $notes ?? 'Paciente en óptimas condiciones de salud oral al momento de la evaluación. Se recomienda mantener higiene constante.' }}
        </p>
    </div>

    <div class="footer">
        Carrera 12 No 13-24 / Barrio Simón Bolívar - Jamundí (Valle) <br>
        Contacto: 316 185 57 27 | Correo: administrativo@crearintegral.com <br>
        <strong>Snake_DEV Health System</strong> - Generado de forma electrónica.
    </div>
</body>
</html>
