<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Contrato;
use App\Models\Radicado;

#[Layout('layouts.app')]
#[Title('Contrátanos')]
class ContratanosPage extends Component
{
    // Campos del formulario
    public string $nombre = '';
    public string $email = '';
    public string $celular = '';
    public string $empresa = '';
    public string $nit = '';
    public string $servicio = '';
    public string $especificar = '';
    public string $mensaje = '';
    public string $estado = 'pendiente';

    // UI / feedback
    public string $flash = '';
    public ?Radicado $ultimoRadicado = null;

    // Panel derecho (resumen global + recientes)
    public array $stats = ['total' => 0, 'pendientes' => 0, 'cerrados' => 0];
    public $solicitudes = [];

    // Catálogo
    public array $servicios = [
        'Desarrollo Web',
        'Soporte y Mantenimiento',
        'Integraciones (APIs)',
        'Automatizaciones / RPA',
        'Consultoría',
    ];

    protected function rules(): array
    {
        return [
            'nombre'       => ['required','string','min:3','max:120'],
            'email'        => ['required','email','max:150'],
            'celular'      => ['nullable','string','max:20'],
            'empresa'      => ['nullable','string','max:150'],
            'nit'          => ['nullable','string','max:30'],
            'servicio'     => ['required','string','in:Desarrollo Web,Soporte y Mantenimiento,Integraciones (APIs),Automatizaciones / RPA,Consultoría'],
            'especificar'  => ['required','string','max:150'], // <-- ahora obligatorio
            'mensaje'      => ['nullable','string','max:2000'],
            'estado'       => ['required','string','in:pendiente,en_proceso,cerrado'],
        ];
    }
    public function mount(): void
    {
        // Cargar panel GLOBAL (sin filtrar por sesión ni email)
        $this->cargarPanel();
    }

    public function updatedServicio(): void
    {
        if ($this->servicio !== 'Otro') {
            $this->especificar = '';
        }
    }

    public function save(): void
    {
        $this->validate();

        $servicioFinal = $this->servicio === 'Otro'
            ? trim("Otro: {$this->especificar}")
            : $this->servicio;

        DB::transaction(function () use ($servicioFinal) {
            $contrato = Contrato::create([
                'nombre'       => $this->nombre,
                'email'        => $this->email,
                'celular'      => $this->celular,
                'empresa'      => $this->empresa,
                'nit'          => $this->nit,
                'servicio'     => $this->servicio,
                'especificar'  => $this->especificar, // siempre se guarda
                'mensaje'      => $this->mensaje,
                'estado'       => $this->estado,
            ]);
            // Generar radicado tipo "CON-YYYY-ABC123"
            $numero = sprintf('CON-%s-%s', now()->format('Y'), Str::upper(Str::random(6)));

            $this->ultimoRadicado = $contrato->radicado()->create([
                'numero'  => $numero,
                'modulo'  => 'contrato',
                'user_id' => auth()->id(), // puede ser null
            ]);
        });

        // Refrescar panel GLOBAL (totales + últimos 8)
        $this->cargarPanel();

        // Limpiar campos (dejamos estado en pendiente)
        $this->reset(['celular','empresa','nit','servicio','especificar','mensaje']);
        $this->estado = 'pendiente';

        $this->flash = '¡Tu solicitud fue radicada correctamente!';
    }

    private function cargarPanel(): void
    {
        // === Totales GLOBALes (toda la tabla) ===
        $this->stats['total']      = Contrato::count();
        $this->stats['pendientes'] = Contrato::where('estado', 'pendiente')->count();
        $this->stats['cerrados']   = Contrato::where('estado', 'cerrado')->count();

        // === Recientes: últimos 8 GLOBALes ===
        $this->solicitudes = Contrato::latest()->take(8)->get();
    }

    public function render()
    {
        return view('livewire.contratanos-page');
    }
}
