<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Soporte;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class SoporteForm extends Component
{
    // Campos del formulario
    public string $titulo = '';
    public string $descripcion = '';
    public string $prioridad = 'media';

    // Nuevos campos de contacto / reporte
    public ?string $tipo_documento = null;      // CC | CE | NIT | PAS | TI
    public ?string $numero_documento = null;
    public ?string $ciudad = null;
    public ?string $direccion = null;
    public ?string $telefono = null;
    public ?string $tipo_servicio = null;       // Redes | Hardware | Software | Impresora | Servidor | Otros
    public string $modalidad = 'local';         // local | recoger

    // Campos del equipo (opcionales)
    public ?string $tipo_equipo = null;         // Portátil, PC, Servidor, etc.
    public ?string $marca = null;
    public ?string $modelo = null;
    public ?string $serial = null;
    public ?string $so = null;                  // Sistema operativo
    public ?string $accesorios = null;

    public string $mensaje = '';

    /** Catálogos para selects */
    public array $prioridades = ['baja', 'media', 'alta'];
    public array $tiposDocumento = ['CC', 'CE', 'NIT', 'PAS', 'TI'];
    public array $modalidades = ['local', 'recoger'];
    public array $tiposServicio = [
        'Redes', 'Hardware', 'Software', 'Impresora', 'Servidor', 'Otros',
    ];

    /** Resumen y lista */
    public array $stats = [
        'total'       => 0,
        'abiertos'    => 0,
        'en_progreso' => 0,
        'cerrados'    => 0,
    ];
    public $tickets = [];

    protected function rules(): array
    {
        return [
            // Base
            'titulo'            => ['required','string','min:3','max:150'],
            'descripcion'       => ['required','string','min:5'],
            'prioridad'         => ['required','in:baja,media,alta'],

            // Nuevos (requeridos)
            'tipo_documento'    => ['required','in:CC,CE,NIT,PAS,TI'],
            'numero_documento'  => ['required','regex:/^\d{6,15}$/'],     // solo dígitos, 6-15
            'ciudad'            => ['required','string','max:100'],
            'direccion'         => ['required','string','max:200'],
            'telefono'          => ['required','regex:/^\d{7,15}$/'],     // solo dígitos, 7-15
            'tipo_servicio'     => ['required','in:Redes,Hardware,Software,Impresora,Servidor,Otros'],
            'modalidad'         => ['required','in:local,recoger'],

            // Equipo (opcionales). Si quieres hacerlos obligatorios, cambia 'nullable' por 'required'.
            'tipo_equipo'       => ['nullable','string','max:100'],
            'marca'             => ['nullable','string','max:100'],
            'modelo'            => ['nullable','string','max:100'],
            'serial'            => ['nullable','string','max:100'],
            'so'                => ['nullable','string','max:100'],
            'accesorios'        => ['nullable','string','max:200'],
        ];
    }

    protected function messages(): array
    {
        return [
            'titulo.required'       => 'El título es obligatorio.',
            'titulo.min'            => 'El título debe tener al menos :min caracteres.',
            'titulo.max'            => 'El título no puede superar :max caracteres.',
            'descripcion.required'  => 'La descripción es obligatoria.',
            'descripcion.min'       => 'La descripción debe tener al menos :min caracteres.',
            'prioridad.required'    => 'Selecciona una prioridad.',
            'prioridad.in'          => 'Prioridad no válida.',

            'tipo_documento.required' => 'Selecciona el tipo de documento.',
            'tipo_documento.in'       => 'Tipo de documento no válido.',

            'numero_documento.required' => 'El número de documento es obligatorio.',
            'numero_documento.regex'    => 'El número de documento debe tener entre 6 y 15 dígitos (solo números).',

            'ciudad.required' => 'La ciudad es obligatoria.',
            'ciudad.max'      => 'La ciudad no puede superar :max caracteres.',

            'direccion.required' => 'La dirección es obligatoria.',
            'direccion.max'      => 'La dirección no puede superar :max caracteres.',

            'telefono.required' => 'El teléfono es obligatorio.',
            'telefono.regex'    => 'El teléfono debe tener entre 7 y 15 dígitos (solo números).',

            'tipo_servicio.required' => 'Selecciona el tipo de servicio.',
            'tipo_servicio.in'       => 'Tipo de servicio no válido.',

            'modalidad.required' => 'Selecciona la modalidad.',
            'modalidad.in'       => 'Modalidad no válida.',

            // Mensajes equipo (opcionales)
            'tipo_equipo.max' => 'Tipo de equipo no puede superar :max caracteres.',
            'marca.max'       => 'La marca no puede superar :max caracteres.',
            'modelo.max'      => 'El modelo no puede superar :max caracteres.',
            'serial.max'      => 'El serial no puede superar :max caracteres.',
            'so.max'          => 'El S.O. no puede superar :max caracteres.',
            'accesorios.max'  => 'Accesorios no puede superar :max caracteres.',
        ];
    }

    public function mount(): void
    {
        $this->cargarResumen();
    }

    private function cargarResumen(): void
    {
        $userId = Auth::id();

        $this->stats['total']       = Soporte::where('user_id', $userId)->count();
        $this->stats['abiertos']    = Soporte::where('user_id', $userId)->where('estado', 'abierto')->count();
        $this->stats['en_progreso'] = Soporte::where('user_id', $userId)->where('estado', 'en_progreso')->count();
        $this->stats['cerrados']    = Soporte::where('user_id', $userId)->where('estado', 'cerrado')->count();

        $this->tickets = Soporte::with('radicado')
            ->where('user_id', $userId)
            ->latest()
            ->take(8)
            ->get();
    }

    public function save(): void
    {
        // Normaliza números por si el navegador deja pegar caracteres
        $this->numero_documento = $this->numero_documento ? preg_replace('/\D/', '', $this->numero_documento) : null;
        $this->telefono         = $this->telefono ? preg_replace('/\D/', '', $this->telefono) : null;

        $this->validate();

        $data = [
            'user_id'          => Auth::id(),
            'titulo'           => trim($this->titulo),
            'descripcion'      => trim($this->descripcion),
            'prioridad'        => $this->prioridad,

            'tipo_documento'   => $this->tipo_documento,
            'numero_documento' => $this->numero_documento,
            'ciudad'           => $this->ciudad ? trim($this->ciudad) : null,
            'direccion'        => $this->direccion ? trim($this->direccion) : null,
            'telefono'         => $this->telefono,
            'tipo_servicio'    => $this->tipo_servicio,
            'modalidad'        => $this->modalidad,

            // Equipo
            'tipo_equipo'      => $this->tipo_equipo ? trim($this->tipo_equipo) : null,
            'marca'            => $this->marca ? trim($this->marca) : null,
            'modelo'           => $this->modelo ? trim($this->modelo) : null,
            'serial'           => $this->serial ? trim($this->serial) : null,
            'so'               => $this->so ? trim($this->so) : null,
            'accesorios'       => $this->accesorios ? trim($this->accesorios) : null,
        ];

        $soporte = Soporte::create($data);

        // Generar número de radicado
        $numero = 'SOP-'.date('Y').'-'.str_pad((string) $soporte->id, 6, '0', STR_PAD_LEFT);

        $radicado = $soporte->radicado()->create([
            'numero'  => $numero,
            'modulo'  => 'soporte',
            'user_id' => Auth::id(),
        ]);

        // Limpiar formulario
        $this->reset([
            'titulo','descripcion','prioridad',
            'tipo_documento','numero_documento','ciudad','direccion',
            'telefono','tipo_servicio',
            'tipo_equipo','marca','modelo','serial','so','accesorios',
        ]);
        $this->modalidad = 'local';

        $this->mensaje = "Soporte creado con éxito. Número de radicado: {$radicado->numero}";
        $this->cargarResumen();
    }

    public function render()
    {
        return view('livewire.soporte-form')->title('Soportes');
    }
}
