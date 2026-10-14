@extends('plantillas.base')

@section('titulo-pestana', 'Clientes')

@section('contenido-principal')

{{-- Encabezado con botón de nuevo cliente --}}
<div class="mb-6 md:mb-8">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold bg-[#1E1B2E] bg-clip-text text-transparent">
                Clientes del taller
            </h1>
            <p class="text-gray-500 text-sm mt-1">Gestiona y consulta el historial de tus clientes</p>
        </div>
        <a href="{{ route('clientes.create') }}"
            class="flex w-full items-center justify-center gap-2 rounded-xl bg-[#EC4899] px-5 py-2.5 text-sm font-medium text-white shadow-md transition-all hover:bg-[#DB2777] hover:shadow-lg sm:w-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo cliente
        </a>
    </div>
</div>

{{-- Filtros de búsqueda --}}
<section class="mb-6 rounded-2xl border border-purple-100 bg-white p-4 shadow-md md:p-5">
    <form method="GET" action="{{ route('clientes.index') }}"
        class="grid gap-3 lg:grid-cols-[minmax(260px,1fr)_auto_auto] lg:items-end">

        {{-- Campo de búsqueda por nombre --}}
        <div>
            <label for="buscar_cliente" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">
                Buscar cliente
            </label>
            <div class="relative">
                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m1.1-5.15a6.25 6.25 0 11-12.5 0 6.25 6.25 0 0112.5 0z"/>
                </svg>
                <input id="buscar_cliente" type="search" name="buscar" value="{{ $buscar }}"
                    maxlength="100" placeholder="Nombre del cliente"
                    class="w-full rounded-xl border border-gray-200 bg-white py-3 pl-10 pr-4 text-sm text-gray-700 placeholder:text-gray-400 focus:border-[#7C3AED] focus:outline-none focus:ring-2 focus:ring-[#7C3AED]/20 lg:min-h-[42px] lg:py-2.5">
            </div>
        </div>

        {{-- Filtro de mayoristas --}}
        <label class="flex items-center gap-3 rounded-xl border border-gray-200 px-4 py-3 text-sm font-medium text-gray-700 lg:min-h-[42px] lg:py-2.5">
            <input type="checkbox" name="mayoristas" value="1" @checked($soloMayoristas)
                class="h-4 w-4 rounded border-gray-300 text-amber-500 focus:ring-amber-400/30">
            Mostrar solo mayoristas
        </label>

        {{-- Botones buscar / limpiar --}}
        <div class="flex flex-col gap-2 sm:flex-row lg:items-end">
            <button type="submit"
                class="flex w-full items-center justify-center rounded-xl bg-[#1E1B2E] px-5 py-3 text-sm font-medium text-white transition hover:bg-[#2D1B69] lg:min-h-[42px] lg:w-auto lg:py-0">
                Buscar
            </button>
            @if($soloMayoristas || $buscar !== '')
            <a href="{{ route('clientes.index') }}"
                class="flex w-full items-center justify-center rounded-xl bg-gray-100 px-5 py-3 text-sm font-medium text-gray-600 transition hover:bg-gray-200 lg:min-h-[42px] lg:w-auto lg:py-0">
                Limpiar
            </a>
            @endif
        </div>
    </form>
</section>

{{-- Vista móvil: tarjetas --}}
<div class="space-y-4 md:hidden">
    @forelse($clientes as $cliente)
    <article class="rounded-2xl border {{ $cliente->es_mayorista ? 'border-amber-200 bg-amber-50/70' : 'border-gray-100 bg-white' }} p-4 shadow-md">
        <div class="flex items-start gap-3">
            {{-- Avatar con iniciales --}}
            <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-full bg-[#7C3AED]/10">
                <span class="text-sm font-medium uppercase text-[#7C3AED]">{{ substr($cliente->nombre, 0, 2) }}</span>
            </div>
            <div class="min-w-0 flex-1">
                <div class="flex min-w-0 flex-wrap items-center gap-2">
                    <h2 class="truncate font-semibold text-[#2D1B69]">{{ $cliente->nombre }}</h2>
                    @if($cliente->es_mayorista)
                    <span class="rounded-md border border-amber-200 bg-amber-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-amber-800">
                        Mayorista
                    </span>
                    @endif
                </div>
                <p class="truncate text-sm text-gray-500">{{ $cliente->email ?? 'Sin email' }}</p>
                <p class="text-sm text-gray-500">{{ $cliente->telefono ?? 'Sin teléfono' }}</p>
            </div>
            {{-- Contador de órdenes --}}
            <span class="rounded-full bg-[#7C3AED]/10 px-2.5 py-1 text-xs font-semibold text-[#7C3AED]">
                {{ $cliente->reparaciones_count }} orden{{ $cliente->reparaciones_count === 1 ? '' : 'es' }}
            </span>
        </div>
        <a href="{{ route('clientes.show', $cliente) }}"
            class="mt-4 inline-flex w-full items-center justify-center rounded-xl bg-[#7C3AED] px-4 py-2.5 text-sm font-medium text-white transition hover:bg-[#6D28D9]">
            Ver historial
        </a>
    </article>
    @empty
    <div class="rounded-2xl bg-white px-6 py-12 text-center shadow-md">
        <p class="font-medium text-gray-400">
            {{ $soloMayoristas || $buscar !== '' ? 'No hay clientes que coincidan con tu búsqueda' : 'No hay clientes registrados' }}
        </p>
        <a href="{{ route('clientes.create') }}" class="mt-2 inline-flex text-sm text-[#7C3AED] hover:underline">
            Crear primer cliente
        </a>
    </div>
    @endforelse
</div>

{{-- Vista desktop: tabla --}}
<div class="hidden overflow-hidden rounded-2xl bg-white shadow-lg md:block">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gradient-to-r from-[#2D1B69] to-[#1E1B2E]">
                <tr>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-purple-200 uppercase tracking-wider whitespace-nowrap">Nombre</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-purple-200 uppercase tracking-wider whitespace-nowrap">Teléfono</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-purple-200 uppercase tracking-wider whitespace-nowrap">Email</th>
                    <th class="text-center px-6 py-4 text-xs font-semibold text-purple-200 uppercase tracking-wider whitespace-nowrap">Órdenes</th>
                    <th class="text-center px-6 py-4 text-xs font-semibold text-purple-200 uppercase tracking-wider whitespace-nowrap">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($clientes as $cliente)
                <tr class="{{ $cliente->es_mayorista ? 'bg-amber-50/60 hover:bg-amber-100/70 font-medium' : 'hover:bg-purple-50/30' }} transition-colors duration-200">

                    {{-- Nombre con avatar e insignia mayorista --}}
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-[#7C3AED]/10">
                                <span class="text-sm font-medium uppercase text-[#7C3AED]">
                                    {{ substr($cliente->nombre, 0, 2) }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-[#2D1B69]">{{ $cliente->nombre }}</span>
                                @if($cliente->es_mayorista)
                                <span class="rounded-md border border-amber-200 bg-amber-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-amber-800 whitespace-nowrap">
                                    Mayorista
                                </span>
                                @endif
                            </div>
                        </div>
                    </td>

                    {{-- Teléfono --}}
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        {{ $cliente->telefono ?? '—' }}
                    </td>

                    {{-- Email --}}
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $cliente->email ?? '—' }}
                    </td>

                    {{-- Contador de órdenes --}}
                    <td class="px-6 py-4 text-center whitespace-nowrap">
                        <span class="inline-flex items-center justify-center rounded-full bg-[#7C3AED]/10 px-3 py-1 text-xs font-semibold text-[#7C3AED] min-w-[40px]">
                            {{ $cliente->reparaciones_count }}
                        </span>
                    </td>

                    {{-- Enlace al perfil --}}
                    <td class="px-6 py-4 text-center whitespace-nowrap">
                        <a href="{{ route('clientes.show', $cliente) }}"
                            class="inline-flex items-center gap-1 text-sm font-medium text-[#7C3AED] transition-colors hover:text-[#2D1B69] group">
                            Ver historial
                            <svg class="w-3.5 h-3.5 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-gray-100">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </div>
                            <p class="font-medium text-gray-400">
                                {{ $soloMayoristas || $buscar !== '' ? 'No hay clientes que coincidan con tu búsqueda' : 'No hay clientes registrados' }}
                            </p>
                            <a href="{{ route('clientes.create') }}" class="text-sm text-[#7C3AED] hover:underline">
                                Crear primer cliente →
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Paginación --}}
<div class="mt-6">
    {{ $clientes->links() }}
</div>

@endsection