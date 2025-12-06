<?php

namespace App\Http\Controllers\App\Penghargaan;

use App\Helper\ToolsHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class SeminarController extends Controller
{
    /**
     * Halaman Daftar Seminar yang sudah diajukan
     */
    public function daftarSeminar(Request $request)
    {
        $auth = $request->attributes->get('auth');
        
        // Query dari database LPPM (gunakan DB::connection jika perlu)
        // Cari dosen berdasarkan user_id
        $dosen = DB::connection('pgsql_lppm')
            ->table('m_dosen')
            ->where('user_id', $auth->id)
            ->first();
        
        if (!$dosen) {
            return redirect()->route('home')
                ->with('error', 'Data dosen tidak ditemukan. Silakan hubungi admin.');
        }

        // Ambil seminar milik dosen ini dengan penghargaan
        $seminarList = DB::connection('pgsql_lppm')
            ->table('p_seminar_user as psu')
            ->join('m_seminar as s', 'psu.seminar_id', '=', 's.id')
            ->leftJoin('t_penghargaan_seminar as ps', 's.id', '=', 'ps.seminar_id')
            ->where('psu.user_id', $auth->id)
            ->select(
                's.id',
                's.nama_forum as judul',
                's.website as url',
                DB::raw("COALESCE(ps.status, 'Belum Diajukan') as status"),
                's.created_at as tanggal_pengajuan'
            )
            ->orderBy('s.created_at', 'desc')
            ->get()
            ->map(function ($seminar) {
                return [
                    'id' => $seminar->id,
                    'judul' => $seminar->judul,
                    'penulis' => 'Data Penulis', // Sesuaikan dengan kebutuhan
                    'status' => $seminar->status === 'Sudah Dicairkan' ? 'Sudah Dicairkan' : 'Belum Dicairkan',
                    'tanggal_pengajuan' => $seminar->tanggal_pengajuan,
                ];
            })
            ->toArray();

        return Inertia::render('app/penghargaan/daftar-seminar-page', [
            'auth' => Inertia::always($auth),
            'pageName' => Inertia::always('Daftar Seminar'),
            'seminarList' => $seminarList,
        ]);
    }

    /**
     * Halaman pilih prosiding (dummy data untuk contoh)
     */
    public function pilihProsiding(Request $request)
    {
        $auth = $request->attributes->get('auth');

        // Cari dosen
        $dosen = DB::connection('pgsql_lppm')
            ->table('m_dosen')
            ->where('user_id', $auth->id)
            ->first();
        
        if (!$dosen) {
            return redirect()->route('home')
                ->with('error', 'Data dosen tidak ditemukan');
        }

        // Ambil list seminar yang tersedia
        $prosidingList = DB::connection('pgsql_lppm')
            ->table('m_seminar')
            ->select('id', 'nama_forum as judul', 'website as url')
            ->get()
            ->map(function($p) use ($dosen) {
                return [
                    'id' => $p->id,
                    'judul' => $p->judul,
                    'sinta_id' => $dosen->sinta_id ?? '',
                    'scopus_id' => $dosen->scopus_id ?? '',
                ];
            })
            ->toArray();

        return Inertia::render('app/penghargaan/pilih-prosiding-page', [
            'auth' => Inertia::always($auth),
            'pageName' => Inertia::always('Pilih Prosiding'),
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

        if (!$prosidingId) {
            return redirect()->route('penghargaan.seminar.pilih')
                ->with('error', 'Pilih prosiding terlebih dahulu');
        }

        // Cari dosen
        $dosen = DB::connection('pgsql_lppm')
            ->table('m_dosen')
            ->where('user_id', $auth->id)
            ->first();
        
        if (!$dosen) {
            return redirect()->route('home')
                ->with('error', 'Data dosen tidak ditemukan');
        }

        // Ambil data prosiding
        $selectedProsiding = DB::connection('pgsql_lppm')
            ->table('m_seminar')
            ->where('id', $prosidingId)
            ->first();

        if (!$selectedProsiding) {
            return redirect()->route('penghargaan.seminar.pilih')
                ->with('error', 'Prosiding tidak ditemukan');
        }

        return Inertia::render('app/penghargaan/pengajuan-seminar-page', [
            'auth' => Inertia::always($auth),
            'pageName' => Inertia::always('Pengajuan Penghargaan Seminar'),
            'selectedProsiding' => [
                'id' => $selectedProsiding->id,
                'judul' => $selectedProsiding->nama_forum,
                'sinta_id' => $dosen->sinta_id ?? '',
                'scopus_id' => $dosen->scopus_id ?? '',
                'nama_forum' => $selectedProsiding->nama_forum,
                'penulis' => $auth->name,
                'institusi_penyelenggara' => 'IT Del',
                'waktu_pelaksanaan' => now()->format('Y-m-d'),
                'tempat_pelaksanaan' => 'Sitoluama',
                'url' => $selectedProsiding->website ?? '',
            ],
        ]);
    }

    /**
     * Simpan pengajuan seminar
     */
    public function storeSeminar(Request $request)
    {
        $auth = $request->attributes->get('auth');
        
        // Cari dosen
        $dosen = DB::connection('pgsql_lppm')
            ->table('m_dosen')
            ->where('user_id', $auth->id)
            ->first();
        
        if (!$dosen) {
            return redirect()->route('home')
                ->with('error', 'Data dosen tidak ditemukan');
        }

        $request->validate([
            'prosiding_id' => 'required',
        ]);

        try {
            // Insert ke pivot table p_seminar_user
            $seminarUserId = ToolsHelper::generateId();
            
            DB::connection('pgsql_lppm')
                ->table('p_seminar_user')
                ->insert([
                    'id' => $seminarUserId,
                    'user_id' => $auth->id,
                    'seminar_id' => $request->prosiding_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

            return redirect()->route('penghargaan.seminar.daftar')
                ->with('success', 'Pengajuan seminar berhasil diajukan!');
                
        } catch (\Exception $e) {
            return redirect()->route('penghargaan.seminar.pilih')
                ->with('error', 'Gagal mengajukan seminar: ' . $e->getMessage());
        }
    }
}