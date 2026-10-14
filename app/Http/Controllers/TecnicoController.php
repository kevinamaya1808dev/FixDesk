<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class TecnicoController extends Controller
{
    // ── Lista de técnicos del taller ──────────────────────────
    // withCount usa subconsultas en lugar de cargar todas las órdenes en memoria
    public function index()
    {
        $this->soloAdmin();
        $taller = auth()->user()->taller()->with('plan')->firstOrFail();

        $tecnicos = User::where('taller_id', $taller->id)
            ->where('rol', 'tecnico')
            ->withCount([
                'reparaciones as ordenes_activas' => fn ($q) => $q->whereNotIn('estado', ['Entregado', 'Cancelado']),
                'reparaciones as ordenes_total',
            ])
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'created_at']);

        return view('tecnicos.index', [
            'tecnicos'          => $tecnicos,
            'maxTecnicos'       => $taller->maxTecnicos(),
            'puedeCrearTecnicos'=> $tecnicos->count() < $taller->maxTecnicos(),
        ]);
    }

    // ── Formulario para crear técnico ─────────────────────────
    public function create()
    {
        $this->soloAdmin();
        $taller           = auth()->user()->taller()->with('plan')->firstOrFail();
        $tecnicosActuales = $taller->tecnicosCount();
        $maxTecnicos      = $taller->maxTecnicos();

        return view('tecnicos.create', [
            'tecnicosActuales'   => $tecnicosActuales,
            'maxTecnicos'        => $maxTecnicos,
            'puedeCrearTecnicos' => $tecnicosActuales < $maxTecnicos,
        ]);
    }

    // ── Guardar nuevo técnico ─────────────────────────────────
    public function store(Request $request)
    {
        $this->soloAdmin();
        $taller = auth()->user()->taller()->with('plan')->firstOrFail();

        // Verificar límite antes de validar para un error más rápido
        if (! $taller->puedeCrearTecnicos()) {
            throw ValidationException::withMessages([
                'name' => "Tu plan permite hasta {$taller->maxTecnicos()} técnicos. Actualiza tu suscripción para agregar más.",
            ]);
        }

        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', Rules\Password::defaults(), 'confirmed'],
        ]);

        User::create([
            'taller_id' => $taller->id,
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => Hash::make($data['password']),
            'rol'       => 'tecnico',
        ]);

        return redirect()->route('tecnicos.index')
            ->with('success', "Técnico {$data['name']} creado correctamente.");
    }

    // ── Eliminar técnico del taller ───────────────────────────
    public function destroy(User $tecnico)
    {
        $this->soloAdmin();
        abort_if((int) $tecnico->taller_id !== (int) auth()->user()->taller_id, 403);
        abort_if($tecnico->rol !== 'tecnico', 403, 'No puedes eliminar a un administrador.');

        $tecnico->delete();

        return redirect()->route('tecnicos.index')->with('success', 'Técnico eliminado del taller.');
    }

    // ── Guard: solo administradores ───────────────────────────
    private function soloAdmin(): void
    {
        abort_unless(auth()->user()?->esAdmin(), 403, 'Solo los administradores pueden acceder a esta sección.');
    }
}