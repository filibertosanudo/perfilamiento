<div class="relative" x-data="{ open: @entangle('showDropdown') }">
    {{-- Bell Button --}}
    <button @click="open = !open" 
        class="relative p-2 hover:bg-gray-100 rounded-lg transition-colors text-gray-600">
        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"></path>
            <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"></path>
        </svg>
        @if($unreadCount > 0)
            <span class="absolute top-1 right-1 flex items-center justify-center min-w-[18px] h-[18px] bg-red-500 text-white text-[10px] font-bold rounded-full px-1">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    {{-- Dropdown --}}
    <div x-show="open" 
        @click.away="open = false"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-xl border border-gray-200 z-50"
        style="display: none;">
        
        {{-- Header --}}
        <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900">Notificaciones</h3>
            @if($unreadCount > 0)
                <button wire:click="markAllAsRead" 
                    class="text-xs text-teal-600 hover:text-teal-700 font-medium">
                    Marcar todas como leídas
                </button>
            @endif
        </div>

        {{-- Notifications List --}}
        <div class="max-h-96 overflow-y-auto">
            @forelse($notifications as $notification)
                <div class="px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-0 {{ $notification->read ? 'opacity-60' : '' }}">
                    <div class="flex gap-3">
                        {{-- Icon --}}
                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-{{ $notification->color }}-100 flex items-center justify-center">
                            @if($notification->icon === 'clipboard')
                                <svg class="w-5 h-5 text-{{ $notification->color }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            @elseif($notification->icon === 'check-circle')
                                <svg class="w-5 h-5 text-{{ $notification->color }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            @elseif($notification->icon === 'alert-triangle')
                                <svg class="w-5 h-5 text-{{ $notification->color }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            @elseif($notification->icon === 'clock')
                                <svg class="w-5 h-5 text-{{ $notification->color }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-{{ $notification->color }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                            @endif
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <p class="text-sm font-medium text-gray-900">{{ $notification->title }}</p>
                                @if(!$notification->read)
                                    <span class="flex-shrink-0 w-2 h-2 bg-teal-500 rounded-full"></span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-600 mt-0.5 line-clamp-2">{{ $notification->message }}</p>
                            <div class="flex items-center justify-between mt-2">
                                <p class="text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</p>
                                <div class="flex items-center gap-2">
                                    @if(!$notification->read)
                                        <button wire:click="markAsRead({{ $notification->id }})" 
                                            class="text-xs text-teal-600 hover:text-teal-700 font-medium">
                                            Marcar leída
                                        </button>
                                    @endif
                                    <button wire:click="deleteNotification({{ $notification->id }})" 
                                        class="text-xs text-red-600 hover:text-red-700 font-medium">
                                        Eliminar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-4 py-8 text-center">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <p class="text-sm text-gray-500">No tienes notificaciones</p>
                </div>
            @endforelse
        </div>

        {{-- Footer --}}
        @if($notifications->count() > 0)
            <div class="px-4 py-3 border-t border-gray-200 text-center">
                <a href="#" class="text-xs text-teal-600 hover:text-teal-700 font-medium">
                    Ver todas las notificaciones
                </a>
            </div>
        @endif
    </div>
</div>