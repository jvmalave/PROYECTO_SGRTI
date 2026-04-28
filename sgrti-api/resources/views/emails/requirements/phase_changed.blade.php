<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            line-height: 1.6;
        }

        .header {
            background-color: #003366;
            color: white;
            padding: 20px;
            text-align: center;
            border-bottom: 4px solid #cc0000;
        }

        .container {
            padding: 20px;
            border: 1px solid #ddd;
            margin-top: 10px;
        }

        .footer {
            font-size: 11px;
            color: #777;
            margin-top: 20px;
            text-align: center;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }

        .highlight {
            color: #003366;
            font-weight: bold;
        }

        .status-badge {
            background-color: #e8f4fd;
            color: #003366;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>SGRTI - CANTV</h2>
        <p>Gerencia de Planificación y Gestión TI</p>
    </div>

    <div class="container">
        <h3>Notificación de Cambio de Fase</h3>
        <p>Se informa que el requerimiento ha culminado la fase de <strong>Planificación (PL)</strong>
            satisfactoriamente.</p>

        <ul>
            <li><span class="highlight">Número RRTI:</span> {{ $requirement->numero_rrti }}</li>
            <li><span class="highlight">Tipo:</span> {{ $requirement->tipo_requerimiento }}</li>
            <li><span class="highlight">Nueva Fase:</span> <span class="status-badge">ATF - Análisis Técnico
                    Funcional</span></li>
        </ul>

        <p><strong>Descripción:</strong><br>
            {{ $requirement->descripcion_detallada }}</p>

        <p>La estimación de horas ha sido completada y el flujo ha avanzado de forma automática.</p>

        <br>
        <p><em>Este es un correo automático, por favor no responda a esta dirección.</em></p>
    </div>

    <div class="footer">
        &copy; {{ date('Y') }} CANTV - Corporación Anónima Nacional Teléfonos de Venezuela. <br>
        Sistema de Gestión de Requerimientos TI (SGRTI).
    </div>
</body>

</html>
