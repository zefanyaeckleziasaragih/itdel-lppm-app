<?php

namespace App\Http\Controllers\App\Penghargaan;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Request;

class PengajuanController extends Controller
{
// Halaman daftar seminar yang sudah diajukan
    public function daftarSeminar(Request $request)
    {
        $auth = $request->attributes->get('auth');

        // Data seminar yang sudah diajukan (dummy)
        $seminarList = [
            [
                'id' => 1,
                'judul' => 'Seminar 1',
                'penulis' => 'Penulis 1, Penulis 2',
                'status' => 'belum dicairkan',
                'tanggal_pengajuan' => '2025-01-15',
            ],
            [
                'id' => 2,
                'judul' => 'Seminar 2',
                'penulis' => 'Penulis 1, Penulis 2',
                'status' => 'belum dicairkan',
                'tanggal_pengajuan' => '2025-01-20',
            ],
            [
                'id' => 3,
                'judul' => 'Seminar 3',
                'penulis' => 'Penulis 1, Penulis 2',
                'status' => 'sudah dicairkan',
                'tanggal_pengajuan' => '2024-12-10',
            ],
            [
                'id' => 4,
                'judul' => 'Seminar 4',
                'penulis' => 'Penulis 1, Penulis 2',
                'status' => 'sudah dicairkan',
                'tanggal_pengajuan' => '2024-11-25',
            ],
        ];

        return Inertia::render('app/penghargaan/daftar-seminar-page', [
            'auth' => Inertia::always($auth),
            'pageName' => Inertia::always('Daftar Seminar'),
            'seminarList' => $seminarList,
        ]);
    }

    // Halaman pilih prosiding (halaman pertama)
    public function pilihProsiding(Request $request)
    {
        $auth = $request->attributes->get('auth');

        // Data prosiding seminar dummy
        $prosidingList = [
            [
                'id' => 1,
                'judul' => 'International Conference on Artificial Intelligence and Machine Learning 2024',
                'sinta_id' => '123456',
                'scopus_id' => 'SCOPUS-2024-001',
            ],
            [
                'id' => 2,
                'judul' => 'Southeast Asian Conference on Software Engineering 2024',
                'sinta_id' => '789012',
                'scopus_id' => 'SCOPUS-2024-002',
            ],
            [
                'id' => 3,
                'judul' => 'International Symposium on Database Systems 2025',
                'sinta_id' => '345678',
                'scopus_id' => 'SCOPUS-2025-003',
            ],
        ];

        return Inertia::render('app/penghargaan/pilih-prosiding-page', [
            'auth' => Inertia::always($auth),
            'pageName' => Inertia::always('Pilih Prosiding'),
            'prosidingList' => $prosidingList,
        ]);
    }

    // Halaman form pengajuan (halaman kedua)
    public function index(Request $request)
    {
        $auth = $request->attributes->get('auth');

        $prosidingId = $request->query('prosiding_id');
        
        // Data prosiding lengkap
        $allProsiding = [
            1 => [
                'id' => 1,
                'judul' => 'International Conference on Artificial Intelligence and Machine Learning 2024',
                'sinta_id' => '123456',
                'scopus_id' => 'SCOPUS-2024-001',
                'nama_forum' => 'ICAIML 2024',
                'penulis' => 'Dr. John Doe, Dr. Jane Smith',
                'institusi_penyelenggara' => 'IEEE Computer Society',
                'waktu_pelaksanaan' => '2024-10-15',
                'tempat_pelaksanaan' => 'Bali International Convention Center',
                'url' => 'https://icaiml2024.com',
            ],
            2 => [
                'id' => 2,
                'judul' => 'Southeast Asian Conference on Software Engineering 2024',
                'sinta_id' => '789012',
                'scopus_id' => 'SCOPUS-2024-002',
                'nama_forum' => 'SEACSE 2024',
                'penulis' => 'Dr. Ahmad Rahman, Prof. Sarah Lee',
                'institusi_penyelenggara' => 'University of Singapore',
                'waktu_pelaksanaan' => '2024-11-20',
                'tempat_pelaksanaan' => 'Singapore Convention Center',
                'url' => 'https://seacse2024.sg',
            ],
            3 => [
                'id' => 3,
                'judul' => 'International Symposium on Database Systems 2025',
                'sinta_id' => '345678',
                'scopus_id' => 'SCOPUS-2025-003',
                'nama_forum' => 'ISDS 2025',
                'penulis' => 'Dr. Michael Chen, Dr. Lisa Wang',
                'institusi_penyelenggara' => 'ACM SIGMOD',
                'waktu_pelaksanaan' => '2025-03-10',
                'tempat_pelaksanaan' => 'Jakarta Convention Center',
                'url' => 'https://isds2025.org',
            ],
        ];

        $selectedProsiding = isset($allProsiding[$prosidingId]) ? $allProsiding[$prosidingId] : null;

        return Inertia::render('app/penghargaan/pengajuan-seminar-page', [
            'auth' => Inertia::always($auth),
            'pageName' => Inertia::always('Pengajuan Penghargaan Seminar'),
            'selectedProsiding' => $selectedProsiding,
        ]);
    }

    public function storeSeminar(Request $request)
    {
        $auth = $request->attributes->get('auth');

        $request->validate([
            'prosiding_id' => 'required|integer',
        ]);

        // Di sini seharusnya menyimpan ke database
        // Untuk sementara hanya return success

        return redirect()->route('penghargaan.seminar.daftar')->with('success', 'Pengajuan seminar berhasil diajukan!');
    }
    
}
