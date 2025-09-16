<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Contrato;
use App\Models\Radicado;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.app')]        // resources/views/layouts/app.blade.php
#[Title('Contrátanos')]
class ContratanosPage extends Component
{
    // Campos del formulario
    public string $nombre   = '';
    public string $email    = '';
    public string $celular  = '';
    public string $empresa  = '';
    public string $nit      = '';
    public string $servicio = '';
    public string $mensaje  = '';
    public string $estado   = 'pendiente';

    // UI/Feedback
    public string $flash = '';
    public ?Radicado $ultimoRadicado = null; // para mostrar badge y botón copiar

    // Catálogo para <select>
    public array $servicios = [
        'Desarrollo Web',
        'Soporte y Mantenimiento',
        'Integraciones (APIs)',
        'Automatizaciones / RPA',
        'Consultoría',
        'Otro',
    ];

    protected function rules(): array
    {
        return [
            'nombre'   => ['required','string','min:3','max:150'],
            'email'    => ['required','email','max:150'],
            'celular'  => ['nullable','string','max:50'],
            'empresa'  => ['nullable','string','max:150'],
            'nit'      => ['nullable','string','max:50'],
            'servicio' => ['required','string','max:150'],
            'mensaje'  => ['nullable','string','max:5000'],
            'estado'   => ['in:pendiente,contactado,cerrado'],
        ];
    }

    protected function messages(): array
    {
        return [
            'nombre.required'   => 'El nombre es obligatorio.',
            'nombre.min'        => 'El nombre debe tener al menos :min caracteres.',
            'email.required'    => 'El correo electrónico es obligatorio.',
            'email.email'       => 'El correo electrónico no tiene un formato válido.',
            'servicio.required' => 'Selecciona o escribe el servicio que necesitas.',
            'estado.in'         => 'El estado seleccionado no es válido.',
        ];
    }

    public function save(): void
    {
        $this->validate();

        $numeroRadicado = null;

        DB::transaction(function () use (&$numeroRadicado) {
            // 1) Crear contrato
            $contrato = Contrato::create([
                'nombre'   => $this->nombre,
                'email'    => $this->email,
                'celular'  => $this->celular,
                'empresa'  => $this->empresa,
                'nit'      => $this->nit,
                'servicio' => $this->servicio,
                'mensaje'  => $this->mensaje,
                'estado'   => $this->estado,
            ]);

            // 2) Generar número de radicado único
            $numeroRadicado = $this->generarNumeroRadicadoUnico();

            // 3) Crear radicado polimórfico y conservarlo para la vista
            $this->ultimoRadicado = $contrato->radicado()->create([
                'numero'  => $numeroRadicado,
                'modulo'  => 'contrato',
                'user_id' => auth()->id(), // null si no autenticado
            ]);
        });

        // 4) Limpiar formulario y restaurar estado
        $this->reset(['nombre','email','celular','empresa','nit','servicio','mensaje']);
        $this->estado = 'pendiente';

        // 5) Flash con número de radicado
        $this->flash = "¡Tu solicitud fue radicada correctamente! Número de radicado: {$numeroRadicado}.";
    }

    private function generarNumeroRadicadoUnico(): string
    {
        do {
            // Formato: CON-YYYY-XXXXXX
            $numero = 'CON-' . date('Y') . '-' . strtoupper(Str::random(6));
            $existe = Radicado::where('numero', $numero)->exists();
        } while ($existe);

        return $numero;
    }

    public function render()
    {
        // Stats para panel derecho (evita Undefined variable $stats)
        $stats = [
            'total'       => Contrato::count(),
            'pendientes'  => Contrato::where('estado', 'pendiente')->count(),
            'cerrados'    => Contrato::where('estado', 'cerrado')->count(),
        ];

        // Últimos 8 radicados del módulo contrato
        $radicados = Radicado::with('radicable')
            ->where('modulo', 'contrato')
            ->orderByDesc('created_at')
            ->take(8)
            ->get();

        return view('livewire.contratanos-page', [
            'stats'          => $stats,
            'radicados'      => $radicados,
            'ultimoRadicado' => $this->ultimoRadicado,
        ]);
    }
}
