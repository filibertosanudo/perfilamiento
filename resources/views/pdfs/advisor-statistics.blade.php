@extends('pdfs.layout', [
    'title' => 'Reporte de Estadísticas',
    'institution' => $advisor->institution->name ?? 'Institución'
])

@push('styles')
<style>
    /* Estilos específicos para estadísticas */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin: 30px 0;
    }

    .stat-card {
        background: #F9FAFB;
        border: 1px solid #E5E7EB;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
    }

    .stat-value {
        font-size: 32pt;
        font-weight: bold;
        color: #0F766E;
        margin: 10px 0;
    }

    .stat-label {
        font-size: 11pt;
        color: #64748B;
        margin-bottom: 5px;
    }

    .stat-sublabel {
        font-size: 9pt;
        color: #9CA3AF;
    }

    .chart-section {
        margin: 40px 0;
        page-break-inside: avoid;
    }

    .chart-title {
        font-size: 14pt;
        color: #1F2937;
        font-weight: bold;
        margin-bottom: 20px;
        text-align: center;
    }

    .period-badge {
        display: inline-block;
        background: #0F766E;
        color: white;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 10pt;
        margin-left: 10px;
    }

    .legend {
        display: flex;
        justify-content: center;
        gap: 30px;
        margin-top: 15px;
        font-size: 10pt;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .legend-color {
        width: 15px;
        height: 15px;
        border-radius: 3px;
    }
</style>
@endpush

@section('content')
    <!-- Title -->
    <div class="title">
        <h1>Reporte de Estadísticas</h1>
        <p>
            {{ $advisor->first_name }} {{ $advisor->last_name }}
            <span class="period-badge">
                @if($period === 'month') Último Mes
                @elseif($period === 'quarter') Último Trimestre
                @elseif($period === 'semester') Último Semestre
                @else Último Año
                @endif
            </span>
        </p>
        <p style="font-size: 10pt; color: #9CA3AF; margin-top: 10px;">
            Generado el {{ $generated_at->format('d/m/Y H:i') }}
        </p>
    </div>

    <!-- KPI Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Tasa de Completado</div>
            <div class="stat-value">{{ $generalStats['completion_rate'] }}%</div>
            <div class="stat-sublabel">De tests asignados</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Tiempo Promedio</div>
            <div class="stat-value">{{ $generalStats['avg_time'] }}</div>
            <div class="stat-sublabel">Minutos por evaluación</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Evaluaciones Completadas</div>
            <div class="stat-value">{{ $generalStats['total_completed'] }}</div>
            <div class="stat-sublabel">+{{ $generalStats['this_month'] }} este mes</div>
        </div>
    </div>

    <!-- Chart 1: Participación por Grupo -->
    <div class="chart-section">
        <div class="chart-title">Participación por Grupo</div>
        
        @php
            $groups = $groupComparison->take(4)->values();
            if ($groups->isEmpty() || $groups->sum('users') == 0) {
                $groups = collect([['name' => 'Sin grupos', 'users' => 0, 'completed' => 0]]);
            }
            $maxVal = max(1, max(array_merge($groups->pluck('users')->toArray(), $groups->pluck('completed')->toArray())));
        @endphp
        
        @if($groups->sum('users') > 0)
        <div style="text-align: center;">
            <svg viewBox="0 0 500 360" style="max-width: 100%; height: auto;">
                @for($i = 0; $i <= 5; $i++)
                    @php
                        $val = ($maxVal / 5) * $i;
                        $y = 320 - (60 * $i);
                    @endphp
                    <line x1="60" y1="{{ $y }}" x2="490" y2="{{ $y }}" stroke="#E2E8F0" stroke-width="1" stroke-dasharray="3,3"/>
                    <text x="55" y="{{ $y + 4 }}" text-anchor="end" font-size="11" fill="#64748B">{{ round($val) }}</text>
                @endfor

                @php
                    $numGroups = $groups->count();
                    $totalWidth = 400;
                    $groupWidth = $totalWidth / $numGroups;
                    $barWidth = min(24, $groupWidth * 0.35);
                    $gap = 8;
                @endphp
                @foreach($groups as $idx => $group)
                    @php
                        $xCenter = 60 + ($groupWidth * $idx) + ($groupWidth / 2);
                        $x1 = $xCenter - $barWidth - ($gap / 2);
                        $x2 = $xCenter + ($gap / 2);
                        $h1 = ($group['users'] / $maxVal) * 300;
                        $h2 = ($group['completed'] / $maxVal) * 300;
                        $y1 = 320 - $h1;
                        $y2 = 320 - $h2;
                    @endphp
                    <rect x="{{ $x1 }}" y="{{ $y1 }}" width="{{ $barWidth }}" height="{{ $h1 }}" fill="#0F766E" rx="4"/>
                    <rect x="{{ $x2 }}" y="{{ $y2 }}" width="{{ $barWidth }}" height="{{ $h2 }}" fill="#0EA5E9" rx="4"/>
                    
                    @php $words = explode(' ', $group['name']); @endphp
                    @if(count($words) > 1)
                        <text x="{{ $xCenter }}" y="340" text-anchor="middle" font-size="10" fill="#64748B">{{ $words[0] }}</text>
                        <text x="{{ $xCenter }}" y="352" text-anchor="middle" font-size="10" fill="#64748B">{{ implode(' ', array_slice($words, 1)) }}</text>
                    @else
                        <text x="{{ $xCenter }}" y="340" text-anchor="middle" font-size="10" fill="#64748B">{{ $group['name'] }}</text>
                    @endif
                @endforeach

                <line x1="60" y1="20" x2="60" y2="320" stroke="#94A3B8" stroke-width="2"/>
                <line x1="60" y1="320" x2="490" y2="320" stroke="#94A3B8" stroke-width="2"/>
            </svg>
        </div>
        
        <div class="legend">
            <div class="legend-item">
                <div class="legend-color" style="background: #0F766E;"></div>
                <span>Usuarios</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background: #0EA5E9;"></div>
                <span>Tests Completados</span>
            </div>
        </div>
        @else
            <p style="text-align: center; color: #9CA3AF; padding: 40px;">No hay datos de grupos disponibles</p>
        @endif
    </div>

    <!-- Page Break -->
    <div class="page-break"></div>

    <!-- Chart 2: Distribución de Tests por Tipo -->
    <div class="chart-section">
        <div class="chart-title">Distribución de Tests por Tipo</div>
        
        @php
            $pieTests = $testStats->filter(fn($t) => $t['completed'] > 0)->map(function($t) {
                $colors = [
                    'GAD' => '#F59E0B', 
                    'Ansiedad' => '#F59E0B', 
                    'PHQ' => '#DC2626', 
                    'Depresión' => '#DC2626', 
                    'Rosenberg' => '#10B981', 
                    'Autoestima' => '#10B981',
                ];
                $color = '#6B7280';
                foreach($colors as $k => $c) {
                    if(str_contains($t['test'], $k)) { 
                        $color = $c; 
                        break; 
                    }
                }
                return ['name' => $t['test'], 'value' => $t['completed'], 'color' => $color];
            })->values();
            
            if ($pieTests->isEmpty()) {
                $pieTests = collect([['name' => 'Sin datos', 'value' => 1, 'color' => '#E5E7EB']]);
            }
            
            $pieTotal = $pieTests->sum('value');
            $cx = 100; $cy = 100; $r = 85; $startAngle = -M_PI/2; $slices = [];
            foreach($pieTests as $seg) {
                $angle = ($seg['value'] / $pieTotal) * 2 * M_PI;
                $mid = $startAngle + ($angle / 2);
                $x1 = $cx + $r * cos($startAngle); $y1 = $cy + $r * sin($startAngle);
                $end = $startAngle + $angle;
                $x2 = $cx + $r * cos($end); $y2 = $cy + $r * sin($end);
                $large = $angle > M_PI ? 1 : 0;
                $pct = round(($seg['value'] / $pieTotal) * 100);
                $lx = $cx + ($r * 0.68) * cos($mid); $ly = $cy + ($r * 0.68) * sin($mid);
                $slices[] = compact('x1','y1','x2','y2','large','pct','lx','ly','seg');
                $startAngle = $end;
            }
        @endphp
        
        <div style="text-align: center;">
            <svg viewBox="0 0 200 200" style="max-width: 400px; height: auto; display: inline-block;">
                @foreach($slices as $sl)
                    <path d="M {{ $cx }} {{ $cy }} L {{ $sl['x1'] }} {{ $sl['y1'] }} A {{ $r }} {{ $r }} 0 {{ $sl['large'] }} 1 {{ $sl['x2'] }} {{ $sl['y2'] }} Z" fill="{{ $sl['seg']['color'] }}"/>
                    @if($sl['pct'] >= 8)
                        <text x="{{ $sl['lx'] }}" y="{{ $sl['ly'] }}" text-anchor="middle" dominant-baseline="middle" font-size="12" fill="white" font-weight="700">{{ $sl['pct'] }}%</text>
                    @endif
                @endforeach
            </svg>
        </div>
        
        <div class="legend" style="flex-direction: column; align-items: center;">
            @foreach($pieTests as $seg)
                <div class="legend-item">
                    <div class="legend-color" style="background: {{ $seg['color'] }};"></div>
                    <span>{{ $seg['name'] }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Chart 3: Tendencia Completados vs Pendientes -->
    <div class="chart-section">
        <div class="chart-title">Tendencia de Completados vs Pendientes</div>
        
        @php
            $trends = [];
            foreach($monthlyTrends['labels'] as $i => $m) {
                $trends[] = [
                    'mes' => $m,
                    'completados' => $monthlyTrends['completed'][$i],
                    'pendientes' => $monthlyTrends['pending'][$i]
                ];
            }
            $tMax = max(1, max(array_merge($monthlyTrends['completed'], $monthlyTrends['pending'])));
        @endphp
        
        <div style="text-align: center;">
            <svg viewBox="0 0 600 360" style="max-width: 100%; height: auto;">
                @for($i = 0; $i <= 5; $i++)
                    @php $y = 320 - (60 * $i); $val = ($tMax / 5) * $i; @endphp
                    <line x1="50" y1="{{ $y }}" x2="590" y2="{{ $y }}" stroke="#E2E8F0" stroke-width="1" stroke-dasharray="3,3"/>
                    <text x="45" y="{{ $y + 4 }}" text-anchor="end" font-size="11" fill="#64748B">{{ round($val) }}</text>
                @endfor

                @php
                    $xSpacing = 540 / max(1, count($trends) - 1);
                    $compPts = [];
                    $pendPts = [];
                    foreach($trends as $idx => $t) {
                        $x = 50 + ($xSpacing * $idx);
                        $compPts[] = ['x' => $x, 'y' => 320 - (($t['completados'] / $tMax) * 300)];
                        $pendPts[] = ['x' => $x, 'y' => 320 - (($t['pendientes'] / $tMax) * 300)];
                    }
                    $compPath = implode(' ', array_map(fn($p) => $p['x'].','.$p['y'], $compPts));
                    $pendPath = implode(' ', array_map(fn($p) => $p['x'].','.$p['y'], $pendPts));
                @endphp
                
                <polyline points="{{ $compPath }}" fill="none" stroke="#10B981" stroke-width="2.5" stroke-linejoin="round" stroke-linecap="round"/>
                <polyline points="{{ $pendPath }}" fill="none" stroke="#F59E0B" stroke-width="2.5" stroke-linejoin="round" stroke-linecap="round"/>

                @foreach($compPts as $idx => $pt)
                    <circle cx="{{ $pt['x'] }}" cy="{{ $pt['y'] }}" r="4.5" fill="#10B981" stroke="white" stroke-width="2"/>
                    <circle cx="{{ $pendPts[$idx]['x'] }}" cy="{{ $pendPts[$idx]['y'] }}" r="4.5" fill="#F59E0B" stroke="white" stroke-width="2"/>
                    <text x="{{ $pt['x'] }}" y="342" text-anchor="middle" font-size="11" fill="#64748B">{{ $trends[$idx]['mes'] }}</text>
                @endforeach

                <line x1="50" y1="20" x2="50" y2="320" stroke="#94A3B8" stroke-width="2"/>
                <line x1="50" y1="320" x2="590" y2="320" stroke="#94A3B8" stroke-width="2"/>
            </svg>
        </div>
        
        <div class="legend">
            <div class="legend-item">
                <div class="legend-color" style="background: #10B981;"></div>
                <span>Completados</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background: #F59E0B;"></div>
                <span>Pendientes</span>
            </div>
        </div>
    </div>

    <!-- Page Break -->
    <div class="page-break"></div>

    <!-- Summary Table -->
    <div class="chart-section">
        <div class="chart-title">Resumen por Grupo</div>
        
        <table>
            <thead>
                <tr>
                    <th>Grupo</th>
                    <th style="text-align: center;">Usuarios</th>
                    <th style="text-align: center;">Tests Completados</th>
                    <th style="text-align: center;">Promedio</th>
                </tr>
            </thead>
            <tbody>
                @forelse($groupComparison as $group)
                    <tr>
                        <td>{{ $group['name'] }}</td>
                        <td style="text-align: center;">{{ $group['users'] }}</td>
                        <td style="text-align: center;">{{ $group['completed'] }}</td>
                        <td style="text-align: center; font-weight: bold; color: #0F766E;">{{ $group['average'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: #9CA3AF;">No hay grupos disponibles</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Notes -->
    <div class="disclaimer">
        <p><strong>Notas sobre los Datos:</strong></p>
        <ul style="margin: 10px 0 0 20px; line-height: 1.8;">
            <li>Los datos mostrados son agregados y anonimizados para proteger la confidencialidad</li>
            <li>Los reportes no incluyen información personal identificable</li>
            <li>Los indicadores representan niveles generales, no diagnósticos clínicos</li>
            <li>Los datos corresponden al período seleccionado y se generaron el {{ $generated_at->format('d/m/Y') }}</li>
        </ul>
    </div>
@endsection