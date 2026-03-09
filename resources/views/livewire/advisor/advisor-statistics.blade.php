<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Reportes y Estadísticas</h1>
            <p class="text-gray-500">Análisis agregado de datos de tus grupos</p>
        </div>
        <div class="flex gap-3 flex-wrap">
            <select wire:model.live="period"
                class="w-[140px] border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                <option value="month">Último mes</option>
                <option value="quarter">Último trimestre</option>
                <option value="semester">Último semestre</option>
                <option value="year">Último año</option>
            </select>
        </div>
    </div>

    {{-- Two Charts Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        {{-- Participación por Grupo (Bar Chart) --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-base font-semibold text-gray-900">Participación por Grupo</h3>
            </div>
            <div class="p-6">
                @php
                    $groups = $groupComparison->take(4)->values();
                    if ($groups->isEmpty() || $groups->sum('users') == 0) {
                        $groups = collect([
                            ['name' => 'Sin grupos', 'users' => 0, 'completed' => 0],
                        ]);
                    }
                    $maxVal = max(1, max(array_merge($groups->pluck('users')->toArray(), $groups->pluck('completed')->toArray())));
                @endphp
                
                @if($groups->sum('users') > 0)
                    <div style="height: 300px; position: relative;">
                        <svg viewBox="0 0 500 360" style="width: 100%; height: 100%;">
                            {{-- Grid lines & Y labels --}}
                            @for($i = 0; $i <= 5; $i++)
                                @php
                                    $val = ($maxVal / 5) * $i;
                                    $y = 320 - (60 * $i);
                                @endphp
                                <line x1="60" y1="{{ $y }}" x2="490" y2="{{ $y }}" stroke="#E2E8F0" stroke-width="1" stroke-dasharray="3,3"/>
                                <text x="55" y="{{ $y + 4 }}" text-anchor="end" font-size="11" fill="#64748B">{{ round($val) }}</text>
                            @endfor

                            {{-- Bars --}}
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
                                
                                {{-- X labels --}}
                                @php $words = explode(' ', $group['name']); @endphp
                                @if(count($words) > 1)
                                    <text x="{{ $xCenter }}" y="340" text-anchor="middle" font-size="10" fill="#64748B">{{ $words[0] }}</text>
                                    <text x="{{ $xCenter }}" y="352" text-anchor="middle" font-size="10" fill="#64748B">{{ implode(' ', array_slice($words, 1)) }}</text>
                                @else
                                    <text x="{{ $xCenter }}" y="340" text-anchor="middle" font-size="10" fill="#64748B">{{ $group['name'] }}</text>
                                @endif
                            @endforeach

                            {{-- Axes --}}
                            <line x1="60" y1="20" x2="60" y2="320" stroke="#94A3B8" stroke-width="2"/>
                            <line x1="60" y1="320" x2="490" y2="320" stroke="#94A3B8" stroke-width="2"/>
                        </svg>
                    </div>
                    <div class="flex items-center justify-center gap-6 mt-4 text-xs">
                        <span class="flex items-center gap-2"><span class="w-3 h-3 bg-teal-700 rounded-sm"></span>Usuarios</span>
                        <span class="flex items-center gap-2"><span class="w-3 h-3 bg-sky-500 rounded-sm"></span>Tests</span>
                    </div>
                @else
                    <div class="text-center py-12 text-gray-400">
                        <p class="text-sm">No hay datos de grupos disponibles</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Distribución de Tests por Tipo (Pie Chart) --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-base font-semibold text-gray-900">Distribución de Tests por Tipo</h3>
            </div>
            <div class="p-6">
                @php
                    $pieTests = $testStats->filter(fn($t) => $t['completed'] > 0)->map(function($t) {
                        $colors = [
                            'GAD' => '#F59E0B', 
                            'Ansiedad' => '#F59E0B', 
                            'PHQ' => '#DC2626', 
                            'Depresión' => '#DC2626', 
                            'Rosenberg' => '#10B981', 
                            'Autoestima' => '#10B981',
                            'Inteligencia' => '#0EA5E9',
                            'Emocional' => '#0EA5E9',
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
                        $pieTests = collect([
                            ['name' => 'Sin datos', 'value' => 1, 'color' => '#E5E7EB'],
                        ]);
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
                <div class="flex flex-col items-center">
                    <svg viewBox="0 0 200 200" class="w-64 h-64">
                        @foreach($slices as $sl)
                            <path d="M {{ $cx }} {{ $cy }} L {{ $sl['x1'] }} {{ $sl['y1'] }} A {{ $r }} {{ $r }} 0 {{ $sl['large'] }} 1 {{ $sl['x2'] }} {{ $sl['y2'] }} Z" fill="{{ $sl['seg']['color'] }}"/>
                            @if($sl['pct'] >= 8)
                                <text x="{{ $sl['lx'] }}" y="{{ $sl['ly'] }}" text-anchor="middle" dominant-baseline="middle" font-size="12" fill="white" font-weight="700">{{ $sl['pct'] }}%</text>
                            @endif
                        @endforeach
                    </svg>
                    <div class="grid grid-cols-1 gap-y-2 mt-4 text-xs w-full max-w-sm">
                        @foreach($pieTests as $seg)
                            <span class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-sm flex-shrink-0" style="background:{{ $seg['color'] }}"></span>
                                <span class="truncate">{{ $seg['name'] }}</span>
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Distribución de Niveles por Categoría (Grouped Bar) --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-base font-semibold text-gray-900">Distribución de Niveles por Categoría</h3>
        </div>
        <div class="p-6">
            @php
                $allLevels = ['Normal' => 0, 'Leve' => 0, 'Moderado' => 0, 'Alto' => 0];
                
                foreach($levelDistribution as $category => $count) {
                    if (in_array($category, ['mínima', 'normal'])) {
                        $allLevels['Normal'] += $count;
                    } elseif ($category === 'leve') {
                        $allLevels['Leve'] += $count;
                    } elseif (in_array($category, ['moderada', 'baja'])) {
                        $allLevels['Moderado'] += $count;
                    } elseif (in_array($category, ['severa', 'alta'])) {
                        $allLevels['Alto'] += $count;
                    }
                }

                $totalResponses = array_sum($allLevels);
                $levelsData = [];
                
                if ($totalResponses > 0) {
                    foreach($allLevels as $nivel => $count) {
                        $percentage = ($count / $totalResponses) * 100;
                        $levelsData[] = [
                            'nivel' => $nivel,
                            'values' => [$percentage, $percentage, $percentage, $percentage]
                        ];
                    }
                } else {
                    $levelsData = [
                        ['nivel' => 'Normal', 'values' => [0, 0, 0, 0]],
                        ['nivel' => 'Leve', 'values' => [0, 0, 0, 0]],
                        ['nivel' => 'Moderado', 'values' => [0, 0, 0, 0]],
                        ['nivel' => 'Alto', 'values' => [0, 0, 0, 0]],
                    ];
                }

                $catCols = ['#DC2626', '#F59E0B', '#10B981', '#0EA5E9'];
                $catLabels = ['Depresión', 'Ansiedad', 'Autoestima', 'Int. Emocional'];
            @endphp
            
            <div style="height: 350px; position: relative;">
                <svg viewBox="0 0 600 410" style="width: 100%; height: 100%;">
                    {{-- Y label --}}
                    <text x="15" y="195" text-anchor="middle" font-size="10" fill="#64748B" transform="rotate(-90, 15, 195)">Porcentaje (%)</text>
                    
                    {{-- Grid --}}
                    @for($i = 0; $i <= 5; $i++)
                        @php $yL = 370 - (70 * $i); $vL = 20 * $i; @endphp
                        <line x1="60" y1="{{ $yL }}" x2="590" y2="{{ $yL }}" stroke="#E2E8F0" stroke-width="1" stroke-dasharray="3,3"/>
                        <text x="55" y="{{ $yL + 4 }}" text-anchor="end" font-size="11" fill="#64748B">{{ $vL }}</text>
                    @endfor

                    {{-- Bars --}}
                    @php
                        $groupWidth = 120;
                        $barWidth = 18;
                        $barGap = 6;
                    @endphp
                    @foreach($levelsData as $gi => $grp)
                        @foreach($grp['values'] as $ci => $val)
                            @php
                                $h = ($val / 100) * 350;
                                $x = 80 + ($gi * $groupWidth) + ($ci * ($barWidth + $barGap));
                                $y = 370 - $h;
                            @endphp
                            <rect x="{{ $x }}" y="{{ $y }}" width="{{ $barWidth }}" height="{{ $h }}" fill="{{ $catCols[$ci] }}" rx="3"/>
                        @endforeach
                        <text x="{{ 80 + ($gi * $groupWidth) + 40 }}" y="390" text-anchor="middle" font-size="11" fill="#64748B">{{ $grp['nivel'] }}</text>
                    @endforeach

                    {{-- Axes --}}
                    <line x1="60" y1="20" x2="60" y2="370" stroke="#94A3B8" stroke-width="2"/>
                    <line x1="60" y1="370" x2="590" y2="370" stroke="#94A3B8" stroke-width="2"/>
                </svg>
            </div>
            <div class="flex flex-wrap items-center justify-center gap-6 mt-4 text-xs">
                @foreach($catLabels as $i => $label)
                    <span class="flex items-center gap-2"><span class="w-3 h-3 rounded-sm" style="background:{{ $catCols[$i] }}"></span>{{ $label }}</span>
                @endforeach
            </div>
            <p class="text-xs text-gray-400 mt-4 text-center">Porcentaje de usuarios en cada nivel de indicadores por categoría de evaluación</p>
        </div>
    </div>

    {{-- Tendencia Completados vs Pendientes (Line Chart) --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-base font-semibold text-gray-900">Tendencia de Completados vs Pendientes</h3>
        </div>
        <div class="p-6">
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
            
            <div style="height: 300px; position: relative;">
                <svg viewBox="0 0 600 360" style="width: 100%; height: 100%;">
                    {{-- Grid --}}
                    @for($i = 0; $i <= 5; $i++)
                        @php $y = 320 - (60 * $i); $val = ($tMax / 5) * $i; @endphp
                        <line x1="50" y1="{{ $y }}" x2="590" y2="{{ $y }}" stroke="#E2E8F0" stroke-width="1" stroke-dasharray="3,3"/>
                        <text x="45" y="{{ $y + 4 }}" text-anchor="end" font-size="11" fill="#64748B">{{ round($val) }}</text>
                    @endfor

                    {{-- Lines --}}
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

                    {{-- Dots + Labels --}}
                    @foreach($compPts as $idx => $pt)
                        <circle cx="{{ $pt['x'] }}" cy="{{ $pt['y'] }}" r="4.5" fill="#10B981" stroke="white" stroke-width="2"/>
                        <circle cx="{{ $pendPts[$idx]['x'] }}" cy="{{ $pendPts[$idx]['y'] }}" r="4.5" fill="#F59E0B" stroke="white" stroke-width="2"/>
                        <text x="{{ $pt['x'] }}" y="342" text-anchor="middle" font-size="11" fill="#64748B">{{ $trends[$idx]['mes'] }}</text>
                    @endforeach

                    {{-- Axes --}}
                    <line x1="50" y1="20" x2="50" y2="320" stroke="#94A3B8" stroke-width="2"/>
                    <line x1="50" y1="320" x2="590" y2="320" stroke="#94A3B8" stroke-width="2"/>
                </svg>
            </div>
            <div class="flex items-center justify-center gap-6 mt-4 text-xs">
                <span class="flex items-center gap-2"><span class="w-3 h-3 bg-emerald-500 rounded-sm"></span>Completados</span>
                <span class="flex items-center gap-2"><span class="w-3 h-3 bg-amber-500 rounded-sm"></span>Pendientes</span>
            </div>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <p class="text-sm text-gray-500 mb-2">Tasa de Completado</p>
            <p class="text-3xl font-bold text-gray-900 mb-2">{{ $generalStats['completion_rate'] }}%</p>
            <p class="text-xs text-gray-400">De tests asignados</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <p class="text-sm text-gray-500 mb-2">Tiempo Promedio</p>
            <p class="text-3xl font-bold text-gray-900 mb-2">{{ $generalStats['avg_time'] }} min</p>
            <p class="text-xs text-gray-400">Por evaluación</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <p class="text-sm text-gray-500 mb-2">Evaluaciones Completadas</p>
            <p class="text-3xl font-bold text-gray-900 mb-2">{{ $generalStats['total_completed'] }}</p>
            <p class="text-xs text-emerald-600">+{{ $generalStats['this_month'] }} este mes</p>
        </div>
    </div>

    {{-- Notas --}}
    <div class="bg-gray-50/50 border border-gray-200 rounded-xl shadow-sm p-6">
        <h3 class="text-base font-semibold text-gray-900 mb-3">Notas sobre los Datos</h3>
        <ul class="space-y-2 text-sm text-gray-500">
            <li>• Los datos mostrados son agregados y anonimizados para proteger la confidencialidad</li>
            <li>• Los reportes no incluyen información personal identificable</li>
            <li>• Los indicadores representan niveles generales, no diagnósticos clínicos</li>
            <li>• Los datos se actualizan en tiempo real según el período seleccionado</li>
        </ul>
    </div>

</div>