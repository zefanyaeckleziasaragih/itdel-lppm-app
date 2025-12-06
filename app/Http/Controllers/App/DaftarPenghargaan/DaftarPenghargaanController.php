<?php

namespace App\Http\Controllers\App\DaftarPenghargaan;

use App\Helper\ToolsHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DaftarPenghargaanController extends Controller
{
    public function index(Request $request)
    {
        $auth = $request->attributes->get('auth');
        $authToken = ToolsHelper::getAuthToken();

        $penghargaanList = [
            [
                'id' => 1,
                'title' => 'Seminar Dosen 1',
                'penulis' => ['Penulis 1', 'Penulis 2'],
                'status' => 'belum disetujui',
                'date' => '01 / 01 / 24'
            ],
            [
                'id' => 2,
                'title' => 'Seminar Dosen 2',
                'penulis' => ['Penulis 1', 'Penulis 2'],
                'status' => 'belum disetujui',
                'date' => '01 / 01 / 24'
            ],
            [
                'id' => 3,
                'title' => 'Seminar Dosen 3',
                'penulis' => ['Penulis 1', 'Penulis 2'],
                'status' => 'Disetujui',
                'date' => '01 / 01 / 24'
            ]
        ];

        return Inertia::render('app/daftar-penghargaan/daftar-penghargaan', [
            'auth' => Inertia::always($auth),
            'pageName' => Inertia::always('Daftar Penghargaan'),
            'authToken' => Inertia::always($authToken),
            'penghargaanList' => $penghargaanList,
        ]);
    }

    // Method baru untuk detail
    public function show(Request $request, $id)
    {
        $auth = $request->attributes->get('auth');
        $authToken = ToolsHelper::getAuthToken();

        // Data dummy detail (nanti bisa ambil dari database)
        $penghargaanDetail = [
            'id' => $id,
            'nama_dosen' => 'Glen rejeki',
            'nip' => '123xxxxx',
            'fakultas' => 'Fak. Vokasi',
            'prodi' => 'D3 Teknologi Informasi',
            'jenis_penghargaan' => 'Publikasi Jurnal',
            'judul_penghargaan' => 'Penelitian xxxxx',
            'status' => 'Menunggu',
            'bukti_pengajuan' => 'Detail',
            'nominal_disetujui' => 'Nominal Disetujui'
        ];

        return Inertia::render('app/daftar-penghargaan/detail-penghargaan', [
            'auth' => Inertia::always($auth),
            'pageName' => Inertia::always('Detail Penghargaan'),
            'authToken' => Inertia::always($authToken),
            'penghargaan' => $penghargaanDetail,
        ]);
    }
}