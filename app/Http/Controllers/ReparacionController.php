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
    public function index(Request $request)
    {
        $tallerId = auth()->user()->taller_id;

        $filtrosEstado = [
            'activas' => 'Activas',
            'Recibido' => 'Recibido',
            'En Revisión' => 'En Revisión',
            'Esperando Pieza' => 'Esperando Pieza',
            'Retardo' => 'Retardo',
            'Reparado' => 'Reparado',
        ];

        $estadoSolicitado = $request->query('estado', 'activas');
        $estadoActual = array_key_exists($estadoSolicitado, $filtrosEstado)
            ? $estadoSolicitado
            : 'activas';
        $estadoInvalido = $request->filled('estado') && ! array_key_exists($estadoSolicitado, $filtrosEstado);

        $tecnicos = User::where('taller_id', $tallerId)
            ->where('rol', 'tecnico')
            ->orderBy('name')
            ->get(['id', 'name']);

        $tecnicoActual = null;
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

        if ($estadoInvalido || $tecnicoInvalido) {
            return redirect()->route('reparaciones.index', array_filter([
                'estado' => $estadoActual !== 'activas' ? $estadoActual : null,
                'tecnico_id' => $tecnicoActual,
                'retraso' => $soloRetrasadas ? 1 : null,
            ], fn ($value) => filled($value)));
        }

        $query = Reparacion::delTaller($tallerId)
            ->with(['cliente', 'tecnico', 'nivel']);

        if ($estadoActual === 'activas') {
            $query->activas();
        } else {
            $query->where('estado', $estadoActual);
        }

        if ($tecnicoActual) {
            $query->where('user_id', $tecnicoActual);
        }

        if ($soloRetrasadas) {
            $query
                ->where('hora_limite', '<', now())
                ->whereNotIn('estado', ['Reparado', 'Entregado', 'Cancelado']);
        }

        $reparaciones = $query
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('reparaciones.index', compact(
            'reparaciones',
            'estadoActual',
            'tecnicoActual',
            'soloRetrasadas',
            'filtrosEstado',
            'tecnicos'
        ));
    }

    public function create()
    {
        $tallerId = auth()->user()->taller_id;

        $niveles = NivelReparacion::orderBy('nivel')->get();
        $clientes = Cliente::where('taller_id', $tallerId)->orderBy('nombre')->get();
        $tecnicos = User::where('taller_id', $tallerId)->orderBy('name')->get();

        return view('reparaciones.create', compact('niveles', 'clientes', 'tecnicos'));
    }

        public function store(Request $request)
    {
        $tallerId = auth()->user()->taller_id;

        $data = $request->validate([
            // Cliente
            'cliente_id' => [
                'nullable',
                Rule::exists('clientes', 'id')->where(fn ($q) => $q->where('taller_id', $tallerId)),
            ],
            'cliente_nombre'   => ['nullable', 'required_without:cliente_id', 'string', 'max:255'],
            'cliente_email'    => ['nullable', 'email', 'max:255'],
            'cliente_telefono' => ['nullable', 'string', 'max:20'],

            // Grupos de dispositivos
            'grupos'                              => ['required', 'array', 'min:1'],
            'grupos.*.tipo'                       => ['required', 'string', 'max:100'],
            'grupos.*.articulos'                  => ['required', 'array', 'min:1'],
            'grupos.*.articulos.*.marca'          => ['required', 'string', 'max:100'],
            'grupos.*.articulos.*.modelo'         => ['required', 'string', 'max:100'],
            'grupos.*.articulos.*.numero_serie'   => ['nullable', 'string', 'max:100'],

            // Reparación compartida
            'nivel_id'          => ['required', 'exists:niveles_reparacion,id'],
            'user_id'           => [
                'nullable',
                Rule::exists('users', 'id')->where(fn ($q) => $q->where('taller_id', $tallerId)),
            ],
            'problema_reportado' => ['required', 'string'],
            'costo_estimado'     => ['nullable', 'numeric', 'min:0'],
        ]);

        // ── Resolver o crear cliente ──────────────────────────
        if (! empty($data['cliente_id'])) {
            $cliente = Cliente::where('taller_id', $tallerId)->findOrFail($data['cliente_id']);
        } else {
            $cliente = Cliente::create([
                'taller_id' => $tallerId,
                'nombre'    => $data['cliente_nombre'],
                'email'     => $data['cliente_email'] ?? null,
                'telefono'  => $data['cliente_telefono'] ?? null,
            ]);
        }

        // ── Datos compartidos ─────────────────────────────────
        $nivel      = NivelReparacion::findOrFail($data['nivel_id']);
        $horaIngreso = now();
        $horaLimite  = now()->addHours($nivel->horas_sla);
        $folioGrupo  = Reparacion::generarFolioGrupo($tallerId);

        $primeraReparacion = null;
        $totalArticulos    = 0;

        foreach ($data['grupos'] as $gi => $grupo) {
            foreach ($grupo['articulos'] as $ai => $articulo) {

                $totalArticulos++;
                $folioIndividual = Reparacion::folioArticulo($folioGrupo, $gi, $ai);

                $reparacion = Reparacion::create([
                    'taller_id'          => $tallerId,
                    'cliente_id'         => $cliente->id,
                    'user_id'            => $data['user_id'] ?? null,
                    'nivel_id'           => $nivel->id,
                    'folio'              => $folioIndividual,
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

        // ── Email solo una vez por lote ───────────────────────
        if ($cliente->email) {
            Mail::to($cliente->email)->send(new OrdenCreadaMail($primeraReparacion));
        }

        // ── Mensaje de éxito ──────────────────────────────────
        if ($totalArticulos === 1) {
            $msg = "Orden {$primeraReparacion->folio} creada correctamente.";
        } else {
            $msg = "Lote {$folioGrupo} creado con {$totalArticulos} artículos.";
        }

        return redirect()->route('reparaciones.show', $primeraReparacion)
            ->with('success', $msg);
    }

    public function show(Reparacion $reparacion)
    {
        $this->autorizarTaller($reparacion);

        $reparacion->load(['cliente', 'tecnico', 'nivel', 'mensajes.user', 'escalamientos.nivelAnterior', 'escalamientos.nivelNuevo', 'escalamientos.user']);

        $niveles = NivelReparacion::orderBy('nivel')->get();
        $tecnicos = User::where('taller_id', auth()->user()->taller_id)->orderBy('name')->get();

        return view('reparaciones.show', compact('reparacion', 'niveles', 'tecnicos'));
    }

    public function update(Request $request, Reparacion $reparacion)
    {
        $this->autorizarTaller($reparacion);

        $data = $request->validate([
            'estado' => ['sometimes', 'in:Recibido,En Revisión,Esperando Pieza,Reparado,Entregado,Cancelado,Retardo'],
            'diagnostico_tecnico' => ['sometimes', 'nullable', 'string'],
            'comentario_retardo' => ['sometimes', 'nullable', 'string'],
            'costo_final' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'user_id' => [
                'sometimes',
                'nullable',
                Rule::exists('users', 'id')->where(fn ($query) => $query->where('taller_id', auth()->user()->taller_id)),
            ],
        ]);

        $estadoAnterior = $reparacion->estado;
        $reparacion->update($data);

        // Si se marca como Reparado: registrar hora_fin y enviar email al cliente
        if (isset($data['estado']) && $data['estado'] === 'Reparado' && $estadoAnterior !== 'Reparado') {
            $reparacion->update(['hora_fin' => now()]);

            if ($reparacion->cliente->email) {
                Mail::to($reparacion->cliente->email)
                    ->send(new ReparacionListaMail($reparacion));
            }
        }
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

        return redirect()->route('reparaciones.show', $reparacion)
            ->with('success', 'Orden actualizada correctamente.');
    }

    public function escalar(Request $request, Reparacion $reparacion)
    {
        $this->autorizarTaller($reparacion);

        $data = $request->validate([
            // Rule::notIn compara el valor enviado contra el nivel_id actual (con cast a int)
            // 'different:N' en Laravel compara contra un *campo de request* llamado N, no contra el valor N
            'nivel_nuevo_id' => ['required', 'exists:niveles_reparacion,id', Rule::notIn([(int) $reparacion->nivel_id])],
            'motivo' => ['required', 'string', 'min:10'],
        ]);

        $nivelAnteriorId = $reparacion->nivel_id;
        $nivelNuevo = NivelReparacion::findOrFail($data['nivel_nuevo_id']);

        // Registrar el escalamiento
        Escalamiento::create([
            'reparacion_id' => $reparacion->id,
            'user_id' => auth()->id(),
            'nivel_anterior_id' => $nivelAnteriorId,
            'nivel_nuevo_id' => $nivelNuevo->id,
            'motivo' => $data['motivo'],
        ]);

        // Actualizar nivel y recalcular hora_limite
        // ->copy() evita mutar el atributo hora_ingreso del modelo en memoria
        $reparacion->update([
            'nivel_id' => $nivelNuevo->id,
            'hora_limite' => $reparacion->hora_ingreso->copy()->addHours($nivelNuevo->horas_sla),
        ]);

        return redirect()->route('reparaciones.show', $reparacion)
            ->with('success', "Nivel escalado a {$nivelNuevo->nombre}. Tiempo límite recalculado.");
    }

    // ─── Helper privado ───────────────────────────────────────

    private function autorizarTaller(Reparacion $reparacion): void
    {
        abort_if((int) $reparacion->taller_id !== (int) auth()->user()->taller_id, 403);
    }
}
