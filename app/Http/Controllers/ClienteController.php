<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Reparacion;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ClienteController extends Controller
{
    // ── Lista paginada de clientes del taller ─────────────────
    public function index(Request $request)
    {
        $tallerId = auth()->user()->taller_id;

        $buscar         = mb_substr(trim((string) $request->query('buscar', '')), 0, 100);
        $soloMayoristas = $request->boolean('mayoristas');

        $clientes = Cliente::where('taller_id', $tallerId)
            ->when($buscar,          fn ($q) => $q->where('nombre', 'like', "%{$buscar}%"))
            ->when($soloMayoristas,  fn ($q) => $q->where('es_mayorista', true))
            ->withCount('reparaciones')
            ->orderBy('nombre')
            ->paginate(20)
            ->withQueryString();

        return view('clientes.index', compact('clientes', 'soloMayoristas', 'buscar'));
    }

    // ── Formulario de nuevo cliente ───────────────────────────
    // Se carga el plan una sola vez con with() para evitar N+1
    public function create()
    {
        $permiteClientesMayoristas = auth()->user()
            ->taller()
            ->with('plan')
            ->firstOrFail()
            ->permiteClientesMayoristas();

        return view('clientes.create', compact('permiteClientesMayoristas'));
    }

    // ── Guardar nuevo cliente ─────────────────────────────────
    public function store(Request $request)
    {
        $taller                    = auth()->user()->taller()->with('plan')->firstOrFail();
        $permiteClientesMayoristas = $taller->permiteClientesMayoristas();

        $data = $request->validate([
            'nombre'       => ['required', 'string', 'max:255'],
            'email'        => ['nullable', 'email', 'max:255'],
            'telefono'     => ['nullable', 'string', 'max:20'],
            'direccion'    => ['nullable', 'string', 'max:500'],
            'es_mayorista' => ['nullable', 'boolean'],
        ]);

        // Bloquear mayorista si el plan no lo permite
        if ($request->boolean('es_mayorista') && ! $permiteClientesMayoristas) {
            throw ValidationException::withMessages([
                'es_mayorista' => 'Tu plan actual no permite registrar clientes mayoristas.',
            ]);
        }

        $data['es_mayorista'] = $request->boolean('es_mayorista') && $permiteClientesMayoristas;

        $cliente = Cliente::create(['taller_id' => $taller->id, ...$data]);

        return redirect()->route('clientes.show', $cliente)
            ->with('success', 'Cliente registrado correctamente.');
    }

    // ── Perfil del cliente + historial de órdenes ─────────────
    // Se usa eager loading con solo las columnas necesarias para reducir la carga
    public function show(Cliente $cliente)
    {
        abort_if((int) $cliente->taller_id !== (int) auth()->user()->taller_id, 403);

        $reparaciones = Reparacion::where('cliente_id', $cliente->id)
            ->with([
                'nivel:id,nivel,nombre',
                'tecnico:id,name',
            ])
            ->latest()
            ->get();

        return view('clientes.show', compact('cliente', 'reparaciones'));
    }
}