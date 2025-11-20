<?php

namespace App\Http\Controllers\App\Penghargaan;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Request;

class StatistikController extends Controller
{
    public function index(Request $request)
    {
        $auth = $request->attributes->get('auth');

        // ==========================
        // Dummy data, nanti diganti
        // ==========================
        $kampus = [
            ['id' => 1, 'nama' => 'IT Del'],
        ];

        $fakultas = [
            ['id' => 1, 'kampus_id' => 1, 'nama' => 'Fakultas Informatika'],
            ['id' => 2, 'kampus_id' => 1, 'nama' => 'Fakultas Teknik Industri'],
        ];

        $prodi = [
            ['id' => 1, 'fakultas_id' => 1, 'nama' => 'Informatika'],
            ['id' => 2, 'fakultas_id' => 1, 'nama' => 'Sistem Informasi'],
            ['id' => 3, 'fakultas_id' => 2, 'nama' => 'Teknik Industri'],
        ];

        // Contoh data statistik per bulan
        $statistik = [
            ['bulan' => 'Mei',     'jurnal' => 4, 'seminar' => 2, 'kampus_id' => 1, 'fakultas_id' => 1, 'prodi_id' => 1],
            ['bulan' => 'Juni',    'jurnal' => 3, 'seminar' => 5, 'kampus_id' => 1, 'fakultas_id' => 1, 'prodi_id' => 2],
            ['bulan' => 'Juli',    'jurnal' => 2, 'seminar' => 4, 'kampus_id' => 1, 'fakultas_id' => 2, 'prodi_id' => 3],
            ['bulan' => 'Agustus', 'jurnal' => 5, 'seminar' => 1, 'kampus_id' => 1, 'fakultas_id' => 1, 'prodi_id' => 1],
        ];

        return Inertia::render('app/penghargaan/statistik-page', [
            'auth'      => Inertia::always($auth),
            'pageName'  => Inertia::always('Statistik LPPM'),
            'kampus'    => $kampus,
            'fakultas'  => $fakultas,
            'prodi'     => $prodi,
            'statistik' => $statistik,
        ]);
    }
}
