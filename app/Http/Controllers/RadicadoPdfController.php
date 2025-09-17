<?php

namespace App\Http\Controllers;

use App\Models\Radicado;
use App\Models\RadicadoRespuesta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class RadicadoPdfController extends Controller
{
    public function __invoke(Radicado $radicado, Request $request)
    {
        // Buscar la última respuesta que cerró el caso
        $respuesta = RadicadoRespuesta::where('radicado_id', $radicado->id)
            ->where('cierra_caso', true)
            ->latest('created_at')
            ->first();

        if (! $respuesta || ! $respuesta->pdf_path) {
            abort(Response::HTTP_NOT_FOUND, 'No hay PDF disponible para este radicado.');
        }

        // Validar que el archivo exista en el disco 'public'
        if (! Storage::disk('public')->exists($respuesta->pdf_path)) {
            abort(Response::HTTP_NOT_FOUND, 'El archivo PDF no se encuentra en el servidor.');
        }

        // Descargarlo o mostrarlo en navegador
        return Storage::disk('public')->download(
            $respuesta->pdf_path,
            'Radicado-'.$radicado->numero.'.pdf'
        );
    }
}
