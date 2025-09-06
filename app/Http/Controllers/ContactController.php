<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function submit(Request $request)
    {
        $data = $request->validate([
            'name'    => ['required','string','max:120'],
            'email'   => ['nullable','email','max:150'],
            'phone'   => ['nullable','string','max:40'],
            'message' => ['required','string','max:2000'],
        ]);

        // Envía al mail configurado (por ahora MAIL_MAILER=log en .env)
        $to = config('mail.from.address', 'hello@example.com');

        try {
            Mail::raw(
                "Nuevo contacto:\n\nNombre: {$data['name']}\nEmail: {$data['email']}\nTel: {$data['phone']}\n\nMensaje:\n{$data['message']}",
                fn ($m) => $m->to($to)->subject('Contacto desde RSS Solutions')
            );

            return back()->with('contact_ok', '¡Gracias! Hemos recibido tu mensaje. Te contactaremos pronto.');
        } catch (\Throwable $e) {
            Log::error('CONTACT_ERROR', ['e'=>$e->getMessage()]);
            return back()->with('contact_error', 'No pudimos enviar tu mensaje. Intenta nuevamente.');
        }
    }
}
