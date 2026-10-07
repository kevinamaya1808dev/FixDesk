@extends('plantillas.base')

@section('titulo-pestana', 'Nueva Orden')

@section('contenido-principal')


<style>
    /* Asegurar que los dropdowns se muestren correctamente */
    .fixed.z-50 {
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    /* Scroll suave para los dropdowns */
    .overflow-y-auto {
        scrollbar-width: thin;
        scrollbar-color: #CBD5E0 #EDF2F7;
    }

    .overflow-y-auto::-webkit-scrollbar {
        width: 6px;
    }

    .overflow-y-auto::-webkit-scrollbar-track {
        background: #EDF2F7;
        border-radius: 10px;
    }

    .overflow-y-auto::-webkit-scrollbar-thumb {
        background: #CBD5E0;
        border-radius: 10px;
    }

    .overflow-y-auto::-webkit-scrollbar-thumb:hover {
        background: #A0AEC0;
    }
</style>

<div class="mx-auto max-w-4xl">
    {{-- Header --}}
    <div class="mb-6 md:mb-8">
        <h1 class="text-2xl md:text-3xl font-bold bg-[#1E1B2E] bg-clip-text text-transparent">
            Nueva orden de reparación
        </h1>
        <p class="text-gray-500 text-sm mt-1">Completa todos los campos para registrar la orden</p>
    </div>

    {{-- Errores de validación --}}
    @if($errors->any())
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 rounded-lg p-4" role="alert">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div class="flex-1">
                <p class="font-semibold text-red-800 mb-2">Por favor corrige los siguientes errores:</p>
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                    <li class="text-red-700 text-sm">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    {{-- Formulario --}}
    <form method="POST" action="{{ route('reparaciones.store') }}" class="space-y-5 md:space-y-6">
        @csrf

        {{-- BLOQUE 1: Datos del cliente --}}
        <div class="bg-white rounded-2xl shadow-md border border-gray-100" style="overflow: visible !important;">
            <div class="border-b border-gray-100 bg-gradient-to-r from-[#7C3AED]/5 to-[#EC4899]/5 px-4 py-4 md:px-6">
                <legend class="text-lg font-semibold text-[#2D1B69] flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#7C3AED]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Datos del cliente
                </legend>
            </div>
            <div class="space-y-4 p-4 md:p-6">
                {{-- Selector de cliente existente --}}
                <div>
                    <label for="cliente_id" class="block text-sm font-medium text-gray-700 mb-1">Buscar cliente existente <span class="text-gray-400 text-xs font-normal">(opcional)</span></label>
                    <x-custom-select
                        name="cliente_id"
                        :options="$clientes->pluck('nombre', 'id')->toArray()"
                        :selected="old('cliente_id')"
                        placeholder="Nuevo cliente"
                        :show-create="true"
                        create-text=" Crear nuevo cliente" />
                </div>

                <div class="bg-amber-50 border border-amber-200 rounded-xl p-3">
                    <p class="text-amber-700 text-xs flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Si seleccionas un cliente existente, los campos de abajo se ignorarán.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="cliente_nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre completo</label>
                        <input type="text" id="cliente_nombre" name="cliente_nombre" value="{{ old('cliente_nombre') }}"
                            class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-[#7C3AED]/20 focus:border-[#7C3AED] transition-all" />
                    </div>
                    <div>
                        <label for="cliente_telefono" class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                        <input type="text" id="cliente_telefono" name="cliente_telefono" value="{{ old('cliente_telefono') }}"
                            class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-[#7C3AED]/20 focus:border-[#7C3AED] transition-all" />
                    </div>
                    <div class="md:col-span-2">
                        <label for="cliente_email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-gray-400 text-xs">(para notificaciones)</span></label>
                        <input type="email" id="cliente_email" name="cliente_email" value="{{ old('cliente_email') }}"
                            class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-[#7C3AED]/20 focus:border-[#7C3AED] transition-all" />
                    </div>
                </div>
            </div>
        </div>

                {{-- BLOQUE 2: Datos del dispositivo --}}
        <div class="bg-white rounded-2xl shadow-md border border-gray-100" style="overflow: visible !important;">
            <div class="border-b border-gray-100 bg-gradient-to-r from-[#7C3AED]/5 to-[#EC4899]/5 px-4 py-4 md:px-6">
                <legend class="text-lg font-semibold text-[#2D1B69] flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#7C3AED]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 3h14a2 2 0 01-2 2H7a2 2 0 01-2-2zm0 0v16a2 2 0 002 2h10a2 2 0 002-2V5"></path>
                    </svg>
                    Dispositivos
                </legend>
            </div>

            <div class="p-4 md:p-6" x-data="{
                grupos: [
                    { tipo: 'Laptop', cantidad: 1, articulos: [{ marca: '', modelo: '', numero_serie: '' }] }
                ],

                tiposDisponibles: [
                    'Laptop','Smartphone','Tablet','PC de Escritorio','Monitor',
                    'Impresora','Consola de videojuegos','Smartwatch','Televisión',
                    'Router / Módem','Otro'
                ],

                agregarGrupo() {
                    this.grupos.push({
                        tipo: 'Laptop',
                        cantidad: 1,
                        articulos: [{ marca: '', modelo: '', numero_serie: '' }]
                    });
                },

                eliminarGrupo(gi) {
                    if (this.grupos.length === 1) return;
                    this.grupos.splice(gi, 1);
                },

                setCantidad(gi, n) {
                    const grupo = this.grupos[gi];
                    if (grupo.cantidad === n) {
                        n = 1;
                    }
                    grupo.cantidad = n;
                    const actual = grupo.articulos.length;
                    if (n > actual) {
                        for (let i = actual; i < n; i++) {
                            grupo.articulos.push({ marca: '', modelo: '', numero_serie: '' });
                        }
                    } else {
                        grupo.articulos = grupo.articulos.slice(0, n);
                    }
                },

                resumen() {
                    return this.grupos.map(g =>
                        g.cantidad + ' ' + g.tipo + (g.cantidad > 1 ? '(s)' : '')
                    ).join(', ');
                }
            }">

                {{-- Resumen dinámico --}}
                <div class="mb-5 rounded-xl bg-[#7C3AED]/5 border border-[#7C3AED]/20 px-4 py-3 flex items-start gap-2">
                    <svg class="w-4 h-4 text-[#7C3AED] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <p class="text-sm text-[#2D1B69]">
                        Esta orden incluye: <strong x-text="resumen()"></strong>
                    </p>
                </div>

                {{-- Grupos de dispositivos --}}
                <div class="space-y-6">
                    <template x-for="(grupo, gi) in grupos" :key="gi">
                        <div class="rounded-2xl border-2 border-gray-200 bg-gray-50 overflow-hidden">

                            {{-- Header del grupo --}}
                            <div class="flex items-center justify-between gap-3 border-b border-gray-200 bg-white px-4 py-3">
                                <div class="flex items-center gap-3 flex-1 min-w-0">
                                    <span class="flex-shrink-0 flex h-7 w-7 items-center justify-center rounded-lg bg-[#7C3AED]/10 text-xs font-bold text-[#7C3AED]" x-text="gi + 1"></span>
                                    {{-- Selector de tipo --}}
                                    <select
                                        :name="`grupos[${gi}][tipo]`"
                                        x-model="grupo.tipo"
                                        class="flex-1 min-w-0 rounded-xl border border-gray-300 px-3 py-1.5 text-sm font-medium text-[#2D1B69] focus:border-[#7C3AED] focus:outline-none focus:ring-2 focus:ring-[#7C3AED]/20">
                                        <template x-for="tipo in tiposDisponibles" :key="tipo">
                                            <option :value="tipo" :selected="grupo.tipo === tipo" x-text="tipo"></option>
                                        </template>
                                    </select>
                                </div>
                                <button
                                    type="button"
                                    @click="eliminarGrupo(gi)"
                                    x-show="grupos.length > 1"
                                    title="Eliminar este grupo"
                                    class="flex-shrink-0 flex h-7 w-7 items-center justify-center rounded-lg text-gray-400 transition hover:bg-red-50 hover:text-red-500">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>

                            <div class="p-4 space-y-4">

                                {{-- Selector de cantidad --}}
                                <div>
                                    <p class="text-xs font-medium text-gray-600 mb-2">
                                        Cantidad
                                        <span class="font-normal text-gray-400">(toca un número; tócalo de nuevo para volver a 1)</span>
                                    </p>
                                    <div class="flex flex-wrap gap-2">
                                        <template x-for="n in [1,2,3,4,5,6,7,8,9,10]" :key="n">
                                            <button
                                                type="button"
                                                @click="setCantidad(gi, n)"
                                                :class="grupo.cantidad === n
                                                    ? 'bg-[#7C3AED] text-white ring-2 ring-[#7C3AED] ring-offset-1 scale-110 shadow-md'
                                                    : 'bg-white border border-gray-300 text-gray-600 hover:border-[#7C3AED] hover:text-[#7C3AED]'"
                                                class="h-8 w-8 rounded-lg text-sm font-semibold transition-all duration-150 flex items-center justify-center"
                                                x-text="n">
                                            </button>
                                        </template>
                                    </div>
                                </div>

                                {{-- Filas de artículos --}}
                                <div class="space-y-3">
                                    <template x-for="(articulo, ai) in grupo.articulos" :key="ai">
                                        <div class="flex flex-wrap items-end gap-3 rounded-xl border border-gray-200 bg-white px-4 py-3">

                                            {{-- Número de artículo --}}
                                            <span class="flex-shrink-0 text-xs font-bold text-gray-400 w-5 text-center" x-text="ai + 1"></span>

                                            {{-- Marca --}}
                                            <div class="flex-1 min-w-[120px]">
                                                <label class="block text-xs font-medium text-gray-500 mb-1">Marca <span class="text-red-400">*</span></label>
                                                <input
                                                    type="text"
                                                    :name="`grupos[${gi}][articulos][${ai}][marca]`"
                                                    x-model="articulo.marca"
                                                    required
                                                    placeholder="Ej: Dell, Apple…"
                                                    class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-[#7C3AED] focus:outline-none focus:ring-2 focus:ring-[#7C3AED]/20 transition-all" />
                                            </div>

                                            {{-- Modelo --}}
                                            <div class="flex-1 min-w-[140px]">
                                                <label class="block text-xs font-medium text-gray-500 mb-1">Modelo <span class="text-red-400">*</span></label>
                                                <input
                                                    type="text"
                                                    :name="`grupos[${gi}][articulos][${ai}][modelo]`"
                                                    x-model="articulo.modelo"
                                                    required
                                                    placeholder="Ej: Inspiron 15…"
                                                    class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-[#7C3AED] focus:outline-none focus:ring-2 focus:ring-[#7C3AED]/20 transition-all" />
                                            </div>

                                            {{-- N° de serie --}}
                                            <div class="flex-1 min-w-[120px]">
                                                <label class="block text-xs font-medium text-gray-500 mb-1">N.° de serie <span class="text-gray-400 font-normal">(opcional)</span></label>
                                                <input
                                                    type="text"
                                                    :name="`grupos[${gi}][articulos][${ai}][numero_serie]`"
                                                    x-model="articulo.numero_serie"
                                                    placeholder="Ej: SN123456"
                                                    class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-[#7C3AED] focus:outline-none focus:ring-2 focus:ring-[#7C3AED]/20 transition-all" />
                                            </div>

                                        </div>
                                    </template>
                                </div>

                            </div>
                        </div>
                    </template>
                </div>

                {{-- Botón agregar tipo --}}
                <button
                    type="button"
                    @click="agregarGrupo()"
                    class="mt-4 flex w-full items-center justify-center gap-2 rounded-xl border-2 border-dashed border-[#7C3AED]/30 py-3 text-sm font-medium text-[#7C3AED] transition hover:border-[#7C3AED] hover:bg-[#7C3AED]/5">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Agregar otro tipo de dispositivo
                </button>

            </div>
        </div>
        {{-- BLOQUE 3: Nivel y técnico --}}
        <div class="bg-white rounded-2xl shadow-md border border-gray-100" style="overflow: visible !important;">
            <div class="border-b border-gray-100 bg-gradient-to-r from-[#7C3AED]/5 to-[#EC4899]/5 px-4 py-4 md:px-6">
                <legend class="text-lg font-semibold text-[#2D1B69] flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#7C3AED]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    Clasificación de la reparación
                </legend>
            </div>
            <div class="space-y-4 p-4 md:p-6">
                <div>
                    <label for="nivel_id" class="mb-1 block text-sm font-medium text-gray-700">Nivel de reparación <span class="text-red-500">*</span></label>

                    <x-select-nivel
                        :niveles="$niveles" />
                    <div id="descripcion-nivel" class="mt-2 text-xs text-gray-500 bg-gray-50 p-2 rounded-lg">
                        Selecciona un nivel para ver su descripción.
                    </div>
                </div>

                <div>
                    <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Técnico asignado <span class="text-gray-400 text-xs">(opcional)</span></label>
                    <x-select-tecnico
                        :tecnicos="$tecnicos"
                        :selected="old('user_id')"
                        :showStats="true"
                        placeholder="Sin asignar" />
                </div>
            </div>
        </div>

        {{-- BLOQUE 4: Problema y costo --}}
        <div class="bg-white rounded-2xl shadow-md border border-gray-100" style="overflow: visible !important;">
            <div class="border-b border-gray-100 bg-gradient-to-r from-[#7C3AED]/5 to-[#EC4899]/5 px-4 py-4 md:px-6">
                <legend class="text-lg font-semibold text-[#2D1B69] flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#7C3AED]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Descripción del problema
                </legend>
            </div>
            <div class="space-y-4 p-4 md:p-6">
                <div>
                    <label for="problema_reportado" class="block text-sm font-medium text-gray-700 mb-1">Problema reportado por el cliente <span class="text-red-500">*</span></label>
                    <textarea id="problema_reportado" name="problema_reportado" rows="4" required
                        class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-[#7C3AED]/20 focus:border-[#7C3AED] transition-all resize-none">{{ old('problema_reportado') }}</textarea>
                </div>
                <div>
                    <label for="costo_estimado" class="block text-sm font-medium text-gray-700 mb-1">Costo estimado <span class="text-gray-400 text-xs">(opcional)</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                        <input type="number" id="costo_estimado" name="costo_estimado" value="{{ old('costo_estimado') }}" step="0.01" min="0"
                            class="w-full pl-7 pr-4 py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-[#7C3AED]/20 focus:border-[#7C3AED] transition-all" />
                    </div>
                </div>
            </div>
        </div>

        {{-- Botones de acción --}}
        <div class="flex flex-col gap-3 pt-2 sm:flex-row sm:flex-wrap md:pt-4">
            <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-[#7C3AED] px-6 py-2.5 font-medium text-white shadow-md transition-all hover:bg-[#6D28D9] hover:shadow-lg sm:w-auto">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Crear orden
            </button>
            <a href="{{ route('panel.inicio') }}" class="flex w-full items-center justify-center gap-2 rounded-xl bg-gray-100 px-6 py-2.5 font-medium text-gray-700 transition-all hover:bg-gray-200 sm:w-auto">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Cancelar
            </a>
        </div>
    </form>
</div>
<!--     -->
{{-- Datos de niveles para JS (descripción dinámica) --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const selectNivel = document.getElementById('nivel_id');
        const descNivel = document.getElementById('descripcion-nivel');

        if (selectNivel && descNivel) {
            selectNivel.addEventListener('change', function() {
                const nivel = niveles[this.value];
                if (nivel) {
                    descNivel.innerHTML = `
                        <div class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-[#7C3AED] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><use href="/sprite.svg#clipboard"/></svg>
                            <div>
                                <strong class="font-medium text-gray-700">${nivel.nombre}</strong>
                                <p class="text-gray-500 text-xs mt-0.5">${nivel.descripcion || 'Sin descripción adicional'}</p>
                                <span class="inline-flex items-center gap-1 mt-1 text-[10px] bg-[#7C3AED]/10 text-[#7C3AED] px-2 py-0.5 rounded-full"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><use href="/sprite.svg#stopwatch"/></svg> Tiempo estimado: ${nivel.horas_sla} horas</span>
                            </div>
                        </div>
                    `;
                } else {
                    descNivel.innerHTML = 'Selecciona un nivel para ver su descripción.';
                }
            });

            // Trigger inicial si hay un valor seleccionado
            if (selectNivel.value) {
                selectNivel.dispatchEvent(new Event('change'));
            }
        }
    });
</script>
@endpush
@endsection
