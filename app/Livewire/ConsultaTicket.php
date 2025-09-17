<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Radicado;
use App\Models\RadicadoRespuesta;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;

#[Layout('layouts.app')]
#[Title('Consultar ticket')]
class ConsultaTicket extends Component
{
    /** Número ingresado por el usuario (ej: PQR-2025-000123) */
    public string $numero = '';

    /** Radicado encontrado (si existe) */
    public ?Radicado $radicado = null;

    /** Datos normalizados del radicable para pintar en la vista */
    public array $data = [];

    /** Estado de la UI */
    public bool $searched = false;
    public string $errorMsg = '';

    /** URL firmada para descargar el PDF del radicado (si aplica) */
    public ?string $pdfUrl = null;

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

    public function mount(?string $numero = null): void
    {
        if ($numero) {
            $this->numero = mb_strtoupper(trim($numero));
            $this->buscar();
        }
    }

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
        $this->pdfUrl   = null;

        $n = mb_strtoupper(trim($this->numero));

        $this->radicado = Radicado::with('radicable')
            ->whereRaw('UPPER(numero) = ?', [$n])
            ->first();

        if (!$this->radicado) {
            $this->errorMsg = 'No encontramos un ticket con ese número.';
            return;
        }

        // Datos para pintar
        $this->data = $this->normalizarRadicable();

        // URL PDF: basada en radicado_respuestas (sirve para soporte, contrato y pqr)
        $this->pdfUrl = $this->computePdfUrl($this->radicado);
    }

    /** Limpia resultados y formulario */
    public function limpiar(): void
    {
        $this->reset(['numero', 'radicado', 'data', 'searched', 'errorMsg', 'pdfUrl']);
    }

    /** Genera URL firmada si hay un PDF almacenado en radicado_respuestas */
    private function computePdfUrl(Radicado $radicado): ?string
    {
        if (!Route::has('radicado.pdf')) {
            return null;
        }

        $respuesta = RadicadoRespuesta::where('radicado_id', $radicado->id)
            ->where('cierra_caso', true)
            ->whereNotNull('pdf_path')
            ->latest('created_at')
            ->first();

        if (!$respuesta) {
            return null;
        }

        // Verificar que el archivo exista (opcional pero útil para no mostrar botón roto)
        if (!Storage::disk('public')->exists($respuesta->pdf_path)) {
            return null;
        }

        return URL::signedRoute('radicado.pdf', ['radicado' => $radicado->id]);
    }

    /** Normaliza campos para la vista, independiente del módulo */
    private function normalizarRadicable(): array
    {
        $r = $this->radicado;
        $m = $r->modulo;
        $x = $r->radicable;

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
                ], fn($v) => $v !== null && $v !== '');
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
                ], fn($v) => $v !== null && $v !== '');
                break;

            case 'pqr':
                $base['resumen'] = isset($x->tipo) ? ('PQR: ' . ucfirst($x->tipo)) : 'PQR';
                $base['detalles'] = array_filter([
                    'Tipo'         => $x->tipo ?? null,
                    'Descripción'  => $x->descripcion ?? null,
                    'Fecha'        => optional($x->created_at)->format('d/m/Y H:i'),
                    'Usuario (ID)' => $x->user_id ?? null,
                ], fn($v) => $v !== null && $v !== '');
                break;

            default:
                $base['resumen'] = 'Documento asociado';
                $base['detalles'] = array_filter([
                    'ID asociado' => $x->id ?? null,
                    'Fecha'       => optional($x->created_at)->format('d/m/Y H:i'),
                ], fn($v) => $v !== null && $v !== '');
                break;
        }

        return $base;
    }

    public function render()
    {
        return view('livewire.consulta-ticket', [
            'pdfUrl' => $this->pdfUrl,
        ]);
    }
}
