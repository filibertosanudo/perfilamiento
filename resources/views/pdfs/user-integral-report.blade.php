@extends('pdfs.layout', [
    'title' => 'Reporte de Bienestar Integral',
    'institution' => $user->area->name ?? 'Área'
])

@push('styles')
<style>
    .dimension-card {
        background: #F9FAFB;
        border: 1px solid #E5E7EB;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
    }

    .dimension-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .dimension-title {
        font-size: 12pt;
        font-weight: bold;
        color: #1F2937;
    }

    .dimension-score {
        font-size: 18pt;
        font-weight: bold;
        color: #0F766E;
    }

    .dimension-status {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 9pt;
        font-weight: bold;
        margin-top: 5px;
    }

    .status-good { background: #D1FAE5; color: #065F46; }
    .status-moderate { background: #FEF3C7; color: #92400E; }
    .status-concern { background: #FEE2E2; color: #991B1B; }

    .summary-box {
        background: linear-gradient(135deg, #0F766E 0%, #14B8A6 100%);
        color: white;
        border-radius: 12px;
        padding: 20px;
        margin: 20px 0;
    }

    .action-item {
        background: #FEF3C7;
        border-left: 4px solid #F59E0B;
        padding: 12px;
        margin: 10px 0;
        border-radius: 4px;
    }
</style>
@endpush

@section('content')
    <!-- Title -->
    <div class="title">
        <h1>Reporte de Bienestar Integral</h1>
        <p>{{ $user->first_name }} {{ $user->last_name }} {{ $user->second_last_name }}</p>
        <p style="font-size: 10pt; color: #9CA3AF; margin-top: 10px;">
            Período: {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}
        </p>
    </div>

    <!-- Summary Box -->
    <div class="summary-box">
        <h2 style="margin-bottom: 15px; font-size: 16pt;">Resumen Ejecutivo</h2>
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
            <div style="text-align: center;">
                <div style="font-size: 28pt; font-weight: bold;">{{ $totalTests }}</div>
                <div style="font-size: 10pt; opacity: 0.9;">Evaluaciones Completadas</div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 28pt; font-weight: bold;">{{ $areasEvaluated }}</div>
                <div style="font-size: 10pt; opacity: 0.9;">Áreas Evaluadas</div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 28pt; font-weight: bold;">
                    @if($overallStatus === 'good') 😊
                    @elseif($overallStatus === 'moderate') 😐
                    @else 😟
                    @endif
                </div>
                <div style="font-size: 10pt; opacity: 0.9;">Estado General</div>
            </div>
        </div>
    </div>

    <!-- Radar Chart -->
    <div style="text-align: center; margin: 30px 0;">
        <h3 style="color: #1F2937; margin-bottom: 20px;">Perfil Multidimensional</h3>
        
        @php
            $dimensions = count($dimensionData);
            if ($dimensions > 0) {
                $angleStep = (2 * M_PI) / $dimensions;
                $centerX = 200;
                $centerY = 200;
                $maxRadius = 150;
                
                $points = [];
                $labels = [];
                
                foreach ($dimensionData as $index => $dim) {
                    $angle = $index * $angleStep - (M_PI / 2);
                    $normalizedScore = $dim['score'] / $dim['max'];
                    $radius = $normalizedScore * $maxRadius;
                    
                    $x = $centerX + ($radius * cos($angle));
                    $y = $centerY + ($radius * sin($angle));
                    $points[] = [$x, $y];
                    
                    $labelX = $centerX + (($maxRadius + 40) * cos($angle));
                    $labelY = $centerY + (($maxRadius + 40) * sin($angle));
                    $labels[] = ['x' => $labelX, 'y' => $labelY, 'text' => $dim['name']];
                }
                
                $pointsStr = implode(' ', array_map(fn($p) => $p[0] . ',' . $p[1], $points));
            }
        @endphp
        
        @if($dimensions > 0)
        <svg viewBox="0 0 400 450" style="max-width: 500px; height: auto;">
            <!-- Background circles -->
            @for($i = 1; $i <= 5; $i++)
                <circle cx="{{ $centerX }}" cy="{{ $centerY }}" r="{{ ($maxRadius / 5) * $i }}" 
                        fill="none" stroke="#E5E7EB" stroke-width="1"/>
            @endfor
            
            <!-- Axis lines -->
            @foreach($dimensionData as $index => $dim)
                @php
                    $angle = $index * $angleStep - (M_PI / 2);
                    $endX = $centerX + ($maxRadius * cos($angle));
                    $endY = $centerY + ($maxRadius * sin($angle));
                @endphp
                <line x1="{{ $centerX }}" y1="{{ $centerY }}" 
                      x2="{{ $endX }}" y2="{{ $endY }}" 
                      stroke="#E5E7EB" stroke-width="1"/>
            @endforeach
            
            <!-- Data polygon -->
            <polygon points="{{ $pointsStr }}" 
                     fill="rgba(15, 118, 110, 0.3)" 
                     stroke="#0F766E" 
                     stroke-width="2"/>
            
            <!-- Data points -->
            @foreach($points as $point)
                <circle cx="{{ $point[0] }}" cy="{{ $point[1] }}" r="5" fill="#0F766E"/>
            @endforeach
            
            <!-- Labels -->
            @foreach($labels as $label)
                <text x="{{ $label['x'] }}" y="{{ $label['y'] }}" 
                      text-anchor="middle" font-size="10" fill="#374151" font-weight="bold">
                    {{ $label['text'] }}
                </text>
            @endforeach
        </svg>
        @endif
    </div>

    <!-- Page Break -->
    <div class="page-break"></div>

    <!-- Dimensions Detail -->
    <h3 style="color: #1F2937; margin-top: 30px; margin-bottom: 20px;">Análisis por Dimensión</h3>

    @foreach($dimensionData as $dimension)
        <div class="dimension-card">
            <div class="dimension-header">
                <div>
                    <div class="dimension-title">{{ $dimension['name'] }}</div>
                    <span class="dimension-status status-{{ $dimension['status'] }}">
                        @if($dimension['status'] === 'good') Óptimo
                        @elseif($dimension['status'] === 'moderate') Atención
                        @else Preocupante
                        @endif
                    </span>
                </div>
                <div class="dimension-score">{{ $dimension['score'] }}/{{ $dimension['max'] }}</div>
            </div>
            
            <div style="margin-top: 10px;">
                <div style="background: #E5E7EB; height: 12px; border-radius: 6px; overflow: hidden;">
                    @php
                        $percentage = ($dimension['score'] / $dimension['max']) * 100;
                        $color = $dimension['status'] === 'good' ? '#10B981' : 
                                ($dimension['status'] === 'moderate' ? '#F59E0B' : '#DC2626');
                    @endphp
                    <div style="width: {{ $percentage }}%; height: 100%; background: {{ $color }};"></div>
                </div>
            </div>
            
            <p style="font-size: 10pt; color: #64748B; margin-top: 10px; line-height: 1.6;">
                {{ $dimension['interpretation'] }}
            </p>
        </div>
    @endforeach

    <!-- Recommendations -->
    <div class="page-break"></div>
    
    <h3 style="color: #1F2937; margin-top: 30px; margin-bottom: 20px;">Plan de Acción Recomendado</h3>

    @foreach($recommendations as $recommendation)
        <div class="action-item">
            <h4 style="color: #92400E; margin-bottom: 8px; font-size: 11pt;">{{ $recommendation['area'] }}</h4>
            <p style="font-size: 10pt; line-height: 1.6;">{{ $recommendation['action'] }}</p>
        </div>
    @endforeach

    <!-- Resources -->
    <div class="info-box" style="margin-top: 30px;">
        <h3>Recursos de Apoyo</h3>
        <ul style="margin-top: 10px; margin-left: 20px; line-height: 1.8; font-size: 10pt;">
            <li>Servicio de Orientación Psicológica: {{ $user->area->name ?? 'Contactar área' }}</li>
            <li>Línea Nacional de Prevención del Suicidio: 800-273-8255</li>
            <li>Chat de Crisis: Envía "HOLA" al 741741</li>
            <li>Directorio de profesionales de salud mental en tu área</li>
        </ul>
    </div>

    <!-- Disclaimer -->
    <div class="disclaimer">
        <p><strong>Importante:</strong> Este reporte integra los resultados de múltiples evaluaciones para proporcionar una visión holística de tu bienestar. No constituye un diagnóstico clínico y debe ser interpretado como una herramienta de autoconocimiento y orientación. Si experimentas malestar significativo en cualquiera de las áreas evaluadas, te recomendamos encarecidamente buscar apoyo profesional de un psicólogo o consejero certificado.</p>
    </div>
@endsection