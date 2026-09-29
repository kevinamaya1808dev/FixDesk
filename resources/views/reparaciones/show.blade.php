@extends('plantillas.base')

@section('titulo-pestana', $reparacion->folio)

@section('contenido-principal')

@php
    $modoHistorial = request()->query('modo') === 'historial';
@endphp

<div class="mx-auto max-w-6xl space-y-5 md:space-y-6">
    {{-- Alerta de éxito --}}
    @if(session('success'))
    <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 p-4 rounded-lg shadow-sm flex items-center gap-3" role="alert">
        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <p class="font-medium">{{ session('success') }}</p>
    </div>
    @endif

    {{-- Banner informativo en modo historial --}}
    @if($modoHistorial)
    <div class="bg-amber-50 border-l-4 border-amber-400 text-amber-800 p-4 rounded-lg shadow-sm flex items-center gap-3">
        <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
        </svg>
        <p class="font-medium text-sm">Estás viendo esta orden en modo <strong>reporte histórico</strong>. La información es de solo lectura.</p>
    </div>
    @endif

    {{-- Ficha principal --}}
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        {{-- Header de la orden --}}
        <div class="bg-gradient-to-r from-[#2D1B69] to-[#1E1B2E] px-4 py-5 md:px-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3 min-w-0">
                    {{-- Botón volver: en modo historial regresa al perfil del cliente, si no a órdenes --}}
                    @if($modoHistorial)
                    <a href="{{ route('clientes.show', $reparacion->cliente) }}"
                       title="Volver al perfil del cliente"
                       class="flex-shrink-0 inline-flex items-center justify-center h-9 w-9 rounded-xl bg-white/10 text-white ring-1 ring-white/20 transition hover:bg-white/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    @else
                    <a href="{{ route('reparaciones.index') }}"
                       title="Volver a Órdenes"
                       class="flex-shrink-0 inline-flex items-center justify-center h-9 w-9 rounded-xl bg-white/10 text-white ring-1 ring-white/20 transition hover:bg-white/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    @endif
                    <div class="min-w-0">
                        <h1 class="text-2xl md:text-3xl font-bold text-white">{{ $reparacion->folio }}</h1>
                        <p class="text-purple-200 text-sm mt-1">
                            @if($modoHistorial)
                                Reporte histórico de orden
                            @else
                                Orden de reparación
                            @endif
                        </p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                    @php
                    $estadoColors = [
                    'Recibido' => 'bg-slate-100 text-slate-700',
                    'En Revisión' => 'bg-blue-100 text-blue-700',
                    'Esperando Pieza' => 'bg-amber-100 text-amber-700',
                    'Reparado' => 'bg-emerald-100 text-emerald-700',
                    'Retardo' => 'bg-red-100 text-red-700',
                    'Entregado' => 'bg-gray-100 text-gray-500',
                    'Cancelado' => 'bg-rose-100 text-rose-700',
                    ];
                    $colorClass = $estadoColors[$reparacion->estado] ?? 'bg-gray-100 text-gray-600';
                    @endphp
                    <span class="px-3 py-1.5 rounded-full text-xs font-medium {{ $colorClass }}">
                        {{ $reparacion->estado }}
                    </span>
                    @if($reparacion->estaRetrasada())
                    <span class="inline-flex items-center gap-1 bg-red-100 text-red-700 px-3 py-1.5 rounded-full text-xs font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        RETARDO — Hora límite superada
                    </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Grid de información --}}
        <div class="grid grid-cols-1 gap-4 p-4 md:grid-cols-2 md:gap-6 md:p-6">
            {{-- Datos del dispositivo --}}
            <div class="rounded-xl bg-gray-50 p-4 md:p-5">
                <h2 class="text-lg font-semibold text-[#2D1B69] flex items-center gap-2 mb-4">
                    <svg class="w-5 h-5 text-[#7C3AED]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 3h14a2 2 0 011 2v14a2 2 0 01-1 2H5a2 2 0 01-2-2V5a2 2 0 012-2z"></path>
                    </svg>
                    Dispositivo
                </h2>
                <dl class="space-y-2">
                    <div class="flex flex-wrap">
                        <dt class="w-32 text-gray-500 text-sm">Tipo:</dt>
                        <dd class="text-gray-800 font-medium">{{ $reparacion->tipo_equipo }}</dd>
                    </div>
                    <div class="flex flex-wrap">
                        <dt class="w-32 text-gray-500 text-sm">Marca:</dt>
                        <dd class="text-gray-800">{{ $reparacion->marca }}</dd>
                    </div>
                    <div class="flex flex-wrap">
                        <dt class="w-32 text-gray-500 text-sm">Modelo:</dt>
                        <dd class="text-gray-800">{{ $reparacion->modelo }}</dd>
                    </div>
                    <div class="flex flex-wrap">
                        <dt class="w-32 text-gray-500 text-sm">Serie:</dt>
                        <dd class="text-gray-800">{{ $reparacion->numero_serie ?? '—' }}</dd>
                    </div>
                    <div class="flex flex-wrap">
                        <dt class="w-32 text-gray-500 text-sm">Nivel:</dt>
                        <dd class="text-gray-800"><span class="bg-[#7C3AED]/10 text-[#7C3AED] px-2 py-0.5 rounded-full text-xs">Nivel {{ $reparacion->nivel->nivel }} — {{ $reparacion->nivel->nombre }}</span></dd>
                    </div>
                    <div class="flex flex-wrap">
                        <dt class="w-32 text-gray-500 text-sm">Problema:</dt>
                        <dd class="text-gray-800">{{ $reparacion->problema_reportado }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Datos del cliente --}}
            <div class="rounded-xl bg-gray-50 p-4 md:p-5">
                <h2 class="text-lg font-semibold text-[#2D1B69] flex items-center gap-2 mb-4">
                    <svg class="w-5 h-5 text-[#7C3AED]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Cliente
                </h2>
                <dl class="space-y-2">
                    <div class="flex flex-wrap">
                        <dt class="w-32 text-gray-500 text-sm">Nombre:</dt>
                        <dd class="text-gray-800">
                            @if($modoHistorial)
                                {{ $reparacion->cliente->nombre }}
                            @else
                                <a href="{{ route('clientes.show', $reparacion->cliente) }}" class="text-[#7C3AED] hover:text-[#2D1B69]">{{ $reparacion->cliente->nombre }}</a>
                            @endif
                        </dd>
                    </div>
                    <div class="flex flex-wrap">
                        <dt class="w-32 text-gray-500 text-sm">Teléfono:</dt>
                        <dd class="text-gray-800">{{ $reparacion->cliente->telefono ?? '—' }}</dd>
                    </div>
                    <div class="flex flex-wrap">
                        <dt class="w-32 text-gray-500 text-sm">Email:</dt>
                        <dd class="text-gray-800">{{ $reparacion->cliente->email ?? '—' }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Control de tiempos --}}
            <div class="rounded-xl bg-gray-50 p-4 md:p-5">
                <h2 class="text-lg font-semibold text-[#2D1B69] flex items-center gap-2 mb-4">
                    <svg class="w-5 h-5 text-[#7C3AED]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Control de tiempos
                </h2>
                <dl class="space-y-2">
                    <div class="flex flex-wrap">
                        <dt class="w-32 text-gray-500 text-sm">Ingreso:</dt>
                        <dd class="text-gray-800">{{ $reparacion->hora_ingreso->format('d/m/Y H:i') }}</dd>
                    </div>
                    <div class="flex flex-wrap">
                        <dt class="w-32 text-gray-500 text-sm">Hora límite (SLA):</dt>
                        <dd class="{{ $reparacion->estaRetrasada() ? 'text-red-600 font-medium' : 'text-gray-800' }}">{{ $reparacion->hora_limite->format('d/m/Y H:i') }}</dd>
                    </div>
                    @if($reparacion->hora_fin)
                    <div class="flex flex-wrap">
                        <dt class="w-32 text-gray-500 text-sm">Finalizado:</dt>
                        <dd class="text-gray-800">{{ $reparacion->hora_fin->format('d/m/Y H:i') }}</dd>
                    </div>
                    @endif
                    <div class="flex flex-wrap">
                        <dt class="w-32 text-gray-500 text-sm">Técnico:</dt>
                        <dd class="text-gray-800">{{ $reparacion->tecnico->name ?? 'Sin asignar' }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Enlace de seguimiento: oculto en modo historial --}}
            @if(!$modoHistorial)
            <div class="rounded-xl bg-gray-50 p-4 md:p-5">
                <h2 class="text-lg font-semibold text-[#2D1B69] flex items-center gap-2 mb-4">
                    <svg class="w-5 h-5 text-[#7C3AED]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                    </svg>
                    Enlace del cliente
                </h2>
                <p class="text-sm text-gray-600 mb-3">Comparte este enlace con el cliente para que pueda ver el estado de su orden:</p>
                <div class="flex flex-col sm:flex-row gap-2">
                    <input type="text" id="enlace-seguimiento" readonly
                        value="{{ url('/seguimiento/' . $reparacion->token_seguimiento) }}"
                        class="min-w-0 flex-1 rounded-xl border border-gray-300 bg-gray-100 px-4 py-2 text-sm text-gray-600 focus:outline-none" />
                    <button onclick="navigator.clipboard.writeText(document.getElementById('enlace-seguimiento').value)"
                        class="bg-[#7C3AED] hover:bg-[#6D28D9] text-white px-4 py-2 rounded-xl text-sm font-medium transition-all flex items-center gap-2 justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path>
                        </svg>
                        Copiar enlace
                    </button>
                </div>
            </div>
            @else
            {{-- En modo historial: mostrar diagnóstico técnico como 4to recuadro del grid --}}
            <div class="rounded-xl bg-gray-50 p-4 md:p-5">
                <h2 class="text-lg font-semibold text-[#2D1B69] flex items-center gap-2 mb-4">
                    <svg class="w-5 h-5 text-[#7C3AED]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Diagnóstico registrado
                </h2>
                <p class="text-gray-700 text-sm leading-relaxed">
                    {{ $reparacion->diagnostico_tecnico ?? '—' }}
                </p>
                @if($reparacion->costo_final)
                <div class="mt-3 pt-3 border-t border-gray-200">
                    <span class="text-gray-500 text-sm">Costo final: </span>
                    <span class="font-semibold text-[#2D1B69]">${{ number_format($reparacion->costo_final, 2) }}</span>
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>

    {{-- Secciones de edición: solo en modo normal --}}
    @if(!$modoHistorial)

    {{-- Actualizar orden --}}
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="border-b border-gray-100 bg-gradient-to-r from-[#7C3AED]/5 to-[#EC4899]/5 px-4 py-4 md:px-6">
            <h2 class="text-lg font-semibold text-[#2D1B69] flex items-center gap-2">
                <svg class="w-5 h-5 text-[#7C3AED]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Actualizar orden
            </h2>
        </div>
        <form method="POST" action="{{ route('reparaciones.update', $reparacion) }}" class="space-y-4 p-4 md:p-6">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="estado" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select id="estado" name="estado" class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-[#7C3AED]/20 focus:border-[#7C3AED]">
                        @foreach(['Recibido', 'En Revisión', 'Esperando Pieza', 'Retardo', 'Reparado', 'Entregado', 'Cancelado'] as $estado)
                        <option value="{{ $estado }}" {{ $reparacion->estado === $estado ? 'selected' : '' }}>
                            {{ $estado }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Técnico asignado</label>
                    <select id="user_id" name="user_id" class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-[#7C3AED]/20 focus:border-[#7C3AED]">
                        <option value="">— Sin asignar —</option>
                        @foreach($tecnicos as $tec)
                        <option value="{{ $tec->id }}" {{ $reparacion->user_id == $tec->id ? 'selected' : '' }}>
                            {{ $tec->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label for="diagnostico_tecnico" class="block text-sm font-medium text-gray-700 mb-1">Diagnóstico técnico</label>
                <textarea id="diagnostico_tecnico" name="diagnostico_tecnico" rows="3"
                    class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-[#7C3AED]/20 focus:border-[#7C3AED] resize-none">{{ old('diagnostico_tecnico', $reparacion->diagnostico_tecnico) }}</textarea>
            </div>

            <div>
                <label for="comentario_retardo" class="block text-sm font-medium text-gray-700 mb-1">Comentario de justificación <span class="text-gray-400 text-xs">(si hay retardo)</span></label>
                <textarea id="comentario_retardo" name="comentario_retardo" rows="2"
                    class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-[#7C3AED]/20 focus:border-[#7C3AED] resize-none">{{ old('comentario_retardo', $reparacion->comentario_retardo) }}</textarea>
            </div>

            <div>
                <label for="costo_final" class="block text-sm font-medium text-gray-700 mb-1">Costo final</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                    <input type="number" id="costo_final" name="costo_final" value="{{ old('costo_final', $reparacion->costo_final) }}" step="0.01" min="0"
                        class="w-full pl-7 pr-4 py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-[#7C3AED]/20 focus:border-[#7C3AED]" />
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-[#7C3AED] px-6 py-2.5 font-medium text-white shadow-md transition-all hover:bg-[#6D28D9] hover:shadow-lg sm:w-auto">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>

    {{-- Escalar nivel --}}
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="border-b border-gray-100 bg-gradient-to-r from-[#7C3AED]/5 to-[#EC4899]/5 px-4 py-4 md:px-6">
            <h2 class="text-lg font-semibold text-[#2D1B69] flex items-center gap-2">
                <svg class="w-5 h-5 text-[#7C3AED]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
                Escalar nivel
            </h2>
        </div>
        <div class="p-4 md:p-6">
            <p class="text-sm text-gray-600 mb-4">Nivel actual: <strong class="text-[#7C3AED]">{{ $reparacion->nivel->nombre }}</strong></p>

            @if($errors->any())
            <div class="mb-4 bg-red-50 border-l-4 border-red-500 rounded-lg p-4">
                <ul class="list-disc list-inside text-red-700 text-sm">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('reparaciones.escalar', $reparacion) }}" class="space-y-4">
                @csrf
                <div>
                    <label for="nivel_nuevo_id" class="block text-sm font-medium text-gray-700 mb-1">Nuevo nivel</label>
                    <select id="nivel_nuevo_id" name="nivel_nuevo_id" required class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-[#7C3AED]/20 focus:border-[#7C3AED]">
                        <option value="">— Seleccionar nuevo nivel —</option>
                        @foreach($niveles as $nivel)
                        @if($nivel->id != $reparacion->nivel_id)
                        <option value="{{ $nivel->id }}">
                            Nivel {{ $nivel->nivel }} — {{ $nivel->nombre }} (SLA: {{ $nivel->horas_sla }}h)
                        </option>
                        @endif
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="motivo" class="block text-sm font-medium text-gray-700 mb-1">Motivo del escalamiento</label>
                    <textarea id="motivo" name="motivo" rows="3" required
                        class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-[#7C3AED]/20 focus:border-[#7C3AED] resize-none"
                        placeholder="Ej: Se detectó falla en la placa madre, requiere microsoldadura.">{{ old('motivo') }}</textarea>
                </div>
                <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-amber-500 px-6 py-2.5 font-medium text-white shadow-md transition-all hover:bg-amber-600 hover:shadow-lg sm:w-auto">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"></path>
                    </svg>
                    Escalar nivel
                </button>
            </form>
        </div>
    </div>

    @endif {{-- fin !$modoHistorial --}}

    {{-- Historial de escalamientos: visible en ambos modos --}}
    @if($reparacion->escalamientos->isNotEmpty())
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="border-b border-gray-100 bg-gradient-to-r from-[#7C3AED]/5 to-[#EC4899]/5 px-4 py-4 md:px-6">
            <h2 class="text-lg font-semibold text-[#2D1B69] flex items-center gap-2">
                <svg class="w-5 h-5 text-[#7C3AED]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                Historial de escalamientos
            </h2>
        </div>
        <div class="p-4 md:p-6">
            <div class="space-y-3">
                @foreach($reparacion->escalamientos as $esc)
                <div class="border-l-4 border-[#7C3AED] pl-4 py-2 bg-gray-50 rounded-r-lg">
                    <div class="mb-1 flex flex-wrap items-center gap-2">
                        <span class="font-semibold text-[#2D1B69]">{{ $esc->nivelAnterior->nombre }}</span>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                        <span class="font-semibold text-[#7C3AED]">{{ $esc->nivelNuevo->nombre }}</span>
                        <span class="w-full text-xs text-gray-400 sm:ml-auto sm:w-auto">por {{ $esc->user->name ?? 'Sistema' }} el {{ $esc->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <p class="text-sm text-gray-600 italic">"{{ $esc->motivo }}"</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- Chat --}}
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="border-b border-gray-100 bg-gradient-to-r from-[#7C3AED]/5 to-[#EC4899]/5 px-4 py-4 md:px-6">
            <h2 class="text-lg font-semibold text-[#2D1B69] flex items-center gap-2">
                <svg class="w-5 h-5 text-[#7C3AED]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                @if($modoHistorial) Historial de mensajes @else Chat con el cliente @endif
            </h2>
        </div>
        <div class="p-4 md:p-6">

            @if($modoHistorial)
            {{-- MODO HISTORIAL: lista estática de todos los mensajes --}}
            @php $mensajesHistorial = $reparacion->mensajes()->orderBy('created_at')->get(); @endphp
            @if($mensajesHistorial->isEmpty())
                <p class="text-center text-gray-400 text-sm py-6">No hubo mensajes en esta orden.</p>
            @else
            <div class="space-y-3">
                @foreach($mensajesHistorial as $msg)
                <div class="flex flex-col {{ $msg->es_del_cliente ? 'items-start' : 'items-end' }}">
                    <div class="max-w-[92%] md:max-w-[80%] {{ $msg->es_del_cliente ? 'bg-gray-200 text-gray-800' : 'bg-[#7C3AED] text-white' }} rounded-2xl px-4 py-2">
                        <div class="flex items-center gap-2 mb-1">
                            <strong class="text-sm">{{ $msg->autor ?? ($msg->es_del_cliente ? $reparacion->cliente->nombre : 'Taller') }}</strong>
                            <time class="text-[10px] opacity-70">{{ $msg->created_at->format('d/m/Y H:i') }}</time>
                        </div>
                        <p class="text-sm break-words">{{ $msg->contenido }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            @else
            {{-- MODO NORMAL: chat con polling y formulario --}}
            @php
                $waMensaje = 'Hola ' . ($reparacion->cliente->nombre ?? 'cliente')
                    . ', te contactamos del taller sobre tu orden con folio '
                    . $reparacion->folio . '. Estado actual: ' . $reparacion->estado . '.';

                $waTelefono = preg_replace('/\D+/', '', $reparacion->cliente->telefono ?? '');
                if (strlen($waTelefono) === 10) {
                    $waTelefono = '52' . $waTelefono;
                }
                $waUrl = ($waTelefono !== '')
                    ? 'https://wa.me/' . $waTelefono . '?text=' . rawurlencode($waMensaje)
                    : null;
            @endphp

            <div id="chat-mensajes" class="mb-4 h-80 space-y-3 overflow-y-auto rounded-xl bg-gray-50 p-3 md:h-96 md:p-4">
                <div class="text-center text-gray-400 text-sm">Cargando mensajes...</div>
            </div>

            <form id="chat-form" class="space-y-3">
                @csrf
                <div>
                    <label for="chat-input" class="block text-sm font-medium text-gray-700 mb-1">Mensaje</label>
                    <textarea id="chat-input" name="contenido" rows="2"
                        class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-[#7C3AED]/20 focus:border-[#7C3AED] resize-none"
                        placeholder="Escribe un mensaje..."></textarea>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <button type="submit" class="flex items-center justify-center gap-2 rounded-xl bg-[#7C3AED] px-6 py-2.5 font-medium text-white shadow-md transition-all hover:bg-[#6D28D9] hover:shadow-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        Enviar mensaje
                    </button>

                    @if($waUrl)
                    <a href="{{ $waUrl }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       title="Abrir chat de WhatsApp con {{ $reparacion->cliente->nombre ?? 'el cliente' }}"
                       class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#25D366] px-4 py-2.5 font-medium text-white shadow-md transition-all hover:bg-[#1ebe5a] hover:shadow-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5" aria-hidden="true">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                        </svg>
                        WhatsApp
                    </a>
                    @else
                    <span title="El cliente no tiene teléfono registrado"
                          class="inline-flex items-center justify-center gap-2 rounded-xl bg-gray-200 px-4 py-2.5 font-medium text-gray-400 cursor-not-allowed select-none">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5" aria-hidden="true">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                        </svg>
                        WhatsApp
                    </span>
                    @endif
                </div>
            </form>
            @endif

        </div>
    </div>
</div>

@if(!$modoHistorial)
<script>
    const chatUrl = "{{ route('reparaciones.mensajes.index', $reparacion) }}";
    const chatStoreUrl = "{{ route('reparaciones.mensajes.store', $reparacion) }}";
    const csrfToken = "{{ csrf_token() }}";

    let lastMessageId = 0;

    function renderMensajes(mensajes) {
        const contenedor = document.getElementById('chat-mensajes');
        if (mensajes.length === 0) {
            contenedor.innerHTML = '<div class="text-center text-gray-400 text-sm">Aún no hay mensajes.</div>';
            return;
        }
        contenedor.innerHTML = mensajes.map(m => `
            <div class="flex flex-col ${m.es_del_cliente ? 'items-start' : 'items-end'}">
                <div class="max-w-[92%] md:max-w-[80%] ${m.es_del_cliente ? 'bg-gray-200 text-gray-800' : 'bg-[#7C3AED] text-white'} rounded-2xl px-4 py-2">
                    <div class="flex items-center gap-2 mb-1">
                        <strong class="text-sm">${m.autor}</strong>
                        <time class="text-[10px] opacity-70">${m.fecha}</time>
                    </div>
                    <p class="text-sm break-words">${m.contenido}</p>
                </div>
            </div>
        `).join('');
        contenedor.scrollTop = contenedor.scrollHeight;
    }

    async function cargarMensajes() {
        try {
            const res = await fetch(chatUrl);
            if (!res.ok) return;
            const mensajes = await res.json();
            if (mensajes.length === 0) return;
            const maxId = Math.max(...mensajes.map(m => m.id));
            if (maxId <= lastMessageId) return;
            lastMessageId = maxId;
            renderMensajes(mensajes);
        } catch (e) {}
    }

    document.getElementById('chat-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const input = document.getElementById('chat-input');
        const btn = this.querySelector('button[type="submit"]');
        const contenido = input.value.trim();
        if (!contenido) return;

        btn.disabled = true;
        btn.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg> Enviando...';

        try {
            const res = await fetch(chatStoreUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ contenido })
            });

            if (res.ok) {
                input.value = '';
                cargarMensajes();
            } else if (res.status === 419) {
                alert('Tu sesión ha expirado. La página se recargará para continuar.');
                location.reload();
            } else {
                alert('No se pudo enviar el mensaje. Intenta de nuevo.');
            }
        } catch (err) {
            alert('Sin conexión. Verifica tu red.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg> Enviar mensaje';
        }
    });

    cargarMensajes();
    setInterval(cargarMensajes, 5000);
</script>
@endif

@endsection