<?php

namespace App\Http\Controllers\App\Penghargaan;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Request;

class StatistikController extends Controller
{
    public function index(Request $request)
        {
            // Redirect ke halaman seminar karena statistik tidak digunakan untuk dosen
            return redirect()->route('penghargaan.seminar');
        }
}
