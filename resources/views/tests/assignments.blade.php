<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ auth()->user()->role_id === 1 ? 'Asignación de Tests' : 'Mis Asignaciones de Tests' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @livewire('admin.test-assignment-management')
        </div>
    </div>
</x-app-layout>