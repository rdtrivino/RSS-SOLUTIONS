<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Demo. Luego podrás traer esto desde BD.
        $services = [
            ['title'=>'Soporte Técnico', 'desc'=>'Diagnóstico, reparación, mantenimiento y optimización de equipos.'],
            ['title'=>'Redes', 'desc'=>'Diseño, cableado, Wi-Fi, VPN, firewall y seguridad perimetral.'],
            ['title'=>'Mesa de Ayuda', 'desc'=>'Atención de tickets, SLA, inventario y soporte remoto.'],
            ['title'=>'Soluciones TI', 'desc'=>'Automatizaciones, backups, cloud, desarrollo a medida.'],
        ];

        $team = [
            [
                'name'=>'Rubén Triviño',
                'role'=>'Ing. de Soporte & Dev',
                'bio'=>'Experiencia en Laravel, redes y automatización de procesos TI.',
                'photo'=>'/images/team1.jpg', // pon tu ruta real
                'linkedin'=>'#',
            ],
            [
                'name'=>'Santiago',
                'role'=>'Ing. de Redes',
                'bio'=>'Especialista en infraestructura, switching, Wi-Fi y seguridad.',
                'photo'=>'/images/team2.jpg',
                'linkedin'=>'#',
            ],
            [
                'name'=>'Sebastián',
                'role'=>'Full-Stack Dev',
                'bio'=>'Desarrollo web/app, APIs, integración con servicios empresariales.',
                'photo'=>'/images/team3.jpg',
                'linkedin'=>'#',
            ],
        ];

        // Blog post demo. Luego lo reemplazas con DB (Posts).
        $posts = [
            [
                'title'=>'5 tips para acelerar tu PC',
                'excerpt'=>'Limpieza, disco SSD, RAM y más prácticas rápidas…',
                'cover'=>'https://images.unsplash.com/photo-1518779578993-ec3579fee39f?q=80&w=1200&auto=format&fit=crop',
                'url'=>'#'
            ],
            [
                'title'=>'Cómo proteger tu Wi-Fi',
                'excerpt'=>'Cambiar credenciales, WPA2/3, invitados y segmentación…',
                'cover'=>'https://images.unsplash.com/photo-1510511459019-5dda7724fd87?q=80&w=1200&auto=format&fit=crop',
                'url'=>'#'
            ],
            [
                'title'=>'Checklist antes de formatear',
                'excerpt'=>'Backups, drivers, licencias, claves y comprobaciones…',
                'cover'=>'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?q=80&w=1200&auto=format&fit=crop',
                'url'=>'#'
            ],
        ];

        $badges = [
            ['label'=>'Cisco Intro Cybersecurity', 'img'=>'/images/badges/cisco.png'],
            ['label'=>'Fortinet NSE 1-3', 'img'=>'/images/badges/fortinet.png'],
            ['label'=>'AWS Cloud Essentials', 'img'=>'/images/badges/aws.png'],
            ['label'=>'Laravel Pro', 'img'=>'/images/badges/laravel.png'],
        ];

        return view('home', compact('services','team','posts','badges'));
    }
}
