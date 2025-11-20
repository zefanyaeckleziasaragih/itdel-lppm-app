<?php

namespace App\Http\Controllers\App\Penghargaan;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Request;

class PengajuanController extends Controller
{
    public function index(Request $request)
    {
        $auth = $request->attributes->get('auth');

        // Sementara pakai dummy data
        $pengajuan = [
            [
                'id'       => 1,
                'judul'    => 'Jurnal Dosen 1',
                'jenis'    => 'Jurnal',
                'penulis'  => 'Dosen 1, Dosen 2',
                'status'   => 'Belum disetujui',
                'tanggal'  => '2025-05-10',
                'kampus'   => 'IT Del',
                'fakultas' => 'Fakultas Informatika',
                'prodi'    => 'Informatika',
            ],
            [
                'id'       => 2,
                'judul'    => 'Seminar Dosen 3',
                'jenis'    => 'Seminar',
                'penulis'  => 'Dosen 3',
                'status'   => 'Disetujui',
                'tanggal'  => '2025-06-01',
                'kampus'   => 'IT Del',
                'fakultas' => 'Fakultas Teknik Industri',
                'prodi'    => 'Teknik Industri',
            ],
        ];

        return Inertia::render('app/penghargaan/daftar-pengajuan-page', [
            'auth'      => Inertia::always($auth),
            'pageName'  => Inertia::always('Daftar Pengajuan Penghargaan'),
            'pengajuan' => $pengajuan,
        ]);
    }
}
