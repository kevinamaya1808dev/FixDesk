{{-- resources/views/components/selector-fecha-limite.blade.php --}}
<div class="flex-1 min-w-[200px]"
    x-data="{
        modalFecha: false,
        fechaTemp: '',
        horaTemp: '08:00',
        mesActual: new Date().getMonth(),
        anioActual: new Date().getFullYear(),
        diaSeleccionado: null,

        indiceHora: 8,
        horasDisponibles: [
            { val: '00:00', lbl: '12:00 AM' },
            { val: '01:00', lbl: '1:00 AM' },
            { val: '02:00', lbl: '2:00 AM' },
            { val: '03:00', lbl: '3:00 AM' },
            { val: '04:00', lbl: '4:00 AM' },
            { val: '05:00', lbl: '5:00 AM' },
            { val: '06:00', lbl: '6:00 AM' },
            { val: '07:00', lbl: '7:00 AM' },
            { val: '08:00', lbl: '8:00 AM' },
            { val: '09:00', lbl: '9:00 AM' },
            { val: '10:00', lbl: '10:00 AM' },
            { val: '11:00', lbl: '11:00 AM' },
            { val: '12:00', lbl: '12:00 PM' },
            { val: '13:00', lbl: '1:00 PM' },
            { val: '14:00', lbl: '2:00 PM' },
            { val: '15:00', lbl: '3:00 PM' },
            { val: '16:00', lbl: '4:00 PM' },
            { val: '17:00', lbl: '5:00 PM' },
            { val: '18:00', lbl: '6:00 PM' },
            { val: '19:00', lbl: '7:00 PM' },
            { val: '20:00', lbl: '8:00 PM' },
            { val: '21:00', lbl: '9:00 PM' },
            { val: '22:00', lbl: '10:00 PM' },
            { val: '23:00', lbl: '11:00 PM' }
        ],

        girarTambor(e) {
            e.preventDefault();
            const total = this.horasDisponibles.length;
            if (e.deltaY > 0) {
                this.indiceHora = (this.indiceHora + 1) % total;
            } else {
                this.indiceHora = (this.indiceHora - 1 + total) % total;
            }
            this.horaTemp = this.horasDisponibles[this.indiceHora].val;
        },

        seleccionarHoraDirecta(idx) {
            this.indiceHora = idx;
            this.horaTemp = this.horasDisponibles[idx].val;
        },

        get nombreMes() {
            return new Date(this.anioActual, this.mesActual, 1)
                .toLocaleString('es-MX', { month: 'long', year: 'numeric' });
        },

        get diasDelMes() {
            const primerDia = new Date(this.anioActual, this.mesActual, 1).getDay();
            const offset = (primerDia === 0) ? 6 : primerDia - 1;
            const totalDias = new Date(this.anioActual, this.mesActual + 1, 0).getDate();
            const celdas = [];
            for (let i = 0; i < offset; i++) celdas.push(null);
            for (let d = 1; d <= totalDias; d++) celdas.push(d);
            return celdas;
        },

        esPasado(dia) {
            if (!dia) return false;
            const hoy = new Date(); hoy.setHours(0,0,0,0);
            return new Date(this.anioActual, this.mesActual, dia) < hoy;
        },

        esHoy(dia) {
            if (!dia) return false;
            const hoy = new Date();
            return dia === hoy.getDate() && this.mesActual === hoy.getMonth() && this.anioActual === hoy.getFullYear();
        },

        mesAnterior() {
            if (this.mesActual === 0) { this.mesActual = 11; this.anioActual--; }
            else this.mesActual--;
        },

        mesSiguiente() {
            if (this.mesActual === 11) { this.mesActual = 0; this.anioActual++; }
            else this.mesActual++;
        },

        seleccionarDia(dia) {
            if (!dia || this.esPasado(dia)) return;
            this.diaSeleccionado = dia;
            const mes = String(this.mesActual + 1).padStart(2, '0');
            const d  = String(dia).padStart(2, '0');
            this.fechaTemp = this.anioActual + '-' + mes + '-' + d;
        },

        get resumen() {
            if (!this.fechaTemp) return '';
            const [y, m, d] = this.fechaTemp.split('-');
            const base = new Date(y, m-1, d).toLocaleDateString('es-MX', { weekday:'long', day:'numeric', month:'long', year:'numeric' });
            const horaLbl = this.horasDisponibles[this.indiceHora]?.lbl || '';
            return base + ' — ' + horaLbl;
        },

        confirmar() {
            if (!this.fechaTemp) return;
            const hora = this.horasDisponibles[this.indiceHora].val || '09:00';
            articulo.hora_limite = this.fechaTemp + 'T' + hora;
            this.modalFecha = false;
        },

        quitarFecha() {
            articulo.hora_limite = '';
            this.fechaTemp = '';
            this.horaTemp = '08:00';
            this.indiceHora = 8;
            this.diaSeleccionado = null;
            this.modalFecha = false;
        },

        abrir() {
            if (articulo.hora_limite) {
                const [fecha, hora] = articulo.hora_limite.split('T');
                this.fechaTemp = fecha || '';
                this.horaTemp  = hora  || '08:00';
                if (hora) {
                    const idx = this.horasDisponibles.findIndex(h => h.val === hora);
                    if (idx !== -1) this.indiceHora = idx;
                }
                if (fecha) {
                    const [y, m, d] = fecha.split('-');
                    this.anioActual = +y;
                    this.mesActual  = +m - 1;
                    this.diaSeleccionado = +d;
                }
            } else {
                this.fechaTemp = '';
                this.horaTemp  = '08:00';
                this.indiceHora = 8;
                this.diaSeleccionado = null;
                this.mesActual  = new Date().getMonth();
                this.anioActual = new Date().getFullYear();
            }
            this.modalFecha = true;
        }
    }">

    <label class="block text-xs font-medium text-gray-500 mb-1">
        Fecha límite de entrega
        <span class="text-gray-400 font-normal">(opcional)</span>
    </label>

    {{-- Botón disparador --}}
    <button
        type="button"
        @click="abrir()"
        class="w-full flex items-center gap-2 rounded-xl border border-gray-300 px-3 py-2 text-sm text-left transition-all hover:border-[#7C3AED] hover:bg-[#7C3AED]/5 focus:outline-none focus:ring-2 focus:ring-[#7C3AED]/20">
        <svg class="h-4 w-4 flex-shrink-0 text-[#7C3AED]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <span
            :class="articulo.hora_limite ? 'text-[#2D1B69] font-medium' : 'text-gray-400'"
            x-text="articulo.hora_limite
                ? new Date(articulo.hora_limite).toLocaleString('es-MX', { day:'2-digit', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit' })
                : 'Sin fecha límite'">
        </span>
        <template x-if="articulo.hora_limite">
            <svg class="ml-auto h-3.5 w-3.5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </template>
    </button>

    {{-- Input oculto --}}
    <input type="hidden"
        :name="`grupos[${gi}][articulos][${ai}][hora_limite]`"
        x-model="articulo.hora_limite" />

    <p class="mt-1 text-[10px] text-gray-400">
        Si no se indica, la orden quedará sin fecha límite.
    </p>

    {{-- ── MODAL ─────────────────────────────────────── --}}
    <div
        x-show="modalFecha"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        role="dialog" aria-modal="true">

        {{-- Fondo --}}
        <div x-show="modalFecha" x-transition.opacity @click="modalFecha = false"
            class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

        {{-- Contenedor del modal --}}
        <div
            x-show="modalFecha"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative z-10 w-full max-w-2xl overflow-hidden rounded-2xl bg-white shadow-2xl">

            {{-- Header --}}
            <div class="flex items-center justify-between bg-gradient-to-r from-[#2D1B69] to-[#1E1B2E] px-6 py-4">
                <div>
                    <h3 class="text-sm font-bold text-white">Fecha límite de entrega</h3>
                    <p class="mt-0.5 text-xs text-purple-300">Selecciona el día y gira la rueda (24 hrs) para la hora</p>
                </div>
                <button type="button" @click="modalFecha = false"
                    class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/10 text-white transition hover:bg-white/20">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Cuerpo: dos columnas --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 divide-y sm:divide-y-0 sm:divide-x divide-gray-100">

                {{-- COL IZQUIERDA: Selector rotativo 360° de 24 horas (Tambor de revólver) --}}
                <div class="p-5 flex flex-col justify-center items-center">
                    <div class="w-full flex items-center justify-between mb-3">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                            Hora límite (24 hrs)
                        </p>
                        <span class="text-xs font-bold text-[#7C3AED] bg-[#7C3AED]/10 px-2.5 py-1 rounded-lg" x-text="horasDisponibles[indiceHora]?.lbl"></span>
                    </div>

                    {{-- Contenedor del Tambor --}}
                    <div
                        @wheel.prevent="girarTambor($event)"
                        class="relative h-44 w-full overflow-hidden flex flex-col items-center bg-gray-50 rounded-2xl border border-gray-200 shadow-inner select-none cursor-ns-resize group">
                        
                        {{-- Ventana de selección central --}}
                        <div class="absolute inset-x-3 top-1/2 -translate-y-1/2 h-10 bg-[#7C3AED]/5 rounded-xl shadow-sm border-2 border-[#7C3AED]/40 pointer-events-none z-10"></div>

                        {{-- Lista deslizable --}}
                        <div class="w-full flex flex-col items-center transition-transform duration-200 ease-out"
                             :style="`transform: translateY(${68 - (indiceHora * 40)}px)`">
                            <template x-for="(h, idx) in horasDisponibles" :key="h.val">
                                <button
                                    type="button"
                                    @click="seleccionarHoraDirecta(idx)"
                                    class="h-10 w-full flex items-center justify-center text-sm transition-all cursor-pointer font-medium focus:outline-none"
                                    :class="indiceHora === idx 
                                        ? 'text-[#7C3AED] font-bold scale-105 z-20' 
                                        : (Math.abs(indiceHora - idx) === 1 ? 'text-gray-400 hover:text-gray-600 scale-95' : 'text-gray-300 hover:text-gray-400 scale-90 blur-[0.4px]')">
                                    <span x-text="h.lbl"></span>
                                </button>
                            </template>
                        </div>

                        {{-- Indicadores visuales --}}
                        <div class="absolute top-2 pointer-events-none text-gray-300 z-20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                        </div>
                        <div class="absolute bottom-2 pointer-events-none text-gray-300 z-20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>

                    <p class="mt-3 text-[11px] text-gray-400 text-center">
                        💡 Rueda el ratón 360° para recorrer las 24 horas o haz clic directo.
                    </p>
                </div>

                {{-- COL DERECHA: calendario --}}
                <div class="p-5 space-y-4">
                    <div class="flex items-center justify-between">
                        <button type="button" @click="mesAnterior()"
                            class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-500 transition hover:bg-gray-100">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                        <span class="text-sm font-bold text-[#2D1B69] capitalize" x-text="nombreMes"></span>
                        <button type="button" @click="mesSiguiente()"
                            class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-500 transition hover:bg-gray-100">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>

                    <div class="grid grid-cols-7 text-center">
                        @foreach(['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'] as $dia)
                        <div class="py-1 text-[10px] font-semibold uppercase tracking-wide text-gray-400">
                            {{ $dia }}
                        </div>
                        @endforeach
                    </div>

                    <div class="grid grid-cols-7 gap-y-1 text-center">
                        <template x-for="(dia, idx) in diasDelMes" :key="idx">
                            <div>
                                <template x-if="!dia">
                                    <div class="h-9 w-9 mx-auto"></div>
                                </template>
                                <template x-if="dia && esPasado(dia)">
                                    <div class="h-9 w-9 mx-auto flex items-center justify-center rounded-xl text-sm text-gray-300 cursor-not-allowed select-none"
                                        x-text="dia"></div>
                                </template>
                                <template x-if="dia && !esPasado(dia) && dia === diaSeleccionado && mesActual === (fechaTemp ? +fechaTemp.split('-')[1] - 1 : -1) && anioActual === (fechaTemp ? +fechaTemp.split('-')[0] : -1)">
                                    <button type="button" @click="seleccionarDia(dia)"
                                        class="h-9 w-9 mx-auto flex items-center justify-center rounded-xl text-sm font-bold text-white bg-[#7C3AED] ring-2 ring-[#7C3AED] ring-offset-1 shadow-md transition-all"
                                        x-text="dia"></button>
                                </template>
                                <template x-if="dia && !esPasado(dia) && esHoy(dia) && !(dia === diaSeleccionado && mesActual === (fechaTemp ? +fechaTemp.split('-')[1] - 1 : -1) && anioActual === (fechaTemp ? +fechaTemp.split('-')[0] : -1))">
                                    <button type="button" @click="seleccionarDia(dia)"
                                        class="h-9 w-9 mx-auto flex items-center justify-center rounded-xl text-sm font-semibold text-[#7C3AED] bg-[#7C3AED]/10 ring-1 ring-[#7C3AED]/30 transition-all hover:bg-[#7C3AED]/20"
                                        x-text="dia"></button>
                                </template>
                                <template x-if="dia && !esPasado(dia) && !esHoy(dia) && !(dia === diaSeleccionado && mesActual === (fechaTemp ? +fechaTemp.split('-')[1] - 1 : -1) && anioActual === (fechaTemp ? +fechaTemp.split('-')[0] : -1))">
                                    <button type="button" @click="seleccionarDia(dia)"
                                        class="h-9 w-9 mx-auto flex items-center justify-center rounded-xl text-sm text-gray-700 transition-all hover:bg-[#7C3AED]/10 hover:text-[#7C3AED]"
                                        x-text="dia"></button>
                                </template>
                            </div>
                        </template>
                    </div>

                    <div class="rounded-xl border border-[#7C3AED]/15 bg-[#7C3AED]/5 px-4 py-3 mt-2">
                        <p class="mb-1 text-[10px] font-semibold uppercase tracking-wide text-gray-500">Resumen</p>
                        <template x-if="resumen">
                            <p class="text-sm font-semibold text-[#2D1B69] capitalize" x-text="resumen"></p>
                        </template>
                        <template x-if="!resumen">
                            <p class="text-sm italic text-gray-400">Ningún día seleccionado</p>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-between gap-3 border-t border-gray-100 bg-gray-50 px-6 py-4">
                <button type="button" @click="quitarFecha()"
                    class="text-xs font-medium text-gray-400 transition hover:text-red-500">
                    Quitar fecha
                </button>
                <div class="flex items-center gap-2">
                    <button type="button" @click="modalFecha = false"
                        class="rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button
                        type="button"
                        @click="confirmar()"
                        :disabled="!fechaTemp"
                        :class="!fechaTemp
                            ? 'bg-gray-200 text-gray-400 cursor-not-allowed'
                            : 'bg-[#7C3AED] text-white hover:bg-[#6D28D9] shadow-sm'"
                        class="flex items-center gap-2 rounded-xl px-5 py-2 text-sm font-medium transition">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Confirmar
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>