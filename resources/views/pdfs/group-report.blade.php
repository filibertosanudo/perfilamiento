@extends('pdfs.layout', [
    'title' => 'Reporte de Grupo',
    'institution' => $group->area->name ?? 'Área'
])

@section('content')
    <!-- Title -->
    <div class="title">
        <h1>Reporte de Grupo</h1>
        <p>{{ $group->name }}</p>
    </div>

    <!-- Group Info -->
    <div class="info-box">
        <h3>Información del Grupo</h3>
        <div class="info-row">
            <span class="info-label">Nombre del Grupo:</span>
            <span class="info-value">{{ $group->name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Institución:</span>
            <span class="info-value">{{ $group->area->name ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Orientador:</span>
            <span class="info-value">{{ $group->creator->first_name }} {{ $group->creator->last_name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Total de Usuarios:</span>
            <span class="info-value">{{ $stats['total_users'] }}</span>
        </div>
    </div>

    <!-- Summary Stats -->
    <div style="display: flex; justify-content: space-around; margin: 30px 0;">
        <div style="text-align: center;">
            <div style="font-size: 32pt; font-weight: bold; color: #0F766E;">{{ $stats['total_completed'] }}</div>
            <div style="font-size: 10pt; color: #64748B;">Tests Completados</div>
        </div>
        
        <div style="text-align: center;">
            <div style="font-size: 32pt; font-weight: bold; color: #0F766E;">{{ round($stats['avg_score'], 1) }}</div>
            <div style="font-size: 10pt; color: #64748B;">Puntaje Promedio</div>
        </div>
        
        <div style="text-align: center;">
            <div style="font-size: 32pt; font-weight: bold; color: #0F766E;">
                {{ $stats['total_users'] > 0 ? round(($stats['total_completed'] / $stats['total_users']), 1) : 0 }}
            </div>
            <div style="font-size: 10pt; color: #64748B;">Tests por Usuario</div>
        </div>
    </div>

    <!-- Test Statistics -->
    <h3 style="color: #1F2937; margin-top: 30px; margin-bottom: 15px;">Estadísticas por Test</h3>
    
    <table>
        <thead>
            <tr>
                <th>Test</th>
                <th style="text-align: center;">Completados</th>
                <th style="text-align: center;">Promedio</th>
            </tr>
        </thead>
        <tbody>
            @forelse($testStats as $stat)
                <tr>
                    <td>{{ $stat['test'] }}</td>
                    <td style="text-align: center;">{{ $stat['completed'] }}</td>
                    <td style="text-align: center; font-weight: bold; color: #0F766E;">{{ $stat['average'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="text-align: center; color: #9CA3AF;">No hay tests completados</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Group Members -->
    <div class="page-break"></div>
    
    <h3 style="color: #1F2937; margin-top: 30px; margin-bottom: 15px;">Miembros del Grupo</h3>
    
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Email</th>
                <th style="text-align: center;">Tests Completados</th>
            </tr>
        </thead>
        <tbody>
            @foreach($group->users as $index => $user)
                @php
                    $userTests = \App\Models\TestResponse::where('user_id', $user->id)
                        ->where('completed', true)
                        ->count();
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                    <td>{{ $user->email }}</td>
                    <td style="text-align: center;">{{ $userTests }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Disclaimer -->
    <div class="disclaimer">
        <p><strong>Confidencialidad:</strong> Este reporte contiene información agregada del grupo. Los datos individuales están protegidos y solo deben ser utilizados con fines de seguimiento y mejora del bienestar del grupo.</p>
    </div>
@endsection