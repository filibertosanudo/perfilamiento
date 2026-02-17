<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-teal-800 leading-tight">
            {{ __('Panel de Control') }}
            <span class="text-gray-500 text-sm font-normal ml-2">
                @if(Auth::user()->role_id == 1) (Administrador)
                @elseif(Auth::user()->role_id == 2) (Orientador)
                @else (Usuario) @endif
            </span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Lógica de selección de Dashboard --}}
            @switch(Auth::user()->role_id)
                @case(1)
                    {{-- Si es Admin, cargamos su vista parcial --}}
                    @include('dashboards._admin')
                    @break

                @case(2)
                    @include('dashboards._advisor')
                    @break

                @default
                    @include('dashboards._user')
            @endswitch

        </div>
    </div>
</x-app-layout>