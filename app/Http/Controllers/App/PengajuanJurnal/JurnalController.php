<?php

namespace App\Http\Controllers\App\PengajuanJurnal;

use App\Helper\ToolsHelper;
use App\Http\Controllers\Controller;

use App\Models\DosenModel;
use App\Models\JurnalModel;
use App\Models\PenghargaanJurnalModel;
use App\Models\JurnalUserModel;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class JurnalController extends Controller
{
    /**
     * Halaman Daftar Jurnal (punya user) + status penghargaan
     */
    public function index(Request $request)
    {
        $auth = $request->attributes->get('auth');

        $dosen = DosenModel::where('user_id', $auth->id)->first();

        if (!$dosen) {
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
                        'id'      => $item->id,
                        'judul'   => $item->judul,
                        'penulis' => $auth->name,
                        'status'  => $item->status,
                        'tanggal' => $item->tanggal ? date('Y-m-d', strtotime($item->tanggal)) : '',
                    ];
                })
                ->toArray();
        }

        return Inertia::render('app/PengajuanJurnal/DaftarJurnalPage', [
            'auth'     => Inertia::always($auth),
            'pageName' => Inertia::always('Daftar Jurnal'),
            'jurnal'   => $jurnal,
        ]);
    }

    /**
     * Halaman Pilih Data Jurnal:
     * - hanya jurnal milik user (p_jurnal_user)
     * - yang belum punya row di t_penghargaan_jurnal
     */
    public function pilihData(Request $request)
    {
        $auth = $request->attributes->get('auth');

        $dosen = DosenModel::where('user_id', $auth->id)->first();
        if (!$dosen) {
            return redirect()->route('pengajuan.jurnal.daftar')
                ->with('error', 'Data dosen tidak ditemukan. Silakan hubungi admin.');
        }

        $jurnalList = DB::table('p_jurnal_user as pju')
            ->join('m_jurnal as j', 'j.id', '=', 'pju.jurnal_id')
            ->leftJoin('t_penghargaan_jurnal as pj', 'j.id', '=', 'pj.jurnal_id')
            ->where('pju.user_id', $auth->id)
            ->whereNull('pj.id') // belum diajukan penghargaan
            ->select(
                'j.id',
                'j.judul_paper as judul',
                'j.nama_jurnal',
                'j.issn'
            )
            ->orderBy('j.created_at', 'desc')
            ->get()
            ->map(function ($jurnal) {
                $namaJurnal = $jurnal->nama_jurnal ? (' - ' . $jurnal->nama_jurnal) : '';
                $issn = $jurnal->issn ? (' (ISSN: ' . $jurnal->issn . ')') : '';

                return [
                    'id'    => $jurnal->id,
                    'value' => $jurnal->id,
                    'label' => $jurnal->judul . $namaJurnal . $issn,
                ];
            })
            ->toArray();

        return Inertia::render('app/PengajuanJurnal/PilihDataPenghargaanPage', [
            'auth'     => Inertia::always($auth),
            'pageName' => Inertia::always('Pilih Data Jurnal'),
            'jurnalList' => $jurnalList,
            'sinta_id'   => $dosen->sinta_id ?? '',
            'scopus_id'  => $dosen->scopus_id ?? '',
        ]);
    }

    /**
     * Halaman Form Penghargaan (auto-fill).
     * ✅ Pastikan jurnal tersebut milik user (p_jurnal_user).
     */
    public function form(Request $request)
    {
        $auth = $request->attributes->get('auth');
        $jurnalId = $request->query('jurnal_id');

        if (!$jurnalId) {
            return redirect()->route('pengajuan.jurnal.pilih-data')
                ->with('error', 'Pilih jurnal terlebih dahulu');
        }

        $dosen = DosenModel::where('user_id', $auth->id)->first();
        if (!$dosen) {
            return redirect()->route('home')
                ->with('error', 'Data dosen tidak ditemukan');
        }

        // ✅ validasi jurnal milik user
        $owned = DB::table('p_jurnal_user')
            ->where('user_id', $auth->id)
            ->where('jurnal_id', $jurnalId)
            ->exists();

        if (!$owned) {
            return redirect()->route('pengajuan.jurnal.pilih-data')
                ->with('error', 'Jurnal bukan milik Anda / tidak terdaftar pada akun ini.');
        }

        $jurnal = JurnalModel::find($jurnalId);
        if (!$jurnal) {
            return redirect()->route('pengajuan.jurnal.pilih-data')
                ->with('error', 'Jurnal tidak ditemukan');
        }

        return Inertia::render('app/PengajuanJurnal/FormPenghargaanJurnalPage', [
            'auth'        => Inertia::always($auth),
            'pageName'    => Inertia::always('Form Pengajuan Jurnal'),
            'sinta_id'    => $dosen->sinta_id ?? '',
            'scopus_id'   => $dosen->scopus_id ?? '',
            'jurnal_id'   => $jurnal->id,
            'judulMakalah'=> $jurnal->judul_paper ?? '',
            'issn'        => $jurnal->issn ?? '',
            'volume'      => $jurnal->volume ?? '',
            'nomor'       => $jurnal->nomor ?? '',
            'namaJurnal'  => $jurnal->nama_jurnal ?? '',
            'jumlahHalaman'=> $jurnal->jumlah_halaman ?? '',
            'url'         => $jurnal->url ?? '',
            'penulis'     => $auth->name,
            'isEdit'      => false,
        ]);
    }

    /**
     * Submit Form - Simpan Pengajuan Penghargaan Jurnal
     * ✅ Fix utama:
     * - jangan insert kolom "status" (kolom itu tidak ada)
     * - status_pengajuan gunakan "pengajuan_baru"
     * - pastikan jurnal milik user
     */
    public function store(Request $request)
    {
        $auth = $request->attributes->get('auth');

        $dosen = DosenModel::where('user_id', $auth->id)->first();
        if (!$dosen) {
            return redirect()->route('home')
                ->with('error', 'Data dosen tidak ditemukan');
        }

        $request->validate([
            'jurnal_id' => 'required|exists:m_jurnal,id',
        ]);

        $jurnalId = $request->jurnal_id;

        // ✅ validasi jurnal milik user
        $owned = DB::table('p_jurnal_user')
            ->where('user_id', $auth->id)
            ->where('jurnal_id', $jurnalId)
            ->exists();

        if (!$owned) {
            return redirect()->route('pengajuan.jurnal.pilih-data')
                ->with('error', 'Jurnal bukan milik Anda / tidak terdaftar pada akun ini.');
        }

        try {
            DB::beginTransaction();

            $existing = PenghargaanJurnalModel::where('jurnal_id', $jurnalId)->first();
            if ($existing) {
                DB::rollBack();
                return redirect()->route('pengajuan.jurnal.daftar')
                    ->with('error', 'Jurnal ini sudah pernah diajukan untuk penghargaan!');
            }

            // relasi p_jurnal_user harusnya sudah ada dari awal,
            // tapi kalau mau aman, tetap cek & create via model:
            $jurnalUserExists = JurnalUserModel::where('jurnal_id', $jurnalId)
                ->where('user_id', $auth->id)
                ->exists();

            if (!$jurnalUserExists) {
                JurnalUserModel::create([
                    'id'       => ToolsHelper::generateId(),
                    'user_id'  => $auth->id,
                    'jurnal_id'=> $jurnalId,
                ]);
            }

            // ✅ INSERT TANPA "status"
            PenghargaanJurnalModel::create([
                'id'                        => ToolsHelper::generateId(),
                'jurnal_id'                 => $jurnalId,
                'tanggal_diajukan'          => now(),
                'status_pengajuan'          => 'pengajuan_baru',
                'nominal_usulan'            => 0,
                'nominal_disetujui'         => null,
                'tgl_pengajuan_penghargaan' => now(),
                'tgl_verifikasi_lppm'       => null,
                'tgl_approve_hrd'           => null,
                'tgl_cair'                  => null,
            ]);

            DB::commit();

            return redirect()->route('pengajuan.jurnal.daftar')
                ->with('success', 'Pengajuan penghargaan jurnal berhasil diajukan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('pengajuan.jurnal.pilih-data')
                ->with('error', 'Gagal mengajukan penghargaan jurnal: ' . $e->getMessage());
        }
    }
}
