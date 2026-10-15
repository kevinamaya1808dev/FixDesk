@extends('plantillas.base')

@section('titulo-pestana', $cliente->nombre)

@section('contenido-principal')

{{-- Mapa de colores de estado declarado una sola vez para toda la vista --}}
@php
$estadoColors = [
    'Recibido'        => 'bg-slate-100 text-slate-700',
    'En Revisión'     => 'bg-blue-100 text-blue-700',
    'Esperando Pieza' => 'bg-amber-100 text-amber-700',
    'Reparado'        => 'bg-emerald-100 text-emerald-700',
    'Entregado'       => 'bg-gray-100 text-gray-500',
    'Cancelado'       => 'bg-rose-100 text-rose-700',
    'Retardo'         => 'bg-red-100 text-red-700',
];
@endphp

<div class="mx-auto max-w-6xl space-y-6">

    {{-- Perfil del cliente --}}
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">

        {{-- Header oscuro con nombre y botón de nueva orden --}}
        <div class="bg-gradient-to-r from-[#2D1B69] to-[#1E1B2E] px-4 py-5 md:px-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                {{-- Botón volver + avatar + nombre --}}
                <div class="flex min-w-0 items-center gap-3">
                    <a href="{{ route('clientes.index') }}" title="Volver a Clientes"
                        class="flex-shrink-0 inline-flex h-9 w-9 items-center justify-center rounded-xl bg-white/10 text-white ring-1 ring-white/20 transition hover:bg-white/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-full bg-[#7C3AED]/20 md:h-16 md:w-16">
                        <span class="text-xl font-bold text-white md:text-2xl">
                            {{ substr($cliente->nombre, 0, 2) }}
                        </span>
                    </div>
                    <div class="min-w-0">
                        <h1 class="text-2xl md:text-3xl font-bold text-white">{{ $cliente->nombre }}</h1>
                        <p class="text-purple-200 text-sm mt-1">Perfil del cliente</p>
                    </div>
                </div>

                {{-- Botón nueva orden para este cliente --}}
                <a href="{{ route('reparaciones.create') }}?cliente_id={{ $cliente->id }}"
                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-[#EC4899] px-5 py-2.5 text-sm font-medium text-white shadow-md transition-all hover:bg-[#DB2777] hover:shadow-lg sm:w-auto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nueva orden para este cliente
                </a>
            </div>
        </div>

        {{-- Datos de contacto --}}
        <div class="p-4 md:p-6">
            <h2 class="mb-4 flex items-center gap-2 text-lg font-semibold text-[#2D1B69]">
                <svg class="w-5 h-5 text-[#7C3AED]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Información de contacto
            </h2>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

                {{-- Teléfono --}}
                <div class="rounded-xl bg-gray-50 p-4">
                    <p class="mb-1 flex items-center gap-2 text-sm text-gray-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        Teléfono
                    </p>
                    <p class="font-medium text-gray-800">{{ $cliente->telefono ?? '—' }}</p>
                </div>

                {{-- Email --}}
                <div class="rounded-xl bg-gray-50 p-4">
                    <p class="mb-1 flex items-center gap-2 text-sm text-gray-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Email
                    </p>
                    <p class="font-medium text-gray-800">{{ $cliente->email ?? '—' }}</p>
                </div>

                {{-- Dirección --}}
                <div class="rounded-xl bg-gray-50 p-4">
                    <p class="mb-1 flex items-center gap-2 text-sm text-gray-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Dirección
                    </p>
                    <p class="font-medium text-gray-800">{{ $cliente->direccion ?? '—' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Historial de reparaciones --}}
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">

        <div class="border-b border-gray-100 bg-gradient-to-r from-[#7C3AED]/5 to-[#EC4899]/5 px-4 py-4 md:px-6">
            <h2 class="flex items-center gap-2 text-lg font-semibold text-[#2D1B69]">
                <svg class="w-5 h-5 text-[#7C3AED]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Historial de reparaciones
            </h2>
        </div>

        {{-- Vista móvil --}}
        <div class="space-y-4 p-4 md:hidden">
            @forelse($reparaciones as $orden)
                <x-order-mobile-card :orden="$orden" :show-cliente="false"
                    date-label="Fecha" :date-value="$orden->created_at->format('d/m/Y')" />
            @empty
                <div class="rounded-2xl bg-gray-50 px-6 py-10 text-center">
                    <p class="font-medium text-gray-400">Este cliente no tiene órdenes registradas</p>
                    <a href="{{ route('reparaciones.create') }}?cliente_id={{ $cliente->id }}"
                        class="mt-2 inline-flex text-sm text-[#7C3AED] hover:underline">
                        Crear primera orden
                    </a>
                </div>
            @endforelse
        </div>

        {{-- Vista desktop --}}
        <div class="hidden overflow-x-auto md:block">
            <table class="w-full">
                <thead class="border-b border-gray-200 bg-gray-50">
                    <tr>
                        <th class="whitespace-nowrap px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Folio</th>
                        <th class="whitespace-nowrap px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Equipo</th>
                        <th class="whitespace-nowrap px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Nivel</th>
                        <th class="whitespace-nowrap px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Estado</th>
                        <th class="whitespace-nowrap px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Técnico</th>
                        <th class="whitespace-nowrap px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Fecha</th>
                        <th class="whitespace-nowrap px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($reparaciones as $orden)
                    {{-- Fila con tono ámbar si el cliente es mayorista --}}
                    <tr class="{{ $cliente->es_mayorista ? 'bg-amber-50/60 hover:bg-amber-100/70 font-medium' : 'hover:bg-purple-50/30' }} transition-colors duration-200">

                        <td class="whitespace-nowrap px-6 py-4">
                            <span class="font-semibold text-[#2D1B69]">{{ $orden->folio }}</span>
                        </td>

                        <td class="whitespace-nowrap px-6 py-4 text-gray-700">
                            {{ $orden->marca }} {{ $orden->modelo }}
                        </td>

                        <td class="whitespace-nowrap px-6 py-4">
                            <span class="rounded-full bg-[#7C3AED]/10 px-2 py-1 text-xs font-medium text-[#7C3AED]">
                                {{ $orden->nivel->nombre }}
                            </span>
                        </td>

                        {{-- Color del estado tomado del mapa declarado arriba --}}
                        <td class="whitespace-nowrap px-6 py-4">
                            <span class="rounded-full px-2 py-1 text-xs font-medium {{ $estadoColors[$orden->estado] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $orden->estado }}
                            </span>
                        </td>

                        <td class="whitespace-nowrap px-6 py-4 text-gray-600">
                            {{ $orden->tecnico->name ?? '—' }}
                        </td>

                        <td class="whitespace-nowrap px-6 py-4 text-gray-600">
                            {{ $orden->created_at->format('d/m/Y') }}
                        </td>

                        {{-- Enlace en modo historial para no mostrar controles de edición --}}
                        <td class="whitespace-nowrap px-6 py-4">
                            <a href="{{ route('reparaciones.show', $orden) }}?modo=historial"
                                class="inline-flex items-center gap-1 text-sm font-medium text-[#7C3AED] transition-colors hover:text-[#2D1B69] group">
                                Ver
                                <svg class="w-3.5 h-3.5 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-gray-100">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                </div>
                                <p class="font-medium text-gray-400">Este cliente no tiene órdenes registradas</p>
                                <a href="{{ route('reparaciones.create') }}?cliente_id={{ $cliente->id }}"
                                    class="text-sm text-[#7C3AED] hover:underline">
                                    Crear primera orden →
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection