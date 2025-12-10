<?php

namespace App\Http\Controllers\App\PengajuanJurnal;

use App\Helper\ToolsHelper;
use App\Http\Controllers\Controller;
use App\Models\DosenModel;
use App\Models\JurnalModel;
use App\Models\PenghargaanJurnalModel;
use App\Models\JurnalUserModel;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class JurnalController extends Controller
{
    /**
     * Halaman Daftar Jurnal yang sudah diajukan penghargaan
     */
    public function index(Request $request)
    {
        $auth = $request->attributes->get('auth');
        
        // Cari dosen
        $dosen = DosenModel::where('user_id', $auth->id)->first();
        
        if (!$dosen) {
            // Jika bukan dosen, tampilkan list kosong
            $jurnal = [];
        } else {
            $jurnal = DB::table('p_jurnal_user as pju')
                ->join('m_jurnal as j', 'pju.jurnal_id', '=', 'j.id')
                ->leftJoin('t_penghargaan_jurnal as pj', 'j.id', '=', 'pj.jurnal_id')
                ->where('pju.user_id', $auth->id)
                ->select(
                    'j.id',
                    'j.judul_paper as judul',
                    'j.created_at as tanggal',
                    DB::raw("CASE 
                        WHEN pj.tgl_cair IS NOT NULL THEN 'Sudah Dicairkan'
                        WHEN pj.tgl_approve_hrd IS NOT NULL THEN 'Disetujui HRD'
                        WHEN pj.tgl_verifikasi_lppm IS NOT NULL THEN 'Diverifikasi LPPM'
                        WHEN pj.id IS NOT NULL THEN 'Menunggu Verifikasi'
                        ELSE 'Belum Diajukan'
                    END as status")
                )
                ->orderBy('j.created_at', 'desc')
                ->get()
                ->map(function ($item) use ($auth) {
                    return [
                        'id' => $item->id,
                        'judul' => $item->judul,
                        'penulis' => $auth->name,
                        'status' => $item->status,
                        'tanggal' => date('Y-m-d', strtotime($item->tanggal)),
                    ];
                })
                ->toArray();
        }

        return Inertia::render('app/PengajuanJurnal/DaftarJurnalPage', [
            'auth' => Inertia::always($auth),
            'pageName' => Inertia::always('Daftar Jurnal'),
            'jurnal' => $jurnal,
        ]);
    }

    /**
     * Halaman Pilih Data Jurnal dari Database
     */
    public function pilihData(Request $request)
    {
        $auth = $request->attributes->get('auth');
        
        // Cari dosen
        $dosen = DosenModel::where('user_id', $auth->id)->first();
        
        if (!$dosen) {
            return redirect()->route('pengajuan.jurnal.daftar')
                ->with('error', 'Data dosen tidak ditemukan. Silakan hubungi admin.');
        }

        $jurnalList = DB::table('m_jurnal as j')
            ->leftJoin('t_penghargaan_jurnal as pj', 'j.id', '=', 'pj.jurnal_id')
            ->whereNull('pj.id')
            ->select(
                'j.id',
                'j.judul_paper as judul',
                'j.nama_jurnal',
                'j.issn',
                'j.volume',
                'j.nomor'
            )
            ->get()
            ->map(function($jurnal) {
                return [
                    'id' => $jurnal->id,
                    'value' => $jurnal->id,
                    'label' => $jurnal->judul . ' - ' . $jurnal->nama_jurnal . ' (ISSN: ' . $jurnal->issn . ')',
                ];
            })
            ->toArray();

        // Debug: Log jumlah data
        \Log::info('Jurnal List Count: ' . count($jurnalList));

        return Inertia::render('app/PengajuanJurnal/PilihDataPenghargaanPage', [
            'auth' => Inertia::always($auth),
            'pageName' => Inertia::always('Pilih Data Jurnal'),
            'jurnalList' => $jurnalList,
            'sinta_id' => $dosen->sinta_id ?? '',
            'scopus_id' => $dosen->scopus_id ?? '',
        ]);
    }

    /**
     * Halaman Form Penghargaan (Auto-fill dari database)
     */
    public function form(Request $request)
    {
        $auth = $request->attributes->get('auth');
        $jurnalId = $request->query('jurnal_id');
        
        if (!$jurnalId) {
            return redirect()->route('pengajuan.jurnal.pilih-data')
                ->with('error', 'Pilih jurnal terlebih dahulu');
        }

        // Cari dosen
        $dosen = DosenModel::where('user_id', $auth->id)->first();
        
        if (!$dosen) {
            return redirect()->route('home')
                ->with('error', 'Data dosen tidak ditemukan');
        }

        // Ambil data jurnal dari database
        $jurnal = JurnalModel::find($jurnalId);
        
        if (!$jurnal) {
            return redirect()->route('pengajuan.jurnal.pilih-data')
                ->with('error', 'Jurnal tidak ditemukan');
        }

        // Auto-fill data dari database
        return Inertia::render('app/PengajuanJurnal/FormPenghargaanJurnalPage', [
            'auth' => Inertia::always($auth),
            'pageName' => Inertia::always('Form Pengajuan Jurnal'),
            'sinta_id'   => $dosen->sinta_id ?? '',
            'scopus_id'  => $dosen->scopus_id ?? '',
            'jurnal_id'  => $jurnal->id,
            'judulMakalah' => $jurnal->judul_paper,
            'issn' => $jurnal->issn,
            'volume' => $jurnal->volume,
            'nomor' => $jurnal->nomor,
            'namaJurnal' => $jurnal->nama_jurnal,
            'jumlahHalaman' => $jurnal->jumlah_halaman,
            'url' => $jurnal->url,
            'penulis' => $auth->name,
            'isEdit' => false,
        ]);
    }

    /**
     * Submit Form - Simpan Pengajuan Penghargaan Jurnal
     */
    public function store(Request $request)
    {
        $auth = $request->attributes->get('auth');
        
        // Cari dosen
        $dosen = DosenModel::where('user_id', $auth->id)->first();
        
        if (!$dosen) {
            return redirect()->route('home')
                ->with('error', 'Data dosen tidak ditemukan');
        }

        $request->validate([
            'jurnal_id' => 'required|exists:m_jurnal,id',
        ]);

        try {
            DB::beginTransaction();

            // Cek apakah sudah pernah diajukan
            $existing = PenghargaanJurnalModel::where('jurnal_id', $request->jurnal_id)->first();
            
            if ($existing) {
                DB::rollBack();
                return redirect()->route('pengajuan.jurnal.daftar')
                    ->with('error', 'Jurnal ini sudah pernah diajukan untuk penghargaan!');
            }

            $jurnalUserExists = JurnalUserModel::where('jurnal_id', $request->jurnal_id)
                ->where('user_id', $auth->id)
                ->exists();

            if (!$jurnalUserExists) {
                \Log::info('Creating new jurnal_user relation', [
                    'user_id' => $auth->id,
                    'jurnal_id' => $request->jurnal_id
                ]);
                
                JurnalUserModel::create([
                    'id' => ToolsHelper::generateId(),
                    'user_id' => $auth->id,
                    'jurnal_id' => $request->jurnal_id,
                ]);
            }

            \Log::info('Creating penghargaan_jurnal', [
                'jurnal_id' => $request->jurnal_id
            ]);
            
            PenghargaanJurnalModel::create([
                'id' => ToolsHelper::generateId(),
                'jurnal_id' => $request->jurnal_id,
                'tanggal_diajukan' => now(),
                'status_pengajuan' => 'Diajukan',
                'nominal_usulan' => 0,
                'nominal_disetujui' => null,
                'status' => 'belum_diverifikasi',
                'tgl_pengajuan_penghargaan' => now(),
                'tgl_verifikasi_lppm' => null,
                'tgl_approve_hrd' => null,
                'tgl_cair' => null,
            ]);

            DB::commit();

            \Log::info('Successfully submitted jurnal penghargaan');

            return redirect()->route('pengajuan.jurnal.daftar')
                ->with('success', 'Pengajuan penghargaan jurnal berhasil diajukan!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error storing jurnal penghargaan: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->route('pengajuan.jurnal.pilih-data')
                ->with('error', 'Gagal mengajukan penghargaan jurnal: ' . $e->getMessage());
        }
    }
}