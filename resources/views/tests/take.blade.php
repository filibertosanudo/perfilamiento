<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Responder Test
        </h2>
    </x-slot>

    @livewire('user.take-test', ['assignmentId' => $assignmentId])
</x-app-layout>