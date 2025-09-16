<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Pqr;
use App\Models\Radicado;

class PqrForm extends Component
{
    // Form
    public string $tipo = 'peticion';       // peticion | queja | reclamo
    public string $descripcion = '';

    // UI / feedback
    public string $flash = '';
    public ?Radicado $ultimoRadicado = null;

    // Panel derecho (resumen global + recientes)
    public array $stats = ['total' => 0, 'radicados' => 0, 'cerrados' => 0];
    public $recientes = [];

    protected function rules(): array
    {
        return [
            'tipo'        => 'required|in:peticion,queja,reclamo',
            'descripcion' => 'required|string|min:10',
        ];
    }

    public function mount(): void
    {
        $this->cargarPanel();
    }

    public function radicar(): void
    {
        $this->validate();

        DB::transaction(function () {
            // 1) Crear PQR
            $pqr = Pqr::create([
                'user_id'     => Auth::id(),          // puede ser null
                'tipo'        => $this->tipo,
                'descripcion' => $this->descripcion,
                'estado'      => 'radicado',          // estados sugeridos: radicado | en_proceso | cerrado
            ]);

            // 2) Generar número de radicado: PQR-YYYY-ABC123
            $numero = sprintf('PQR-%s-%s', now()->format('Y'), Str::upper(Str::random(6)));

            // 3) Crear radicado polimórfico
            $this->ultimoRadicado = $pqr->radicado()->create([
                'numero'  => $numero,
                'modulo'  => 'pqr',
                'user_id' => Auth::id(),             // puede ser null
            ]);
        });

        // 4) Refrescar panel (totales globales + recientes)
        $this->cargarPanel();

        // 5) Feedback con número
        $num = optional($this->ultimoRadicado)->numero ?? '';
        $this->flash = $num
            ? "✅ Tu PQR fue radicada con el número {$num}."
            : '✅ Tu PQR fue radicada.';

        // 6) Reset de formulario (deja tipo por defecto)
        $this->reset(['descripcion']);
        $this->tipo = 'peticion';
    }

    private function cargarPanel(): void
    {
        // Totales GLOBALes
        $this->stats['total']     = Pqr::count();
        $this->stats['radicados'] = Pqr::where('estado', 'radicado')->count();
        $this->stats['cerrados']  = Pqr::where('estado', 'cerrado')->count();

        // Últimos 8 GLOBALes (con radicado)
        $this->recientes = Pqr::with('radicado')
            ->latest()
            ->take(8)
            ->get();
    }

    public function render()
    {
        // Usa un Blade propio o incrústalo donde quieras
        return view('livewire.pqr-form')->layout('layouts.app');
    }
}
