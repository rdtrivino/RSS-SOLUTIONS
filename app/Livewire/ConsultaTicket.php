<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Radicado;

#[Layout('layouts.app')]
#[Title('Consultar ticket')]
class ConsultaTicket extends Component
{
    /** Número ingresado por el usuario (ej: PQR-2025-ABC123) */
    public string $numero = '';

    /** Radicado encontrado (si existe) */
    public ?Radicado $radicado = null;

    /** Datos normalizados del radicable para pintar en la vista */
    public array $data = [];

    /** Estado de la UI */
    public bool $searched = false;   // <- se marcara en cuanto se haga una búsqueda
    public string $errorMsg = '';    // <- mensaje cuando no hay resultados

    protected function rules(): array
    {
        return [
            'numero' => ['required', 'string', 'min:6', 'max:50'],
        ];
    }

    protected function messages(): array
    {
        return [
            'numero.required' => 'Ingresa el número de radicado.',
            'numero.min'      => 'El número es muy corto.',
            'numero.max'      => 'El número es muy largo.',
        ];
    }

    /**
     * Permite precargar consulta si viene como parámetro (opcional).
     * Ej: /consulta-nit?numero=PQR-2025-ABC123
     */
    public function mount(?string $numero = null): void
    {
        if ($numero) {
            $this->numero = $numero;
            $this->buscar();
        }
    }

    /** Normaliza cuando el usuario escribe */
    public function updatedNumero(): void
    {
        $this->numero = mb_strtoupper(trim($this->numero));
    }

    /** Acción principal: buscar el radicado y normalizar datos del radicable */
    public function buscar(): void
    {
        $this->validate();

        $this->searched = true;
        $this->errorMsg = '';
        $this->data     = [];
        $this->radicado = null;

        // Normaliza para coincidir con el índice único de la tabla `radicados`
        $n = mb_strtoupper(trim($this->numero));

        // Búsqueda exacta por numero (case-insensitive)
        $this->radicado = Radicado::with('radicable')
            ->whereRaw('UPPER(numero) = ?', [$n])
            ->first();

        if (!$this->radicado) {
            $this->errorMsg = 'No encontramos un ticket con ese número.';
            return;
        }

        // Construir datos normalizados para la vista
        $this->data = $this->normalizarRadicable();
    }

    /** Limpia resultados y formulario */
    public function limpiar(): void
    {
        $this->reset(['numero', 'radicado', 'data', 'searched', 'errorMsg']);
    }

    /**
     * Normaliza los campos más relevantes según el módulo (soporte/contrato/pqr)
     * para que la vista pinte sin condicionales complejos.
     */
    private function normalizarRadicable(): array
    {
        $r = $this->radicado;
        $m = $r->modulo;
        $x = $r->radicable; // modelo concreto (Soporte | Contrato | Pqr | ...)

        $base = [
            'radicado' => $r->numero,
            'modulo'   => $m,
            'creado'   => optional($r->created_at)->format('d/m/Y H:i'),
            'estado'   => $x->estado ?? null,
            'resumen'  => null,
            'detalles' => [],
        ];

        if (!$x) {
            $base['resumen'] = 'Sin información del documento asociado.';
            return $base;
        }

        switch ($m) {
            case 'soporte':
                $base['resumen'] = $x->titulo ?? 'Ticket de soporte';
                $base['detalles'] = array_filter([
                    'Descripción'     => $x->descripcion ?? null,
                    'Prioridad'       => $x->prioridad ?? null,
                    'Tipo servicio'   => $x->tipo_servicio ?? null,
                    'Modalidad'       => $x->modalidad ?? null,
                    'Fecha'           => optional($x->created_at)->format('d/m/Y H:i'),
                ], fn($v) => !is_null($v) && $v !== '');
                break;

            case 'contrato':
                $base['resumen'] = $x->servicio ?? 'Solicitud de contrato';
                $base['detalles'] = array_filter([
                    'Nombre'       => $x->nombre ?? null,
                    'Email'        => $x->email ?? null,
                    'Celular'      => $x->celular ?? null,
                    'Empresa'      => $x->empresa ?? null,
                    'NIT'          => $x->nit ?? null,
                    'Servicio'     => $x->servicio ?? null,
                    'Especificar'  => $x->especificar ?? null,
                    'Mensaje'      => $x->mensaje ?? null,
                    'Fecha'        => optional($x->created_at)->format('d/m/Y H:i'),
                ], fn($v) => !is_null($v) && $v !== '');
                break;

            case 'pqr':
                $base['resumen'] = isset($x->tipo) ? ('PQR: ' . ucfirst($x->tipo)) : 'PQR';
                $base['detalles'] = array_filter([
                    'Tipo'         => $x->tipo ?? null,
                    'Descripción'  => $x->descripcion ?? null,
                    'Fecha'        => optional($x->created_at)->format('d/m/Y H:i'),
                    'Usuario (ID)' => $x->user_id ?? null,
                ], fn($v) => !is_null($v) && $v !== '');
                break;

            default:
                $base['resumen'] = 'Documento asociado';
                $base['detalles'] = array_filter([
                    'ID asociado' => $x->id ?? null,
                    'Fecha'       => optional($x->created_at)->format('d/m/Y H:i'),
                ], fn($v) => !is_null($v) && $v !== '');
                break;
        }

        return $base;
    }

    public function render()
    {
        return view('livewire.consulta-ticket');
    }
}
