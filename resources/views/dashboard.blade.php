<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-teal-800 leading-tight">
            {{ __('Panel de Control') }} - 
            @if(Auth::user()->role_id == 1) Administrador 
            @elseif(Auth::user()->role_id == 2) Orientador 
            @else Usuario @endif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                
                {{-- Director de Tráfico por Rol --}}
                @switch(Auth::user()->role_id)
                    @case(1) {{-- ID de Admin --}}
                        @include('dashboards._admin')
                        @break

                    @case(2) {{-- ID de Advisor --}}
                        @include('dashboards._advisor')
                        @break

                    @default {{-- ID de Usuario --}}
                        @include('dashboards._user')
                @endswitch

            </div>
        </div>
    </div>
</x-app-layout>