@props(['title' => __('Confirm Password'), 'content' => __('For your security, please confirm your password to continue.'), 'button' => __('Confirm')])

@php
    $confirmableId = md5($attributes->wire('then'));
@endphp

<span
    {{ $attributes->wire('then') }}
    x-data
    x-ref="span"
    x-on:click="$wire.startConfirmingPassword('{{ $confirmableId }}')"
    x-on:password-confirmed.window="setTimeout(() => $event.detail.id === '{{ $confirmableId }}' && $refs.span.dispatchEvent(new CustomEvent('then', { bubbles: false })), 250);"
>
    {{ $slot }}
</span>

@once
<x-dialog-modal wire:model.live="confirmingPassword">
    <x-slot name="title">
        <div class="flex items-center gap-3 text-gray-900">
            <div class="p-2 bg-teal-50 rounded-lg text-teal-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
            </div>
            <span class="font-bold text-xl">{{ $title }}</span>
        </div>
    </x-slot>

    <x-slot name="content">
        <p class="text-sm text-gray-500 leading-relaxed">{{ $content }}</p>

        <div class="mt-6" x-data="{}" x-on:confirming-password.window="setTimeout(() => $refs.confirmable_password.focus(), 250)">
            <x-label for="confirmable_password" value="Tu Contraseña" class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2" />
            <x-input type="password" class="block w-full border-gray-100 rounded-xl px-4 py-3 bg-gray-50 focus:bg-white focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all duration-200 shadow-sm" 
                        placeholder="••••••••" 
                        autocomplete="current-password"
                        x-ref="confirmable_password"
                        wire:model="confirmablePassword"
                        wire:keydown.enter="confirmPassword" />

            <x-input-error for="confirmable_password" class="mt-2 text-xs" />
        </div>
    </x-slot>

    <x-slot name="footer">
        <div class="flex items-center gap-3">
            <button type="button" wire:click="stopConfirmingPassword" wire:loading.attr="disabled"
                    class="px-6 py-2.5 text-[10px] font-black text-gray-500 hover:text-gray-900 transition-all uppercase tracking-widest">
                {{ __('Cancel') }}
            </button>

            <button type="button" dusk="confirm-password-button" wire:click="confirmPassword" wire:loading.attr="disabled"
                    class="px-8 py-2.5 text-sm text-white bg-teal-600 hover:bg-teal-700 rounded-xl font-bold transition-all duration-200 shadow-lg shadow-teal-500/20 active:scale-95 disabled:opacity-50 uppercase tracking-widest">
                {{ $button }}
            </button>
        </div>
    </x-slot>
</x-dialog-modal>
@endonce
