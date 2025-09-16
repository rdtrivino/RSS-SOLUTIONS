<?php

namespace App\Livewire; // ⇐ si usas v2: App\Http\Livewire

use Livewire\Component;
use App\Models\Pqr;
use Illuminate\Support\Facades\Auth;

class PqrForm extends Component
{
    public $tipo = 'peticion';
    public $descripcion = '';

    protected function rules()
    {
        return [
            'tipo'        => 'required|in:peticion,queja,reclamo',
            'descripcion' => 'required|string|min:10',
        ];
    }

    public function radicar()
    {
        $this->validate();

        Pqr::create([
            'user_id'     => Auth::id(),
            'tipo'        => $this->tipo,
            'descripcion' => $this->descripcion,
            'estado'      => 'radicado',
        ]);

        session()->flash('success', '✅ Tu PQR fue radicada. Te contactaremos pronto.');
        $this->reset(['tipo','descripcion']);
        $this->tipo = 'peticion';
    }

    public function render()
    {
        return view('livewire.pqr-form')->layout('layouts.app');
    }
}
