<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkOrder;

class TrackingController extends Controller
{
    public function lookup(Request $request)
    {
        $request->validate(['code' => 'required|string|max:50']);
        $code = trim($request->code);

        $order = WorkOrder::query()
            ->where('tracking_code', $code)
            ->first();

        if (!$order) {
            return back()->with('tracking_error', 'No encontramos una orden con ese código. Verifica e intenta nuevamente.');
        }

        // Puedes devolver una vista elegante, o por ahora un JSON simple.
        return view('tracking.show', ['order' => $order, 'code' => $code]);
    }
}

