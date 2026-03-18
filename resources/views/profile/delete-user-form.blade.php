<div>
    <div class="max-w-xl text-sm text-gray-600 leading-relaxed mb-6">
        Una vez que se elimine tu cuenta, todos sus recursos y datos se eliminarán de forma permanente. Antes de eliminar tu cuenta, descarga cualquier dato o información que desees conservar.
    </div>

    <div class="mt-5">
        <button wire:click="confirmUserDeletion" wire:loading.attr="disabled"
                class="px-8 py-2.5 bg-red-600 text-white rounded-xl text-sm font-bold hover:bg-red-700 shadow-lg shadow-red-500/20 transition-all active:scale-95 disabled:opacity-50">
            Eliminar Cuenta
        </button>
    </div>

    <!-- Delete User Confirmation Modal -->
    @if($confirmingUserDeletion)
        <div class="fixed inset-0 z-[60] overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4">
                
                {{-- Backdrop --}}
                <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-md transition-opacity" wire:click="$toggle('confirmingUserDeletion')"></div>

                {{-- Modal --}}
                <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all animate-modal-pop">
                    
                    {{-- Header --}}
                    <div class="px-8 py-6 border-b border-red-50 bg-red-50/30">
                        <h3 class="text-xl font-black text-red-900 tracking-tight">
                            Confirmar Eliminación
                        </h3>
                        <p class="text-xs font-medium text-red-500 mt-1 uppercase tracking-wider">
                            Esta acción es irreversible
                        </p>
                    </div>

                    {{-- Body --}}
                    <div class="px-8 py-8">
                        <p class="text-sm text-gray-600 mb-6 leading-relaxed">
                            ¿Estás seguro de que deseas eliminar tu cuenta? Una vez eliminada, todos los datos se perderán de forma permanente. Por favor, introduce tu contraseña para confirmar.
                        </p>

                        <div class="mb-4">
                            <label for="password_deletion" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                                Contraseña de Confirmación
                            </label>
                            <input 
                                wire:model="password"
                                type="password" 
                                id="password_deletion"
                                class="block w-full border border-gray-100 rounded-2xl px-5 py-3 text-sm bg-gray-50 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 transition-all duration-200 shadow-sm"
                                placeholder="••••••••••••"
                                autofocus
                                wire:keydown.enter="deleteUser"
                            >
                            @error('password')
                                <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="px-8 py-6 border-t border-gray-50 bg-gray-50/50 flex flex-col sm:flex-row gap-3">
                        <button 
                            wire:click="$toggle('confirmingUserDeletion')"
                            class="flex-1 px-6 py-3 text-xs font-bold text-gray-500 hover:bg-gray-100 rounded-2xl transition-all uppercase tracking-widest order-2 sm:order-1">
                            Cancelar
                        </button>
                        <button 
                            wire:click="deleteUser"
                            class="flex-1 px-8 py-3 text-xs font-bold text-white bg-red-600 rounded-2xl hover:bg-red-700 transition-all shadow-lg shadow-red-500/20 active:scale-95 order-1 sm:order-2 uppercase tracking-widest">
                            Eliminar Permanentemente
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
