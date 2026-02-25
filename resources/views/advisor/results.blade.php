<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @if(auth()->user()->role_id === 1)
                Resultados de Tests (Todos)
            @else
                Resultados de Tests
            @endif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @livewire('advisor.advisor-results')
        </div>
    </div>
</x-app-layout>