@extends('pdfs.layout', [
    'title' => 'Resultado de Test',
    'institution' => $user->area->name ?? 'Área'
])

@section('content')
    <!-- Title -->
    <div class="title">
        <h1>Resultado de Evaluación</h1>
        <p>{{ $test->name }}</p>
    </div>

    <!-- User Info -->
    <div class="info-box">
        <h3>Información del Participante</h3>
        <div class="info-row">
            <span class="info-label">Nombre:</span>
            <span class="info-value">{{ $user->first_name }} {{ $user->last_name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Fecha de Evaluación:</span>
            <span class="info-value">{{ $response->finished_at->format('d/m/Y H:i') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Tiempo de Completado:</span>
            <span class="info-value">{{ $response->started_at->diffInMinutes($response->finished_at) }} minutos</span>
        </div>
    </div>

    <!-- Result Score -->
    <div style="text-align: center; margin: 30px 0;">
        <h3 style="color: #64748B; margin-bottom: 10px;">Puntaje Obtenido</h3>
        <div style="font-size: 48pt; font-weight: bold; color: #0F766E;">
            {{ $response->numeric_result }}
        </div>
        <div style="font-size: 12pt; color: #64748B; margin-top: 5px;">
            de {{ $test->max_score }} puntos
        </div>
        
        @php
            $category = strtolower($response->result_category);
            $badgeClass = 'badge-normal';
            if (str_contains($category, 'mínima')) $badgeClass = 'badge-minimal';
            elseif (str_contains($category, 'leve')) $badgeClass = 'badge-leve';
            elseif (str_contains($category, 'moderada')) $badgeClass = 'badge-moderada';
            elseif (str_contains($category, 'severa')) $badgeClass = 'badge-severa';
            elseif (str_contains($category, 'baja')) $badgeClass = 'badge-baja';
            elseif (str_contains($category, 'alta')) $badgeClass = 'badge-alta';
        @endphp
        
        <div class="result-badge {{ $badgeClass }}">
            {{ $response->result_category }}
        </div>
    </div>

    <!-- Interpretation -->
    <div class="info-box">
        <h3>Interpretación del Resultado</h3>
        <p style="margin-top: 10px; line-height: 1.8;">
            @if(str_contains($test->name, 'GAD'))
                @if($response->numeric_result <= 4)
                    Tu nivel de ansiedad es mínimo. Experimentas síntomas de ansiedad muy ocasionalmente, lo cual es completamente normal.
                @elseif($response->numeric_result <= 9)
                    Presentas síntomas leves de ansiedad. Estos niveles son manejables y podrían beneficiarse de técnicas de relajación y manejo del estrés.
                @elseif($response->numeric_result <= 14)
                    Tu nivel de ansiedad es moderado. Se recomienda considerar hablar con un profesional de la salud mental para desarrollar estrategias de afrontamiento.
                @else
                    Presentas síntomas severos de ansiedad. Es importante que busques apoyo profesional para manejar estos síntomas de manera efectiva.
                @endif
            @elseif(str_contains($test->name, 'PHQ'))
                @if($response->numeric_result <= 4)
                    Tu nivel de síntomas depresivos es mínimo. Esto indica un buen estado de ánimo general.
                @elseif($response->numeric_result <= 9)
                    Presentas síntomas leves de depresión. Mantener actividades positivas y conexiones sociales puede ser beneficioso.
                @elseif($response->numeric_result <= 14)
                    Tu nivel de depresión es moderado. Se sugiere consultar con un profesional para evaluar opciones de tratamiento.
                @elseif($response->numeric_result <= 19)
                    Presentas síntomas moderadamente severos de depresión. Es importante buscar apoyo profesional.
                @else
                    Presentas síntomas severos de depresión. Te recomendamos encarecidamente buscar ayuda profesional de inmediato.
                @endif
            @elseif(str_contains($test->name, 'Rosenberg'))
                @if($response->numeric_result < 25)
                    Tu nivel de autoestima es bajo. Trabajar en el autoconocimiento y la aceptación personal puede ser muy beneficioso.
                @elseif($response->numeric_result <= 30)
                    Tu autoestima se encuentra en un nivel saludable y equilibrado.
                @else
                    Presentas un nivel alto de autoestima, lo cual es positivo para tu bienestar emocional.
                @endif
            @endif
        </p>
    </div>

    <!-- Visual Chart (Simple Bar) -->
    <div class="chart-container">
        <h3 style="color: #64748B; margin-bottom: 20px;">Visualización del Resultado</h3>
        <svg viewBox="0 0 600 100" style="max-width: 600px; height: auto;">
            <!-- Background bar -->
            <rect x="0" y="40" width="600" height="20" fill="#E5E7EB" rx="10"/>
            
            <!-- Progress bar -->
            @php
                $percentage = ($response->numeric_result / $test->max_score) * 100;
                $width = ($percentage / 100) * 600;
                $color = '#10B981'; // Default green
                if ($percentage > 66) $color = '#DC2626'; // Red
                elseif ($percentage > 33) $color = '#F59E0B'; // Amber
            @endphp
            <rect x="0" y="40" width="{{ $width }}" height="20" fill="{{ $color }}" rx="10"/>
            
            <!-- Labels -->
            <text x="0" y="30" font-size="12" fill="#64748B">0</text>
            <text x="590" y="30" text-anchor="end" font-size="12" fill="#64748B">{{ $test->max_score }}</text>
            <text x="{{ $width }}" y="85" text-anchor="middle" font-size="14" font-weight="bold" fill="{{ $color }}">
                {{ $response->numeric_result }}
            </text>
        </svg>
    </div>

    <!-- Recommendations -->
    <div class="info-box" style="margin-top: 30px;">
        <h3>Recomendaciones Generales</h3>
        <ul style="margin-top: 10px; margin-left: 20px; line-height: 1.8;">
            <li>Mantén un estilo de vida saludable con ejercicio regular y alimentación balanceada</li>
            <li>Practica técnicas de relajación como meditación o respiración profunda</li>
            <li>Mantén conexiones sociales significativas con familiares y amigos</li>
            <li>Establece rutinas de sueño saludables (7-8 horas por noche)</li>
            @if($response->numeric_result > ($test->max_score * 0.5))
                <li><strong>Considera buscar apoyo profesional de un psicólogo o consejero</strong></li>
            @endif
        </ul>
    </div>

    <!-- Disclaimer -->
    <div class="disclaimer">
        <p><strong>Importante:</strong> Este resultado es solo una evaluación preliminar y no constituye un diagnóstico clínico. Los tests de screening son herramientas útiles para identificar áreas de preocupación, pero no reemplazan la evaluación profesional. Si experimentas malestar significativo o síntomas que interfieren con tu vida diaria, te recomendamos consultar con un profesional de la salud mental.</p>
    </div>
@endsection