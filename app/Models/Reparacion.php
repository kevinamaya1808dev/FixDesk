<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Reparacion extends Model
{
    protected $table = 'reparaciones';

    protected $fillable = [
        'taller_id',
        'cliente_id',
        'user_id',
        'nivel_id',
        'folio',
         'folio_grupo',       // ← nuevo
        'articulo_num',      // ← nuevo
        'token_seguimiento',
        'tipo_equipo',
        'marca',
        'modelo',
        'numero_serie',
        'problema_reportado',
        'diagnostico_tecnico',
        'comentario_retardo',
        'estado',
        'costo_estimado',
        'costo_final',
        'hora_ingreso',
        'hora_limite',
        'hora_fin',
    ];

    protected $casts = [
        'hora_ingreso' => 'datetime',
        'hora_limite' => 'datetime',
        'hora_fin' => 'datetime',
    ];

    // ─── Relaciones ──────────────────────────────────────────

    public function taller()
    {
        return $this->belongsTo(Taller::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function tecnico()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function nivel()
    {
        return $this->belongsTo(NivelReparacion::class, 'nivel_id');
    }

    public function mensajes()
    {
        return $this->hasMany(Mensaje::class)->orderBy('created_at');
    }

    public function escalamientos()
    {
        return $this->hasMany(Escalamiento::class)->orderBy('created_at');
    }
        /**
     * Todas las reparaciones del mismo lote (mismo folio_grupo).
     */
    public function hermanas()
    {
        return $this->hasMany(Reparacion::class, 'folio_grupo', 'folio_grupo')
            ->where('id', '!=', $this->id)
            ->orderBy('articulo_num');
    }

    /**
     * ¿Esta orden pertenece a un lote de varios artículos?
     */
    public function esLote(): bool
    {
        return Reparacion::where('folio_grupo', $this->folio_grupo)->count() > 1;
    }

    // ─── Scopes ──────────────────────────────────────────────

    /**
     * Filtra las reparaciones por el taller del usuario autenticado.
     */
    public function scopeDelTaller($query, int $tallerId)
    {
        return $query->where('taller_id', $tallerId);
    }

    /**
     * Solo órdenes activas (no entregadas ni canceladas).
     */
    public function scopeActivas($query)
    {
        return $query->whereNotIn('estado', ['Entregado', 'Cancelado']);
    }

    /**
     * Órdenes que todavía pueden pasar a estado Retardo por SLA vencido.
     */
    public function scopePendientesDeRetardo($query)
    {
        return $query
            ->whereNotIn('estado', ['Retardo', 'Reparado', 'Entregado', 'Cancelado'])
            ->where('hora_limite', '<', now());
    }

    // ─── Accesores / Helpers ─────────────────────────────────

    /**
     * Indica si la orden ya superó su hora límite.
     */
    public function estaRetrasada(): bool
    {
        if (in_array($this->estado, ['Reparado', 'Entregado', 'Cancelado'])) {
            return false;
        }

        return Carbon::now()->isAfter($this->hora_limite);
    }

    /**
     * Retorna el tiempo restante en formato legible.
     * Si ya pasó, retorna un número negativo de horas.
     */
    public function tiempoRestanteHoras(): float
    {
        return round(Carbon::now()->diffInMinutes($this->hora_limite, false) / 60, 1);
    }

    // ─── Generadores estáticos ───────────────────────────────

    /**
     * Genera un folio globalmente único: FF-{CODIGO_TALLER}-YYYY-XXXX
     */
        /**
     * Genera el folio base del grupo: FF-{CODIGO}-YYYY-XXXX
     */
    public static function generarFolioGrupo(int $tallerId): string
    {
        $taller = Taller::findOrFail($tallerId);
        $codigoTaller = $taller->codigo_publico;

        if (! $codigoTaller) {
            $codigoTaller = Taller::generarCodigoPublico($taller->nombre);
            $taller->forceFill(['codigo_publico' => $codigoTaller])->save();
        }

        $anio = now()->year;
        $prefijo = "FF-{$codigoTaller}-{$anio}-";

        $ultimo = self::where('taller_id', $tallerId)
            ->whereYear('created_at', $anio)
            ->selectRaw('MAX(CAST(SUBSTRING(folio_grupo, ?) AS UNSIGNED)) as max_num', [strlen($prefijo) + 1])
            ->value('max_num') ?? 0;

        $numero = str_pad($ultimo + 1, 4, '0', STR_PAD_LEFT);

        return "{$prefijo}{$numero}";
    }

    /**
     * Folio individual: folio_grupo + sufijo de artículo (A1, A2, B1…)
     * gi = índice de grupo (0-based), ai = índice de artículo dentro del grupo (0-based)
     */
    public static function folioArticulo(string $folioGrupo, int $gi, int $ai): string
    {
        $letra = chr(65 + $gi); // A, B, C…
        return "{$folioGrupo}-{$letra}" . ($ai + 1);
    }

    /**
     * Genera un token único para el portal del cliente.
     */
    public static function generarToken(): string
    {
        do {
            $token = Str::random(32);
        } while (self::where('token_seguimiento', $token)->exists());

        return $token;
    }
}
