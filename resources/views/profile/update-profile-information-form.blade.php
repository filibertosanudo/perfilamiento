<form wire:submit="updateProfileInformation">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
            <div x-data="{photoName: null, photoPreview: null}" class="md:col-span-2">
                <input type="file" id="photo" class="hidden"
                            wire:model.live="photo"
                            x-ref="photo"
                            x-on:change="
                                    photoName = $refs.photo.files[0].name;
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        photoPreview = e.target.result;
                                    };
                                    reader.readAsDataURL($refs.photo.files[0]);
                            " />

                <x-label for="photo" value="Foto de Perfil" class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-2" />

                <div class="flex items-center gap-6 mt-2">
                    <div x-show="! photoPreview" class="shrink-0">
                        <img src="{{ $this->user->profile_photo_url }}" alt="{{ $this->user->name }}" class="rounded-2xl size-24 object-cover ring-4 ring-gray-50 shadow-sm border border-gray-100">
                    </div>

                    <div x-show="photoPreview" style="display: none;" class="shrink-0">
                        <span class="block rounded-2xl size-24 bg-cover bg-no-repeat bg-center ring-4 ring-teal-50 shadow-sm border border-teal-100"
                              x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                        </span>
                    </div>

                    <div class="flex flex-col gap-2">
                        <button type="button" class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-xs font-bold text-gray-700 hover:bg-gray-50 transition-all shadow-sm active:scale-95" x-on:click.prevent="$refs.photo.click()">
                            Cambiar Foto
                        </button>

                        @if ($this->user->profile_photo_path)
                            <button type="button" class="px-4 py-2 bg-red-50 border border-red-100 rounded-xl text-xs font-bold text-red-600 hover:bg-red-100 transition-all active:scale-95" wire:click="deleteProfilePhoto">
                                Eliminar
                            </button>
                        @endif
                    </div>
                </div>

                <x-input-error for="photo" class="mt-2 text-xs" />
            </div>
        @endif

        {{-- Names --}}
        <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <x-label for="first_name" value="Nombre(s)" class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-2" />
                <x-input id="first_name" type="text" class="block w-full border-gray-100 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all duration-200" wire:model="state.first_name" required autocomplete="given-name" />
                <x-input-error for="first_name" class="mt-2 text-xs" />
            </div>

            <div>
                <x-label for="last_name" value="Apellido Paterno" class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-2" />
                <x-input id="last_name" type="text" class="block w-full border-gray-100 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all duration-200" wire:model="state.last_name" required autocomplete="family-name" />
                <x-input-error for="last_name" class="mt-2 text-xs" />
            </div>

            <div>
                <x-label for="second_last_name" value="Apellido Materno" class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-2" />
                <x-input id="second_last_name" type="text" class="block w-full border-gray-100 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all duration-200" wire:model="state.second_last_name" autocomplete="family-name" />
                <x-input-error for="second_last_name" class="mt-2 text-xs" />
            </div>
        </div>

        {{-- Email --}}
        <div class="md:col-span-2">
            <x-label for="email" value="Correo Electrónico" class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-2" />
            <x-input id="email" type="email" class="block w-full border-gray-100 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all duration-200" wire:model="state.email" required autocomplete="username" />
            <x-input-error for="email" class="mt-2 text-xs" />

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::emailVerification()) && ! $this->user->hasVerifiedEmail())
                <p class="text-[11px] mt-2 font-medium text-amber-600 bg-amber-50 px-3 py-2 rounded-lg border border-amber-100">
                    Tu dirección de correo no está verificada.
                    <button type="button" class="underline hover:text-amber-800 font-bold ml-1 active:scale-95" wire:click.prevent="sendEmailVerification">
                        Reenviar enlace de verificación
                    </button>
                </p>

                @if ($this->verificationLinkSent)
                    <p class="mt-2 text-[11px] font-bold text-green-600 bg-green-50 px-3 py-2 rounded-lg border border-green-100">
                        Se ha enviado un nuevo enlace de verificación.
                    </p>
                @endif
            @endif
        </div>
    </div>

    <div class="flex items-center justify-end mt-8 pt-6 border-t border-gray-50">
        <x-action-message class="me-3 text-xs font-bold text-teal-600" on="saved">
            ¡Guardado con éxito!
        </x-action-message>

        <button wire:loading.attr="disabled" wire:target="photo" class="px-8 py-2.5 bg-teal-600 text-white rounded-xl text-sm font-bold hover:bg-teal-700 shadow-lg shadow-teal-500/20 transition-all active:scale-95 disabled:opacity-50">
            Guardar Cambios
        </button>
    </div>
</form>