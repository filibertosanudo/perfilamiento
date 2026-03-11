@extends('pdfs.layout', [
    'title' => 'Dashboard Global del Sistema',
    'institution' => 'Sistema Completo'
])

@push('styles')
<style>
    .metric-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 15px;
        margin: 20px 0;
    }

    .metric-card {
        background: linear-gradient(135deg, #F9FAFB 0%, #F3F4F6 100%);
        border: 1px solid #E5E7EB;
        border-radius: 8px;
        padding: 15px;
        text-align: center;
    }

    .metric-value {
        font-size: 28pt;
        font-weight: bold;
        color: #0F766E;
        margin: 8px 0;
    }

    .metric-label {
        font-size: 9pt;
        color: #64748B;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .metric-change {
        font-size: 8pt;
        margin-top: 5px;
    }

    .change-positive { color: #059669; }
    .change-negative { color: #DC2626; }

    .section-title {
        font-size: 14pt;
        font-weight: bold;
        color: #1F2937;
        margin: 30px 0 20px 0;
        padding-bottom: 10px;
        border-bottom: 2px solid #0F766E;
    }

    .area-card {
        background: white;
        border: 1px solid #E5E7EB;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
    }

    .area-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .area-name {
        font-size: 11pt;
        font-weight: bold;
        color: #1F2937;
    }

    .area-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 8pt;
        font-weight: bold;
    }

    .badge-high { background: #D1FAE5; color: #065F46; }
    .badge-medium { background: #FEF3C7; color: #92400E; }
    .badge-low { background: #FEE2E2; color: #991B1B; }

    .stats-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
        margin-top: 10px;
    }

    .stat-item {
        text-align: center;
        padding: 8px;
        background: #F9FAFB;
        border-radius: 6px;
    }

    .stat-number {
        font-size: 14pt;
        font-weight: bold;
        color: #0F766E;
    }

    .stat-label {
        font-size: 7pt;
        color: #64748B;
        margin-top: 3px;
    }
</style>
@endpush

@section('content')
    <!-- Title -->
    <div class="title">
        <h1>Dashboard Global del Sistema</h1>
        <p>Reporte Ejecutivo - {{ $period_label }}</p>
        <p style="font-size: 10pt; color: #9CA3AF; margin-top: 10px;">
            Generado el {{ now()->format('d/m/Y H:i') }}
        </p>
    </div>

    <!-- KPI Metrics -->
    <div class="metric-grid">
        <div class="metric-card">
            <div class="metric-label">Total Usuarios</div>
            <div class="metric-value">{{ number_format($metrics['total_users']) }}</div>
            <div class="metric-change change-positive">
                +{{ $metrics['users_growth'] }}% vs mes anterior
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-label">Tests Completados</div>
            <div class="metric-value">{{ number_format($metrics['total_tests']) }}</div>
            <div class="metric-change change-positive">
                +{{ $metrics['tests_growth'] }}% este mes
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-label">Tasa Completado</div>
            <div class="metric-value">{{ $metrics['completion_rate'] }}%</div>
            <div class="metric-change {{ $metrics['completion_trend'] >= 0 ? 'change-positive' : 'change-negative' }}">
                {{ $metrics['completion_trend'] >= 0 ? '+' : '' }}{{ $metrics['completion_trend'] }}% tendencia
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-label">Áreas Activas</div>
            <div class="metric-value">{{ $metrics['active_areas'] }}</div>
            <div class="metric-change">
                de {{ $metrics['total_areas'] }} registradas
            </div>
        </div>
    </div>

    <!-- Chart 1: Participación por Área -->
    <div class="section-title">Participación por Área</div>
    
    <?php
        $areas = collect($areaStats)->take(6);
        if ($areas->isEmpty()) {
            $areas = collect([['name' => 'Sin datos', 'users' => 0, 'tests' => 0]]);
        }
        $maxVal = max(1, $areas->max('users'), $areas->max('tests'));
    ?>
    
    <?php if($areas->sum('users') > 0): ?>
    <div style="text-align: center; margin-bottom: 30px;">
        <svg viewBox="0 0 600 360" style="max-width: 100%; height: auto;">
            <?php for($i = 0; $i <= 5; $i++): ?>
                <?php
                    $val = ($maxVal / 5) * $i;
                    $y = 320 - (60 * $i);
                ?>
                <line x1="80" y1="{{ $y }}" x2="590" y2="{{ $y }}" stroke="#E2E8F0" stroke-width="1" stroke-dasharray="3,3"/>
                <text x="75" y="{{ $y + 4 }}" text-anchor="end" font-size="11" fill="#64748B">{{ round($val) }}</text>
            <?php endfor; ?>

            <?php
                $numInst = $areas->count();
                $totalWidth = 480;
                $groupWidth = $totalWidth / $numInst;
                $barWidth = min(30, $groupWidth * 0.35);
                $gap = 10;
            ?>
            
            <?php foreach($areas as $idx => $inst): ?>
                <?php
                    $xCenter = 80 + ($groupWidth * $idx) + ($groupWidth / 2);
                    $x1 = $xCenter - $barWidth - ($gap / 2);
                    $x2 = $xCenter + ($gap / 2);
                    $h1 = ($inst['users'] / $maxVal) * 300;
                    $h2 = ($inst['tests'] / $maxVal) * 300;
                    $y1 = 320 - $h1;
                    $y2 = 320 - $h2;
                ?>
                <rect x="{{ $x1 }}" y="{{ $y1 }}" width="{{ $barWidth }}" height="{{ $h1 }}" fill="#0F766E" rx="4"/>
                <rect x="{{ $x2 }}" y="{{ $y2 }}" width="{{ $barWidth }}" height="{{ $h2 }}" fill="#0EA5E9" rx="4"/>
                
                <text x="{{ $xCenter }}" y="340" text-anchor="middle" font-size="9" fill="#64748B">
                    {{ \Illuminate\Support\Str::limit($inst['name'], 12) }}
                </text>
            <?php endforeach; ?>

            <line x1="80" y1="20" x2="80" y2="320" stroke="#94A3B8" stroke-width="2"/>
            <line x1="80" y1="320" x2="590" y2="320" stroke="#94A3B8" stroke-width="2"/>
        </svg>
        
        <div style="display: flex; justify-content: center; gap: 30px; margin-top: 15px; font-size: 10pt;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <div style="width: 15px; height: 15px; background: #0F766E; border-radius: 3px;"></div>
                <span>Usuarios</span>
            </div>
            <div style="display: flex; align-items: center; gap: 8px;">
                <div style="width: 15px; height: 15px; background: #0EA5E9; border-radius: 3px;"></div>
                <span>Tests Completados</span>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Page Break -->
    <div class="page-break"></div>

    <!-- Chart 2: Distribución de Tests por Tipo -->
    <div class="section-title">Distribución de Tests por Tipo</div>
    
    <?php
        $pieTests = collect($testDistribution)->filter(fn($t) => $t['count'] > 0);
        
        if ($pieTests->isEmpty()) {
            $pieTests = collect([['name' => 'Sin datos', 'count' => 1, 'color' => '#E5E7EB']]);
        }
        
        $pieTotal = $pieTests->sum('count');
        $cx = 100; $cy = 100; $r = 85; $startAngle = -M_PI/2; $slices = [];
        
        foreach($pieTests as $seg) {
            $angle = ($seg['count'] / $pieTotal) * 2 * M_PI;
            $mid = $startAngle + ($angle / 2);
            $x1 = $cx + $r * cos($startAngle); $y1 = $cy + $r * sin($startAngle);
            $end = $startAngle + $angle;
            $x2 = $cx + $r * cos($end); $y2 = $cy + $r * sin($end);
            $large = $angle > M_PI ? 1 : 0;
            $pct = round(($seg['count'] / $pieTotal) * 100);
            $lx = $cx + ($r * 0.68) * cos($mid); $ly = $cy + ($r * 0.68) * sin($mid);
            $slices[] = compact('x1','y1','x2','y2','large','pct','lx','ly','seg');
            $startAngle = $end;
        }
    ?>
    
    <div style="text-align: center; margin-bottom: 30px;">
        <svg viewBox="0 0 200 200" style="max-width: 400px; height: auto; display: inline-block;">
            <?php foreach($slices as $sl): ?>
                <path d="M {{ $cx }} {{ $cy }} L {{ $sl['x1'] }} {{ $sl['y1'] }} A {{ $r }} {{ $r }} 0 {{ $sl['large'] }} 1 {{ $sl['x2'] }} {{ $sl['y2'] }} Z" 
                      fill="{{ $sl['seg']['color'] }}"/>
                <?php if($sl['pct'] >= 8): ?>
                    <text x="{{ $sl['lx'] }}" y="{{ $sl['ly'] }}" text-anchor="middle" dominant-baseline="middle" 
                          font-size="12" fill="white" font-weight="700">{{ $sl['pct'] }}%</text>
                <?php endif; ?>
            <?php endforeach; ?>
        </svg>
        
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-top: 20px; font-size: 10pt;">
            <?php foreach($pieTests as $seg): ?>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <div style="width: 15px; height: 15px; background: {{ $seg['color'] }}; border-radius: 3px;"></div>
                    <span>{{ $seg['name'] }} ({{ $seg['count'] }})</span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Áreas Detalle -->
    <div class="page-break"></div>
    <div class="section-title">Detalle por Área</div>

    <?php foreach($areaDetails as $area): ?>
        <div class="area-card">
            <div class="area-header">
                <div class="area-name">{{ $area['name'] }}</div>
                <span class="area-badge badge-{{ $area['performance'] }}">
                    <?php if($area['performance'] === 'high'): ?>
                        Alto Rendimiento
                    <?php elseif($area['performance'] === 'medium'): ?>
                        Rendimiento Medio
                    <?php else: ?>
                        Necesita Atención
                    <?php endif; ?>
                </span>
            </div>
            
            <div class="stats-row">
                <div class="stat-item">
                    <div class="stat-number">{{ $area['users'] }}</div>
                    <div class="stat-label">Usuarios</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">{{ $area['advisors'] }}</div>
                    <div class="stat-label">Orientadores</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">{{ $area['groups'] }}</div>
                    <div class="stat-label">Grupos</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">{{ $area['completion_rate'] }}%</div>
                    <div class="stat-label">Tasa Completado</div>
                </div>
            </div>
            
            <?php if($area['notes']): ?>
                <p style="margin-top: 10px; font-size: 9pt; color: #64748B; font-style: italic;">
                    {{ $area['notes'] }}
                </p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

    <!-- Top Performers -->
    <div class="page-break"></div>
    <div class="section-title">Rendimiento Destacado</div>

    <table>
        <thead>
            <tr>
                <th style="text-align: center;">#</th>
                <th>Área</th>
                <th style="text-align: center;">Tests Completados</th>
                <th style="text-align: center;">Tasa Completado</th>
                <th style="text-align: center;">Promedio de Puntaje</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($topPerformers as $index => $performer): ?>
                <tr>
                    <td style="text-align: center; font-weight: bold; color: #0F766E;">{{ $index + 1 }}</td>
                    <td>{{ $performer['name'] }}</td>
                    <td style="text-align: center;">{{ $performer['tests_completed'] }}</td>
                    <td style="text-align: center; font-weight: bold; color: #0F766E;">{{ $performer['completion_rate'] }}%</td>
                    <td style="text-align: center;">{{ $performer['avg_score'] }}</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Recommendations -->
    <div class="section-title">Recomendaciones del Sistema</div>

    <div class="info-box">
        <h3>Áreas de Mejora Identificadas</h3>
        <ul style="margin-top: 10px; margin-left: 20px; line-height: 1.8; font-size: 10pt;">
            <?php foreach($recommendations as $recommendation): ?>
                <li>{{ $recommendation }}</li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Footer Note -->
    <div class="disclaimer">
        <p><strong>Nota:</strong> Este reporte proporciona una visión global del sistema. Los datos son agregados y anonimizados. Para información detallada de áreas específicas, genere reportes individuales. Los indicadores de rendimiento se calculan en base a la tasa de completación, participación activa y tiempo de respuesta promedio.</p>
    </div>
@endsection