@props(['orden', 'origen' => 'show'])

@php
$estados = [
    'Recibido'        => ['color' => 'bg-slate-500',   'hover' => 'hover:bg-slate-600',   'ring' => 'ring-slate-400',   'icono' => 'M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4'],
    'En Revisión'     => ['color' => 'bg-blue-500',    'hover' => 'hover:bg-blue-600',    'ring' => 'ring-blue-400',    'icono' => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'],
    'Esperando Pieza' => ['color' => 'bg-amber-500',   'hover' => 'hover:bg-amber-600',   'ring' => 'ring-amber-400',   'icono' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
    'Retardo'         => ['color' => 'bg-red-500',     'hover' => 'hover:bg-red-600',     'ring' => 'ring-red-400',     'icono' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
    'Reparado'        => ['color' => 'bg-emerald-500', 'hover' => 'hover:bg-emerald-600', 'ring' => 'ring-emerald-400', 'icono' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
    'Entregado'       => ['color' => 'bg-gray-500',    'hover' => 'hover:bg-gray-600',    'ring' => 'ring-gray-400',    'icono' => 'M5 13l4 4L19 7'],
    'Cancelado'       => ['color' => 'bg-rose-600',    'hover' => 'hover:bg-rose-700',    'ring' => 'ring-rose-400',    'icono' => 'M6 18L18 6M6 6l12 12'],
];

$estadoColorsbadge = [
    'Recibido'        => 'bg-slate-100 text-slate-700',
    'En Revisión'     => 'bg-blue-100 text-blue-700',
    'Esperando Pieza' => 'bg-amber-100 text-amber-700',
    'Reparado'        => 'bg-emerald-100 text-emerald-700',
    'Retardo'         => 'bg-red-100 text-red-700',
    'Entregado'       => 'bg-gray-100 text-gray-500',
    'Cancelado'       => 'bg-rose-100 text-rose-700',
];
$colorActual = $estadoColorsbage[$orden->estado] ?? 'bg-gray-100 text-gray-600';
@endphp

<div
    x-data="{ abierto: false, estadoSeleccionado: '{{ $orden->estado }}' }"
    @abrir-modal-estado-{{ $orden->id }}.window="abierto = true"
    x-show="abierto"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    role="dialog"
    aria-modal="true"
    aria-labelledby="modal-titulo-{{ $orden->id }}">

    {{-- Fondo oscuro --}}
    <div
        x-show="abierto"
        x-transition.opacity
        @click="abierto = false"
        class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

    {{-- Cuerpo del modal --}}
    <div
        x-show="abierto"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="relative z-10 w-full max-w-xl rounded-2xl bg-white shadow-2xl">

        {{-- Header --}}
        <div class="flex items-center justify-between rounded-t-2xl bg-gradient-to-r from-[#2D1B69] to-[#1E1B2E] px-6 py-4">
            <div>
                <h3 id="modal-titulo-{{ $orden->id }}" class="text-base font-bold text-white">
                    Actualizar estado
                </h3>
                <div class="mt-1 flex items-center gap-2">
                    <p class="text-purple-300 text-xs">Orden: <span class="font-semibold text-purple-100">{{ $orden->folio }}</span></p>
                    <span class="text-purple-500 text-xs">·</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-medium {{ $estadoColorsbage[$orden->estado] ?? 'bg-gray-100 text-gray-600' }}">
                        {{ $orden->estado }}
                    </span>
                </div>
            </div>
            <button
                type="button"
                @click="abierto = false"
                class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/10 text-white transition hover:bg-white/20"
                aria-label="Cerrar">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        {{-- Formulario --}}
        <form method="POST" action="{{ route('reparaciones.update', $orden) }}" class="p-6 space-y-5">
            @csrf
            @method('PATCH')

            {{-- Input oculto que recibe el valor seleccionado --}}
            <input type="hidden" name="estado" :value="estadoSeleccionado" required>

            {{-- Instrucción --}}
            <p class="text-sm text-gray-500">Selecciona el nuevo estado para esta orden:</p>

            {{-- Grid de botones de estado --}}
            <div class="grid grid-cols-4 gap-3 sm:grid-cols-7">
                @foreach($estados as $nombre => $cfg)
                <button
                    type="button"
                    @click="estadoSeleccionado = '{{ $nombre }}'"
                    title="{{ $nombre }}"
                    :class="estadoSeleccionado === '{{ $nombre }}'
                        ? '{{ $cfg['color'] }} {{ $cfg['ring'] }} ring-2 ring-offset-2 scale-105 shadow-lg'
                        : '{{ $cfg['color'] }} {{ $cfg['hover'] }} opacity-60 hover:opacity-90'"
                    class="flex flex-col items-center justify-center gap-2 rounded-xl p-3 text-white transition-all duration-150
                        {{ $orden->estado === $nombre ? 'cursor-default' : '' }}">
                    <svg class="h-6 w-6 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $cfg['icono'] }}"></path>
                    </svg>
                    <span class="text-center text-[10px] font-semibold leading-tight">{{ $nombre }}</span>
                </button>
                @endforeach
            </div>

            {{-- Confirmación del estado seleccionado --}}
            <div class="rounded-xl border border-gray-100 bg-gray-50 px-4 py-3 text-sm text-gray-600">
                Estado a guardar:
                <span class="ml-1 font-semibold text-[#2D1B69]" x-text="estadoSeleccionado"></span>
                <template x-if="estadoSeleccionado === '{{ $orden->estado }}'">
                    <span class="ml-2 text-xs text-amber-600">(es el estado actual)</span>
                </template>
            </div>

            {{-- Campos ocultos para no pisar los demás valores --}}
            <input type="hidden" name="user_id"             value="{{ $orden->user_id }}">
            <input type="hidden" name="diagnostico_tecnico" value="{{ $orden->diagnostico_tecnico }}">
            <input type="hidden" name="comentario_retardo"  value="{{ $orden->comentario_retardo }}">
            <input type="hidden" name="costo_final"         value="{{ $orden->costo_final }}">
            <input type="hidden" name="_origen"             value="{{ $origen }}">

            {{-- Botones de acción --}}
            <div class="flex items-center justify-end gap-3 pt-1">
                <button
                    type="button"
                    @click="abierto = false"
                    class="rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                    Cancelar
                </button>
                <button
                    type="submit"
                    :disabled="estadoSeleccionado === '{{ $orden->estado }}'"
                    :class="estadoSeleccionado === '{{ $orden->estado }}'
                        ? 'bg-gray-300 text-gray-400 cursor-not-allowed'
                        : 'bg-[#7C3AED] hover:bg-[#6D28D9] text-white shadow-sm'"
                    class="flex items-center gap-2 rounded-xl px-5 py-2 text-sm font-medium transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Guardar estado
                </button>
            </div>
        </form>
    </div>
</div>