<?php

namespace App\Http\Controllers;

class AcademicController extends Controller
{
    public function courses()
    {
        usleep(500000);
        
        logger('CONTROLLER: consultando cursos');

        return response()->json([
            'courses' => [
                'Programación Web 2',
                'Arquitectura de Software',
                'Bases de Datos',
            ],
        ]);
    }
}