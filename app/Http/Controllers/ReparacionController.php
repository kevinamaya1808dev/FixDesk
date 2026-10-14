<?php

namespace App\Http\Controllers;

use App\Mail\OrdenCreadaMail;
use App\Mail\ReparacionListaMail;
use App\Models\Cliente;
use App\Models\Escalamiento;
use App\Models\NivelReparacion;
use App\Models\Reparacion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class ReparacionController extends Controller
{
    // ── Lista paginada de órdenes con filtros ─────────────────
    public function index(Request $request)
    {
        $tallerId = auth()->user()->taller_id;

        $filtrosEstado = [
            'activas'         => 'Activas',
            'Recibido'        => 'Recibido',
            'En Revisión'     => 'En Revisión',
            'Esperando Pieza' => 'Esperando Pieza',
            'Retardo'         => 'Retardo',
            'Reparado'        => 'Reparado',
        ];

        $estadoSolicitado = $request->query('estado', 'activas');
        $estadoActual     = array_key_exists($estadoSolicitado, $filtrosEstado)
            ? $estadoSolicitado
            : 'activas';
        $estadoInvalido   = $request->filled('estado') && ! array_key_exists($estadoSolicitado, $filtrosEstado);

        // Solo columnas necesarias para los selectores de filtro
        $tecnicos = User::where('taller_id', $tallerId)
            ->where('rol', 'tecnico')
            ->orderBy('name')
            ->get(['id', 'name']);

        $tecnicoActual   = null;
        $tecnicoInvalido = false;

        if ($request->filled('tecnico_id')) {
            $tecnicoId = (int) $request->query('tecnico_id');
            if ($tecnicos->contains('id', $tecnicoId)) {
                $tecnicoActual = $tecnicoId;
            } else {
                $tecnicoInvalido = true;
            }
        }

        $soloRetrasadas = $request->boolean('retraso');

        // Redirigir limpiando parámetros inválidos
        if ($estadoInvalido || $tecnicoInvalido) {
            return redirect()->route('reparaciones.index', array_filter([
                'estado'     => $estadoActual !== 'activas' ? $estadoActual : null,
                'tecnico_id' => $tecnicoActual,
                'retraso'    => $soloRetrasadas ? 1 : null,
            ], fn ($v) => filled($v)));
        }

        $query = Reparacion::delTaller($tallerId)
            ->with(['cliente:id,nombre', 'tecnico:id,name', 'nivel:id,nivel,nombre']);

        if ($estadoActual === 'activas') {
            $query->activas();
        } else {
            $query->where('estado', $estadoActual);
        }

        if ($tecnicoActual) {
            $query->where('user_id', $tecnicoActual);
        }

        if ($soloRetrasadas) {
            $query->where('hora_limite', '<', now())
                  ->whereNotIn('estado', ['Reparado', 'Entregado', 'Cancelado']);
        }

        $reparaciones = $query->latest()->paginate(20)->withQueryString();

        return view('reparaciones.index', compact(
            'reparaciones', 'estadoActual', 'tecnicoActual',
            'soloRetrasadas', 'filtrosEstado', 'tecnicos'
        ));
    }

    // ── Formulario de nueva orden ─────────────────────────────
    public function create()
    {
        $tallerId = auth()->user()->taller_id;

        return view('reparaciones.create', [
            'niveles'   => NivelReparacion::orderBy('nivel')->get(),
            'clientes'  => Cliente::where('taller_id', $tallerId)->orderBy('nombre')->get(['id', 'nombre']),
            'tecnicos'  => User::where('taller_id', $tallerId)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    // ── Guardar lote de órdenes (uno o varios artículos) ──────
    public function store(Request $request)
    {
        $tallerId = auth()->user()->taller_id;

        $data = $request->validate([
            // Cliente
            'cliente_id'       => ['nullable', Rule::exists('clientes', 'id')->where(fn ($q) => $q->where('taller_id', $tallerId))],
            'cliente_nombre'   => ['nullable', 'required_without:cliente_id', 'string', 'max:255'],
            'cliente_email'    => ['nullable', 'email', 'max:255'],
            'cliente_telefono' => ['nullable', 'string', 'max:20'],

            // Grupos y artículos individuales
            'grupos'                              => ['required', 'array', 'min:1'],
            'grupos.*.tipo'                       => ['required', 'string', 'max:100'],
            'grupos.*.articulos'                  => ['required', 'array', 'min:1'],
            'grupos.*.articulos.*.marca'          => ['required', 'string', 'max:100'],
            'grupos.*.articulos.*.modelo'         => ['required', 'string', 'max:100'],
            'grupos.*.articulos.*.numero_serie'   => ['nullable', 'string', 'max:100'],
            'grupos.*.articulos.*.nivel_id'       => ['required', 'exists:niveles_reparacion,id'],

            // Datos comunes del lote
            'user_id'            => ['nullable', Rule::exists('users', 'id')->where(fn ($q) => $q->where('taller_id', $tallerId))],
            'problema_reportado' => ['required', 'string'],
            'costo_estimado'     => ['nullable', 'numeric', 'min:0'],
        ]);

        // Resolver o crear cliente
        $cliente = ! empty($data['cliente_id'])
            ? Cliente::where('taller_id', $tallerId)->findOrFail($data['cliente_id'])
            : Cliente::create([
                'taller_id' => $tallerId,
                'nombre'    => $data['cliente_nombre'],
                'email'     => $data['cliente_email']    ?? null,
                'telefono'  => $data['cliente_telefono'] ?? null,
            ]);

        $folioGrupo        = Reparacion::generarFolioGrupo($tallerId);
        $horaIngreso       = now();
        $primeraReparacion = null;
        $totalArticulos    = 0;

        foreach ($data['grupos'] as $gi => $grupo) {
            foreach ($grupo['articulos'] as $ai => $articulo) {

                // Nivel y SLA propios de cada artículo
                $nivel      = NivelReparacion::findOrFail($articulo['nivel_id']);
                $horaLimite = $horaIngreso->copy()->addHours($nivel->horas_sla);
                $totalArticulos++;

                $reparacion = Reparacion::create([
                    'taller_id'          => $tallerId,
                    'cliente_id'         => $cliente->id,
                    'user_id'            => $data['user_id'] ?? null,
                    'nivel_id'           => $nivel->id,
                    'folio'              => Reparacion::folioArticulo($folioGrupo, $gi, $ai),
                    'folio_grupo'        => $folioGrupo,
                    'articulo_num'       => $totalArticulos,
                    'token_seguimiento'  => Reparacion::generarToken(),
                    'tipo_equipo'        => $grupo['tipo'],
                    'marca'              => $articulo['marca'],
                    'modelo'             => $articulo['modelo'],
                    'numero_serie'       => $articulo['numero_serie'] ?? null,
                    'problema_reportado' => $data['problema_reportado'],
                    'costo_estimado'     => $data['costo_estimado'] ?? null,
                    'estado'             => 'Recibido',
                    'hora_ingreso'       => $horaIngreso,
                    'hora_limite'        => $horaLimite,
                ]);

                $primeraReparacion ??= $reparacion;
            }
        }

        // Email de confirmación una sola vez por lote
        if ($cliente->email) {
            Mail::to($cliente->email)->send(new OrdenCreadaMail($primeraReparacion));
        }

        $msg = $totalArticulos === 1
            ? "Orden {$primeraReparacion->folio} creada correctamente."
            : "Lote {$folioGrupo} creado con {$totalArticulos} artículos.";

        return redirect()->route('reparaciones.show', $primeraReparacion)->with('success', $msg);
    }

    // ── Detalle de una orden ──────────────────────────────────
    public function show(Reparacion $reparacion)
    {
        $this->autorizarTaller($reparacion);

        $reparacion->load([
            'cliente', 'tecnico', 'nivel',
            'mensajes.user:id,name',
            'escalamientos.nivelAnterior:id,nombre',
            'escalamientos.nivelNuevo:id,nombre',
            'escalamientos.user:id,name',
        ]);

        return view('reparaciones.show', [
            'reparacion' => $reparacion,
            'niveles'    => NivelReparacion::orderBy('nivel')->get(),
            'tecnicos'   => User::where('taller_id', auth()->user()->taller_id)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    // ── Actualizar estado, técnico, diagnóstico o costo ───────
    public function update(Request $request, Reparacion $reparacion)
    {
        $this->autorizarTaller($reparacion);

        $data = $request->validate([
            'estado'              => ['sometimes', 'in:Recibido,En Revisión,Esperando Pieza,Reparado,Entregado,Cancelado,Retardo'],
            'diagnostico_tecnico' => ['sometimes', 'nullable', 'string'],
            'comentario_retardo'  => ['sometimes', 'nullable', 'string'],
            'costo_final'         => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'user_id'             => ['sometimes', 'nullable', Rule::exists('users', 'id')->where(fn ($q) => $q->where('taller_id', auth()->user()->taller_id))],
        ]);

        $estadoAnterior = $reparacion->estado;
        $reparacion->update($data);

        // Al marcar como Reparado: registrar hora de fin y notificar al cliente
        if (isset($data['estado']) && $data['estado'] === 'Reparado' && $estadoAnterior !== 'Reparado') {
            $reparacion->update(['hora_fin' => now()]);

            if ($reparacion->cliente->email) {
                Mail::to($reparacion->cliente->email)->send(new ReparacionListaMail($reparacion));
            }
        }

        // Redirigir según el origen de la petición
        $origen = $request->input('_origen');

        if ($origen === 'panel') {
            return redirect()->route('panel.inicio')
                ->with('success', "Orden {$reparacion->folio} actualizada a \"{$reparacion->estado}\".");
        }

        if ($origen === 'index') {
            return redirect()->route('reparaciones.index')
                ->with('success', "Orden {$reparacion->folio} actualizada a \"{$reparacion->estado}\".");
        }

        return redirect()->route('reparaciones.show', $reparacion)
            ->with('success', 'Orden actualizada correctamente.');
    }

    // ── Escalar nivel de reparación ───────────────────────────
    public function escalar(Request $request, Reparacion $reparacion)
    {
        $this->autorizarTaller($reparacion);

        $data = $request->validate([
            'nivel_nuevo_id' => ['required', 'exists:niveles_reparacion,id', Rule::notIn([(int) $reparacion->nivel_id])],
            'motivo'         => ['required', 'string', 'min:10'],
        ]);

        $nivelNuevo = NivelReparacion::findOrFail($data['nivel_nuevo_id']);

        // Registrar historial de escalamiento
        Escalamiento::create([
            'reparacion_id'    => $reparacion->id,
            'user_id'          => auth()->id(),
            'nivel_anterior_id'=> $reparacion->nivel_id,
            'nivel_nuevo_id'   => $nivelNuevo->id,
            'motivo'           => $data['motivo'],
        ]);

        // Actualizar nivel y recalcular hora límite desde hora de ingreso
        $reparacion->update([
            'nivel_id'    => $nivelNuevo->id,
            'hora_limite' => $reparacion->hora_ingreso->copy()->addHours($nivelNuevo->horas_sla),
        ]);

        return redirect()->route('reparaciones.show', $reparacion)
            ->with('success', "Nivel escalado a {$nivelNuevo->nombre}. Tiempo límite recalculado.");
    }

    // ── Autorización de taller ────────────────────────────────
    private function autorizarTaller(Reparacion $reparacion): void
    {
        abort_if((int) $reparacion->taller_id !== (int) auth()->user()->taller_id, 403);
    }
}