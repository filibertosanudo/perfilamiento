<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Reporte PDF' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12pt;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header */
        .header {
            border-bottom: 3px solid #0F766E;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 24pt;
            font-weight: bold;
            color: #0F766E;
        }

        .system-name {
            font-size: 10pt;
            color: #64748B;
            margin-top: 5px;
        }

        .header-info {
            text-align: right;
            font-size: 10pt;
            color: #64748B;
        }

        /* Title */
        .title {
            text-align: center;
            margin-bottom: 30px;
        }

        .title h1 {
            font-size: 20pt;
            color: #1F2937;
            margin-bottom: 10px;
        }

        .title p {
            font-size: 11pt;
            color: #64748B;
        }

        /* Content */
        .content {
            margin-bottom: 30px;
        }

        /* Footer */
        .footer {
            border-top: 1px solid #E5E7EB;
            padding-top: 20px;
            margin-top: 40px;
            text-align: center;
            font-size: 9pt;
            color: #9CA3AF;
        }

        /* Info Box */
        .info-box {
            background-color: #F0FDFA;
            border: 1px solid #0F766E;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .info-box h3 {
            color: #0F766E;
            font-size: 12pt;
            margin-bottom: 10px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px solid #E5E7EB;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: bold;
            color: #64748B;
        }

        .info-value {
            color: #1F2937;
        }

        /* Result Badge */
        .result-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 14pt;
            margin: 20px 0;
        }

        .badge-minimal { background-color: #D1FAE5; color: #065F46; }
        .badge-leve { background-color: #DBEAFE; color: #1E40AF; }
        .badge-moderada { background-color: #FEF3C7; color: #92400E; }
        .badge-severa { background-color: #FEE2E2; color: #991B1B; }
        .badge-normal { background-color: #D1FAE5; color: #065F46; }
        .badge-baja { background-color: #FEF3C7; color: #92400E; }
        .badge-alta { background-color: #D1FAE5; color: #065F46; }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #E5E7EB;
        }

        th {
            background-color: #F9FAFB;
            font-weight: bold;
            color: #374151;
        }

        /* Chart Placeholder */
        .chart-container {
            margin: 30px 0;
            text-align: center;
        }

        .chart-container svg {
            max-width: 100%;
            height: auto;
        }

        /* Disclaimer */
        .disclaimer {
            background-color: #FEF3C7;
            border-left: 4px solid #F59E0B;
            padding: 15px;
            margin-top: 30px;
            font-size: 10pt;
        }

        .disclaimer strong {
            color: #92400E;
        }

        /* Page Break */
        .page-break {
            page-break-after: always;
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="header-content">
            <div>
                <div class="logo">SIESI</div>
                <div class="system-name">Sistema de Perfilamiento de Bienestar Integral</div>
            </div>
            <div class="header-info">
                <div>{{ $area ?? 'Área' }}</div>
                <div>Generado: {{ now()->format('d/m/Y H:i') }}</div>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="container">
        @yield('content')
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Este documento es confidencial y está destinado únicamente para uso del destinatario autorizado.</p>
        <p>© {{ now()->year }} Sistema de Perfilamiento de Bienestar Integral</p>
    </div>
</body>
</html>