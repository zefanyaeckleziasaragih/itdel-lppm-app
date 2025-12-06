<?php

namespace App\Http\Controllers\App\Penghargaan;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Request;

class PengajuanController extends Controller
{
    // ===============================
    // DARI test-ifs23050
    // ===============================

    // Halaman daftar seminar yang sudah diajukan
    public function daftarSeminar(Request $request)
    {
        $auth = $request->attributes->get('auth');

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
            'auth'        => Inertia::always($auth),
            'pageName'    => Inertia::always('Daftar Seminar'),
            'seminarList' => $seminarList,
        ]);
    }

    // Halaman pilih prosiding
    public function pilihProsiding(Request $request)
    {
        $auth = $request->attributes->get('auth');

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
            'auth'         => Inertia::always($auth),
            'pageName'     => Inertia::always('Pilih Prosiding'),
            'prosidingList'=> $prosidingList,
        ]);
    }

    // Form pengajuan seminar (dari prosiding)
    public function formSeminar(Request $request)
    {
        $auth = $request->attributes->get('auth');
        $prosidingId = $request->query('prosiding_id');

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

        $selectedProsiding = isset($allProsiding[$prosidingId])
            ? $allProsiding[$prosidingId]
            : null;

        return Inertia::render('app/penghargaan/pengajuan-seminar-page', [
            'auth'             => Inertia::always($auth),
            'pageName'         => Inertia::always('Pengajuan Penghargaan Seminar'),
            'selectedProsiding'=> $selectedProsiding,
        ]);
    }

    public function storeSeminar(Request $request)
    {
        $request->validate([
            'prosiding_id' => 'required|integer',
        ]);

        return redirect()
            ->route('penghargaan.seminar.daftar')
            ->with('success', 'Pengajuan seminar berhasil diajukan!');
    }

    // ===============================
    // Daftar pengajuan penghargaan
    // ===============================
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
                'fakultas' => 'Fakultas Informatika dan Teknik Elektro',
                'prodi'    => 'Informatika',
            ],
            [
                'id'       => 2,
                'judul'    => 'Seminar Dosen 3',
                'jenis'    => 'Seminar Nasional',
                'penulis'  => 'Lola Simanjuntak',
                'status'   => 'Belum disetujui',
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

    // Detail pengajuan
    public function show(Request $request, $id)
    {
        $auth = $request->attributes->get('auth');

        if ((int) $id === 1) {
            $pengajuan = [
                'id'                => 1,
                'nama_dosen'        => 'Dosen 1, Dosen 2',
                'nip'               => '1987654321',
                'nik'               => '12710511010001',
                'jenis_penghargaan' => 'Publikasi Jurnal',
                'nama_kegiatan'     => 'Penerapan Machine Learning untuk Prediksi Cuaca',
                'indeks'            => 'Scopus Q2 – Journal of Computer Science',
                'dana_maksimum'     => 10000000,
                'status'            => 'Belum disetujui',
                'bukti_url'         => '#',
                'dana_disetujui'    => null,
            ];

            return Inertia::render('app/penghargaan/detail-pengajuan-jurnal-page', [
                'auth'      => Inertia::always($auth),
                'pageName'  => Inertia::always('Form Konfirmasi Jurnal'),
                'pengajuan' => $pengajuan,
            ]);
        }

        $pengajuan = [
            'id'                => (int) $id,
            'nama_dosen'        => 'Lola Simanjuntak',
            'nip'               => '1987654321',
            'nik'               => '12710511010001',
            'jenis_penghargaan' => 'Seminar Nasional',
            'nama_kegiatan'     => 'Implementasi AI untuk Pendidikan',
            'indeks'            => 'Scopus – Elsevier Procedia Computer Science',
            'dana_maksimum'     => 7500000,
            'status'            => 'Belum disetujui',
            'bukti_url'         => '#',
            'dana_disetujui'    => null,
        ];

        return Inertia::render('app/penghargaan/detail-pengajuan-seminar-page', [
            'auth'      => Inertia::always($auth),
            'pageName'  => Inertia::always('Form Konfirmasi Seminar'),
            'pengajuan' => $pengajuan,
        ]);
    }

    public function konfirmasi(Request $request, $id)
    {
        $validated = $request->validate([
            'status'         => 'required|string|in:Setuju,Menolak,Belum disetujui',
            'dana_disetujui' => 'required|numeric|min:0',
        ]);

        return redirect()
            ->route('penghargaan.daftar')
            ->with('success', 'Data konfirmasi berhasil disimpan.');
    }
}
