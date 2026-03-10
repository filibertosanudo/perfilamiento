@extends('pdfs.layout', [
    'title' => 'Historial de Evaluaciones',
    'institution' => $user->institution->name ?? 'Institución'
])

@section('content')
    <!-- Title -->
    <div class="title">
        <h1>Historial de Evaluaciones</h1>
        <p>{{ $user->first_name }} {{ $user->last_name }} {{ $user->second_last_name }}</p>
    </div>

    <!-- User Info -->
    <div class="info-box">
        <h3>Información del Participante</h3>
        <div class="info-row">
            <span class="info-label">Nombre Completo:</span>
            <span class="info-value">{{ $user->first_name }} {{ $user->last_name }} {{ $user->second_last_name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Email:</span>
            <span class="info-value">{{ $user->email }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Institución:</span>
            <span class="info-value">{{ $user->institution->name ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Total de Evaluaciones:</span>
            <span class="info-value">{{ $responses->count() }} tests completados</span>
        </div>
    </div>

    <!-- Summary Stats -->
    <div style="display: flex; justify-content: space-around; margin: 30px 0;">
        @php
            $totalTests = $responses->count();
            $avgScore = $responses->avg('numeric_result');
            $lastTest = $responses->first();
        @endphp
        
        <div style="text-align: center;">
            <div style="font-size: 32pt; font-weight: bold; color: #0F766E;">{{ $totalTests }}</div>
            <div style="font-size: 10pt; color: #64748B;">Tests Completados</div>
        </div>
        
        <div style="text-align: center;">
            <div style="font-size: 32pt; font-weight: bold; color: #0F766E;">{{ round($avgScore, 1) }}</div>
            <div style="font-size: 10pt; color: #64748B;">Puntaje Promedio</div>
        </div>
        
        @if($lastTest)
        <div style="text-align: center;">
            <div style="font-size: 14pt; font-weight: bold; color: #0F766E;">{{ $lastTest->finished_at->format('d/m/Y') }}</div>
            <div style="font-size: 10pt; color: #64748B;">Última Evaluación</div>
        </div>
        @endif
    </div>

    <!-- Tests Table -->
    <h3 style="color: #1F2937; margin-top: 30px; margin-bottom: 15px;">Detalle de Evaluaciones</h3>
    
    @if($responses->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Test</th>
                    <th>Puntaje</th>
                    <th>Resultado</th>
                    <th>Tiempo</th>
                </tr>
            </thead>
            <tbody>
                @foreach($responses as $response)
                    <tr>
                        <td>{{ $response->finished_at->format('d/m/Y') }}</td>
                        <td>{{ $response->assignment->test->name }}</td>
                        <td style="text-align: center; font-weight: bold;">{{ $response->numeric_result }}</td>
                        <td>
                            @php
                                $category = strtolower($response->result_category);
                                $color = '#10B981';
                                if (str_contains($category, 'severa')) $color = '#DC2626';
                                elseif (str_contains($category, 'moderada')) $color = '#F59E0B';
                                elseif (str_contains($category, 'leve')) $color = '#3B82F6';
                            @endphp
                            <span style="color: {{ $color }}; font-weight: bold;">
                                {{ $response->result_category }}
                            </span>
                        </td>
                        <td>{{ $response->started_at->diffInMinutes($response->finished_at) }} min</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="text-align: center; color: #9CA3AF; padding: 40px;">No hay evaluaciones completadas</p>
    @endif

    <!-- Evolution Chart -->
    @if($responses->count() >= 2)
        <div class="page-break"></div>
        
        <h3 style="color: #1F2937; margin-top: 30px; margin-bottom: 20px; text-align: center;">Evolución Temporal</h3>
        
        <div class="chart-container">
            @php
                $sortedResponses = $responses->sortBy('finished_at')->values();
                $maxScore = $sortedResponses->max('numeric_result');
                $chartHeight = 300;
                $chartWidth = 700;
                $padding = 50;
                $points = [];
                
                foreach ($sortedResponses as $index => $resp) {
                    $x = $padding + (($chartWidth - 2 * $padding) / max(1, $sortedResponses->count() - 1)) * $index;
                    $y = $chartHeight - $padding - (($resp->numeric_result / max(1, $maxScore)) * ($chartHeight - 2 * $padding));
                    $points[] = ['x' => $x, 'y' => $y, 'response' => $resp];
                }
                
                $pathPoints = implode(' ', array_map(fn($p) => $p['x'] . ',' . $p['y'], $points));
            @endphp
            
            <svg viewBox="0 0 {{ $chartWidth }} {{ $chartHeight + 60 }}" style="max-width: 100%; height: auto;">
                <!-- Grid lines -->
                @for($i = 0; $i <= 5; $i++)
                    @php
                        $y = $chartHeight - $padding - (($chartHeight - 2 * $padding) / 5) * $i;
                        $value = ($maxScore / 5) * $i;
                    @endphp
                    <line x1="{{ $padding }}" y1="{{ $y }}" x2="{{ $chartWidth - $padding }}" y2="{{ $y }}" 
                          stroke="#E5E7EB" stroke-width="1" stroke-dasharray="3,3"/>
                    <text x="{{ $padding - 10 }}" y="{{ $y + 5 }}" text-anchor="end" font-size="10" fill="#64748B">
                        {{ round($value, 1) }}
                    </text>
                @endfor
                
                <!-- Line -->
                <polyline points="{{ $pathPoints }}" fill="none" stroke="#0F766E" stroke-width="3" stroke-linejoin="round"/>
                
                <!-- Points -->
                @foreach($points as $index => $point)
                    <circle cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" r="5" fill="#0F766E" stroke="white" stroke-width="2"/>
                    <text x="{{ $point['x'] }}" y="{{ $chartHeight - $padding + 25 }}" text-anchor="middle" font-size="9" fill="#64748B">
                        {{ $point['response']->finished_at->format('d/m') }}
                    </text>
                @endforeach
                
                <!-- Axes -->
                <line x1="{{ $padding }}" y1="{{ $padding }}" x2="{{ $padding }}" y2="{{ $chartHeight - $padding }}" 
                      stroke="#9CA3AF" stroke-width="2"/>
                <line x1="{{ $padding }}" y1="{{ $chartHeight - $padding }}" x2="{{ $chartWidth - $padding }}" y2="{{ $chartHeight - $padding }}" 
                      stroke="#9CA3AF" stroke-width="2"/>
                
                <!-- Labels -->
                <text x="{{ $chartWidth / 2 }}" y="{{ $chartHeight + 20 }}" text-anchor="middle" font-size="12" fill="#374151" font-weight="bold">
                    Fecha de Evaluación
                </text>
                <text x="20" y="{{ $chartHeight / 2 }}" text-anchor="middle" font-size="12" fill="#374151" font-weight="bold"
                      transform="rotate(-90, 20, {{ $chartHeight / 2 }})">
                    Puntaje
                </text>
            </svg>
        </div>
    @endif

    <!-- Disclaimer -->
    <div class="disclaimer">
        <p><strong>Importante:</strong> Este historial muestra la evolución de tus evaluaciones a lo largo del tiempo. Es importante recordar que estos resultados son herramientas de screening y no constituyen diagnósticos clínicos. Si observas tendencias preocupantes o experimentas malestar significativo, te recomendamos consultar con un profesional de la salud mental.</p>
    </div>
@endsection