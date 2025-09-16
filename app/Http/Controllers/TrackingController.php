<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Radicado;

class TrackingController extends Controller
{
    public function lookup(Request $request)
    {
        // 1) Validación
        $request->validate([
            'code' => ['required', 'string', 'max:50'],
        ]);

        // 2) Normalizar código: quitar espacios y pasar a mayúsculas
        $code = Str::upper(preg_replace('/\s+/', '', $request->get('code')));

        // 3) Buscar el radicado (case-insensitive en Postgres)
        $query = Radicado::query();
        $driver = config('database.default');

        if ($driver === 'pgsql') {
            $radicado = $query->whereRaw('numero ILIKE ?', [$code])->first();
        } else {
            $radicado = $query->where('numero', $code)->first();
        }

        // 4) No encontrado
        if (!$radicado) {
            if ($request->wantsJson()) {
                return response()->json([
                    'ok'      => false,
                    'message' => 'No encontramos ningún ticket con ese código.',
                ], 404);
            }

            return back()
                ->withInput()
                ->with('tracking_error', 'No encontramos ningún ticket con ese código.');
        }

        // 5) Encontrado: si piden JSON (AJAX desde el modal), devolver datos listos
        if ($request->wantsJson()) {
            $modelo = $radicado->radicable; // contrato / soporte / pqr (relación polimórfica)

            return response()->json([
                'ok'   => true,
                'data' => [
                    'codigo'       => $radicado->numero,
                    'modulo'       => $radicado->modulo,
                    'estado'       => $modelo->estado ?? null,
                    'mensaje'      => $modelo->mensaje ?? null,               // si aplica
                    'creado'       => optional($radicado->created_at)->timezone('America/Bogota')->format('Y-m-d H:i'),
                    'actual'       => now('America/Bogota')->format('Y-m-d H:i'),
                ],
            ]);
        }

        // 6) Fallback HTML (vista tradicional)
        return view('tracking.result', [
            'radicado' => $radicado,
            'modelo'   => $radicado->radicable, // contrato / soporte / pqr
        ]);
    }
}
