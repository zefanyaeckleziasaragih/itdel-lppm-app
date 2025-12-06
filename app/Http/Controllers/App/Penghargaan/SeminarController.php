<?php

namespace App\Http\Controllers\App\Penghargaan;

use App\Helper\ToolsHelper;
use App\Http\Controllers\Controller;
use App\Models\LPPM\DosenModel;
use App\Models\LPPM\SeminarModel;
use App\Models\LPPM\PenghargaanSeminarModel;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SeminarController extends Controller
{
    /**
     * Halaman Daftar Seminar yang sudah diajukan
     */
    public function daftarSeminar(Request $request)
    {
        $auth = $request->attributes->get('auth');
        
        // Cari dosen
        $dosen = DosenModel::where('user_id', $auth->id)->first();
        
        if (!$dosen) {
            return redirect()->route('home')->with('error', 'Data dosen tidak ditemukan');
        }

        // Ambil seminar milik dosen ini dengan penghargaan
        $seminarList = $dosen->seminars()
            ->with('penghargaan')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($seminar) {
                return [
                    'id' => $seminar->id,
                    'judul' => $seminar->judul_makalah,
                    'penulis' => $seminar->dosens->pluck('user_id')->join(', '),
                    'status' => $seminar->penghargaan ? 
                        ($seminar->penghargaan->tgl_cair ? 'Sudah Dicairkan' : 'Belum Dicairkan') 
                        : 'Belum Diajukan',
                    'tanggal_pengajuan' => $seminar->created_at->format('Y-m-d'),
                ];
            });

        return Inertia::render('app/penghargaan/daftar-seminar-page', [
            'auth' => $auth,
            'seminarList' => $seminarList,
        ]);
    }

    /**
     * Halaman pilih prosiding (dummy data untuk contoh)
     */
    public function pilihProsiding(Request $request)
    {
        $auth = $request->attributes->get('auth');

        // TODO: Ganti dengan query real dari database
        $prosidingList = [
            [
                'id' => 1,
                'judul' => 'International Conference on AI 2024',
                'sinta_id' => '123456',
                'scopus_id' => 'SCOPUS-001',
            ],
            [
                'id' => 2,
                'judul' => 'Southeast Asian Conference on Software Engineering 2024',
                'sinta_id' => '789012',
                'scopus_id' => 'SCOPUS-002',
            ],
        ];

        return Inertia::render('app/penghargaan/pilih-prosiding-page', [
            'auth' => $auth,
            'prosidingList' => $prosidingList,
        ]);
    }

    /**
     * Form pengajuan seminar
     */
    public function formSeminar(Request $request)
    {
        $auth = $request->attributes->get('auth');
        $prosidingId = $request->query('prosiding_id');

        // TODO: Query prosiding dari database
        $selectedProsiding = [
            'id' => $prosidingId,
            'judul' => 'International Conference on AI 2024',
            'sinta_id' => '123456',
            'scopus_id' => 'SCOPUS-001',
            'nama_forum' => 'ICAI 2024',
            'penulis' => 'Dr. John Doe',
            'institusi_penyelenggara' => 'IEEE',
            'waktu_pelaksanaan' => '2024-10-15',
            'tempat_pelaksanaan' => 'Bali',
            'url' => 'https://icai2024.com',
        ];

        return Inertia::render('app/penghargaan/pengajuan-seminar-page', [
            'auth' => $auth,
            'selectedProsiding' => $selectedProsiding,
        ]);
    }

    /**
     * Simpan pengajuan seminar
     */
    public function storeSeminar(Request $request)
    {
        $auth = $request->attributes->get('auth');
        
        // Cari dosen
        $dosen = DosenModel::where('user_id', $auth->id)->first();
        
        if (!$dosen) {
            return redirect()->route('home')->with('error', 'Data dosen tidak ditemukan');
        }

        $request->validate([
            'prosiding_id' => 'required|integer',
        ]);

        // TODO: Ambil data prosiding dari database berdasarkan prosiding_id
        // Sementara hardcode untuk contoh
        
        $seminar = SeminarModel::create([
            'id' => ToolsHelper::generateId(),
            'judul_makalah' => 'Judul dari prosiding', // TODO: dari database
            'nama_prosiding' => 'Nama prosiding',
            'nama_forum' => 'Nama forum',
            'penyelenggara' => 'Penyelenggara',
            'lokasi' => 'Lokasi',
            'tanggal_pelaksanaan' => now(),
            'url' => '',
            'tingkat' => 'Internasional',
            'jenis' => 'Konferensi',
        ]);

        // Hubungkan dengan dosen
        $seminar->dosens()->attach($dosen->id);

        return redirect()->route('penghargaan.seminar.daftar')
            ->with('success', 'Pengajuan seminar berhasil diajukan!');
    }
}