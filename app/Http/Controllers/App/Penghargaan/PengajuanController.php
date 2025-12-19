<?php

namespace App\Http\Controllers\App\Penghargaan;

use App\Helper\ToolsHelper;
use App\Http\Controllers\Controller;

// LPPM / HRD models (punyamu)
use App\Models\PenghargaanLppmKelompok5\PenghargaanJurnal;
use App\Models\PenghargaanLppmKelompok5\PenghargaanSeminar;

// Dosen side
use App\Models\DosenModel;
use App\Models\SeminarModel;
use App\Models\PenghargaanSeminarModel;
use App\Models\SeminarUserModel;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PengajuanController extends Controller
{
    // ==========================
    // HELPER KONVERSI STATUS LPPM
    // ==========================
    private function mapStatusDbToLabel(?string $status): string
    {
        return match ($status) {
            'disetujui'      => 'Setuju',
            'ditolak'        => 'Menolak',
            'pengajuan_baru' => 'Belum disetujui',
            default          => 'Belum disetujui',
        };
    }

    private function mapStatusLabelToDb(string $label): string
    {
        return match ($label) {
            'Setuju'          => 'disetujui',
            'Menolak'         => 'ditolak',
            'Belum disetujui' => 'pengajuan_baru',
            default           => 'pengajuan_baru',
        };
    }

    // ===============================
    // LIST PENGAJUAN (HALAMAN LPPM)
    // ===============================
    public function index(Request $request)
    {
        $auth = $request->attributes->get('auth');

        $jurnalAwards = PenghargaanJurnal::query()
            ->leftJoin('m_jurnal', 'm_jurnal.id', '=', 't_penghargaan_jurnal.jurnal_id')
            ->select(
                't_penghargaan_jurnal.id',
                'm_jurnal.judul_paper as judul',
                DB::raw("'Jurnal' as jenis"),
                DB::raw("'Dosen 1, Dosen 2' as penulis"), // TODO: ganti relasi dosen
                't_penghargaan_jurnal.status_pengajuan as status_db',
                't_penghargaan_jurnal.tgl_pengajuan_penghargaan as tanggal'
            );

        $seminarAwards = PenghargaanSeminar::query()
            ->leftJoin('m_seminar', 'm_seminar.id', '=', 't_penghargaan_seminar.seminar_id')
            ->select(
                't_penghargaan_seminar.id',
                'm_seminar.nama_forum as judul',
                DB::raw("'Seminar' as jenis"),
                DB::raw("'Lola Simanjuntak' as penulis"), // TODO: ganti relasi dosen
                't_penghargaan_seminar.status_pengajuan as status_db',
                't_penghargaan_seminar.tgl_pengajuan_penghargaan as tanggal'
            );

        $union = $jurnalAwards->unionAll($seminarAwards);

        $rows = DB::query()
            ->fromSub($union, 'x')
            ->orderBy('tanggal', 'desc')
            ->get();

        $pengajuan = $rows->map(function ($row) {
            $statusLabel = $this->mapStatusDbToLabel($row->status_db);

            return [
                'id'      => $row->id,
                'judul'   => $row->judul,
                'jenis'   => $row->jenis,
                'penulis' => $row->penulis,
                'status'  => $statusLabel,
                'tanggal' => $row->tanggal,

                'kampus'   => 'IT Del',
                'fakultas' => $row->jenis === 'Jurnal'
                    ? 'Fakultas Informatika dan Teknik Elektro'
                    : 'Fakultas Teknik Industri',
                'prodi'    => $row->jenis === 'Jurnal'
                    ? 'Informatika'
                    : 'Teknik Industri',
            ];
        });

        return Inertia::render('app/penghargaan/daftar-pengajuan-page', [
            'auth'      => Inertia::always($auth),
            'pageName'  => Inertia::always('Daftar Pengajuan Penghargaan'),
            'pengajuan' => $pengajuan,
        ]);
    }

    // ===============================
    // DETAIL PENGAJUAN (LPPM VIEW)
    // ===============================
    public function show(Request $request, string $id)
    {
        $auth = $request->attributes->get('auth');

        $jurnal = PenghargaanJurnal::query()
            ->join('m_jurnal', 'm_jurnal.id', '=', 't_penghargaan_jurnal.jurnal_id')
            ->where('t_penghargaan_jurnal.id', $id)
            ->select('t_penghargaan_jurnal.*', 'm_jurnal.judul_paper', 'm_jurnal.nama_jurnal')
            ->first();

        if ($jurnal) {
            $statusLabel = $this->mapStatusDbToLabel($jurnal->status_pengajuan);

            $pengajuan = [
                'id'                => $jurnal->id,
                'nama_dosen'        => 'Dosen 1, Dosen 2', // TODO relasi
                'nip'               => '1987654321',
                'nik'               => '12710511010001',
                'jenis_penghargaan' => 'Publikasi Jurnal',
                'nama_kegiatan'     => $jurnal->judul_paper,
                'indeks'            => 'Scopus Q2 – ' . ($jurnal->nama_jurnal ?? 'Journal'),
                'dana_maksimum'     => $jurnal->nominal_usulan ?? 10_000_000,
                'status'            => $statusLabel,
                'bukti_url'         => '#',
                'dana_disetujui'    => $jurnal->nominal_disetujui,
            ];

            return Inertia::render('app/penghargaan/detail-pengajuan-jurnal-page', [
                'auth'      => Inertia::always($auth),
                'pageName'  => Inertia::always('Form Konfirmasi Jurnal'),
                'pengajuan' => $pengajuan,
            ]);
        }

        $seminar = PenghargaanSeminar::query()
            ->join('m_seminar', 'm_seminar.id', '=', 't_penghargaan_seminar.seminar_id')
            ->where('t_penghargaan_seminar.id', $id)
            ->select('t_penghargaan_seminar.*', 'm_seminar.nama_forum', 'm_seminar.website')
            ->firstOrFail();

        $statusLabel = $this->mapStatusDbToLabel($seminar->status_pengajuan);

        $pengajuan = [
            'id'                => $seminar->id,
            'nama_dosen'        => 'Lola Simanjuntak', // TODO relasi
            'nip'               => '1987654321',
            'nik'               => '12710511010001',
            'jenis_penghargaan' => 'Seminar',
            'nama_kegiatan'     => $seminar->nama_forum,
            'indeks'            => $seminar->website ?? '-',
            'dana_maksimum'     => $seminar->nominal_usulan ?? 7_500_000,
            'status'            => $statusLabel,
            'bukti_url'         => '#',
            'dana_disetujui'    => $seminar->nominal_disetujui,
        ];

        return Inertia::render('app/penghargaan/detail-pengajuan-seminar-page', [
            'auth'      => Inertia::always($auth),
            'pageName'  => Inertia::always('Form Konfirmasi Seminar'),
            'pengajuan' => $pengajuan,
        ]);
    }

    // ===============================
    // SIMPAN KONFIRMASI (STATUS & DANA) – LPPM
    // ===============================
    public function konfirmasi(Request $request, string $id)
    {
        $validated = $request->validate([
            'status'         => 'required|string|in:Setuju,Menolak,Belum disetujui',
            'dana_disetujui' => 'required|integer|min:0|max:2147483647',
        ]);

        $statusDb = $this->mapStatusLabelToDb($validated['status']);
        $nominal  = (int) $validated['dana_disetujui'];

        $jurnal  = PenghargaanJurnal::find($id);
        $seminar = null;

        if (!$jurnal) {
            $seminar = PenghargaanSeminar::find($id);
        }

        if (!$jurnal && !$seminar) {
            return back()->with('error', 'Data pengajuan tidak ditemukan.');
        }

        $maksimum = $jurnal?->nominal_usulan ?? $seminar?->nominal_usulan ?? 0;

        if ($nominal > $maksimum) {
            return back()
                ->withErrors([
                    'dana_disetujui' => 'Dana yang disetujui melebihi Dana Maksimum (' . number_format($maksimum, 0, ',', '.') . ').',
                ])
                ->withInput()
                ->with('error', 'Dana yang disetujui melebihi batas maksimum!');
        }

        $update = [
            'status_pengajuan'  => $statusDb,
            'nominal_disetujui' => $nominal,
        ];

        if ($statusDb === 'disetujui') {
            $update['status_hrd'] = 'belum_dicairkan';
        } else {
            $update['status_hrd'] = null;
        }

        if ($jurnal) {
            $jurnal->update($update);
        } else {
            $seminar->update($update);
        }

        return redirect()
            ->route('penghargaan.daftar')
            ->with('success', 'Data konfirmasi berhasil disimpan.');
    }

    // =====================================================
    // ========== BAGIAN DOSEN: JURNAL PENGHARGAAN ==========
    // =====================================================

    public function daftarJurnal(Request $request)
    {
        $auth = $request->attributes->get('auth');

        $dosen = DosenModel::where('user_id', $auth->id)->first();

        if (!$dosen) {
            $jurnalList = [];
        } else {
            $jurnalList = DB::table('m_jurnal as j')
                ->leftJoin('t_penghargaan_jurnal as pj', 'j.id', '=', 'pj.jurnal_id')
                // kalau jurnal punya user_id, pakai filter ini. kalau tidak, hapus 1 baris ini:
                ->where('j.user_id', $auth->id)
                ->select(
                    'j.id',
                    'j.judul_paper as judul',
                    'j.created_at as tanggal_pengajuan',
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
                ->map(function ($jurnal) use ($auth) {
                    return [
                        'id'                => $jurnal->id,
                        'judul'             => $jurnal->judul,
                        'penulis'           => $auth->name,
                        'status'            => $jurnal->status,
                        'tanggal_pengajuan' => $jurnal->tanggal_pengajuan,
                    ];
                })
                ->toArray();
        }

        return Inertia::render('app/penghargaan/daftar-jurnal-page', [
            'auth'      => Inertia::always($auth),
            'pageName'  => Inertia::always('Daftar Jurnal'),
            'jurnalList'=> $jurnalList,
        ]);
    }

    public function pilihJurnal(Request $request)
    {
        $auth = $request->attributes->get('auth');

        $dosen = DosenModel::where('user_id', $auth->id)->first();
        if (!$dosen) {
            return redirect()->route('penghargaan.jurnal.daftar')
                ->with('error', 'Data dosen tidak ditemukan. Silakan hubungi admin.');
        }

        // Ambil jurnal yang BELUM pernah diajukan penghargaan
        $jurnalList = DB::table('m_jurnal as j')
            ->leftJoin('t_penghargaan_jurnal as pj', 'j.id', '=', 'pj.jurnal_id')
            // kalau jurnal punya user_id, pakai filter ini. kalau tidak, hapus 1 baris ini:
            ->where('j.user_id', $auth->id)
            ->whereNull('pj.id')
            ->select('j.id', 'j.judul_paper', 'j.nama_jurnal')
            ->orderBy('j.created_at', 'desc')
            ->get()
            ->map(function ($j) use ($dosen) {
                return [
                    'id'        => $j->id,
                    'value'     => $j->id,
                    'label'     => $j->judul_paper . ($j->nama_jurnal ? ' — ' . $j->nama_jurnal : ''),
                    'judul'     => $j->judul_paper,
                    'sinta_id'  => $dosen->sinta_id ?? '',
                    'scopus_id' => $dosen->scopus_id ?? '',
                ];
            })
            ->toArray();

        return Inertia::render('app/penghargaan/pilih-jurnal-page', [
            'auth'      => Inertia::always($auth),
            'pageName'  => Inertia::always('Pilih Data Jurnal'),
            'jurnalList'=> $jurnalList,
        ]);
    }

    public function formJurnal(Request $request)
    {
        $auth     = $request->attributes->get('auth');
        $jurnalId = $request->query('jurnal_id');

        if (!$jurnalId) {
            return redirect()->route('penghargaan.jurnal.pilih')
                ->with('error', 'Pilih jurnal terlebih dahulu');
        }

        $dosen = DosenModel::where('user_id', $auth->id)->first();
        if (!$dosen) {
            return redirect()->route('home')->with('error', 'Data dosen tidak ditemukan');
        }

        $jurnal = DB::table('m_jurnal')->where('id', $jurnalId)->first();
        if (!$jurnal) {
            return redirect()->route('penghargaan.jurnal.pilih')
                ->with('error', 'Jurnal tidak ditemukan');
        }

        return Inertia::render('app/penghargaan/pengajuan-jurnal-page', [
            'auth'     => Inertia::always($auth),
            'pageName' => Inertia::always('Pengajuan Penghargaan Jurnal'),
            'selectedJurnal' => [
                'id'        => $jurnal->id,
                'judul'     => $jurnal->judul_paper ?? '',
                'nama_jurnal'=> $jurnal->nama_jurnal ?? '',
                'penulis'   => $auth->name,
                'sinta_id'  => $dosen->sinta_id ?? '',
                'scopus_id' => $dosen->scopus_id ?? '',
            ],
        ]);
    }

    /**
     * ✅ FIX UTAMA:
     * JANGAN PERNAH insert kolom "status" ke t_penghargaan_jurnal
     * yang dipakai: status_pengajuan
     */
    public function storeJurnal(Request $request)
    {
        $auth = $request->attributes->get('auth');

        $dosen = DosenModel::where('user_id', $auth->id)->first();
        if (!$dosen) {
            return redirect()->route('home')->with('error', 'Data dosen tidak ditemukan');
        }

        $request->validate([
            'jurnal_id' => 'required|exists:m_jurnal,id',
        ]);

        try {
            DB::beginTransaction();

            $existing = PenghargaanJurnal::where('jurnal_id', $request->jurnal_id)->first();
            if ($existing) {
                DB::rollBack();
                return redirect()->route('penghargaan.jurnal.daftar')
                    ->with('error', 'Jurnal ini sudah pernah diajukan untuk penghargaan!');
            }

            // ✅ INSERT TANPA kolom "status"
            PenghargaanJurnal::create([
                'id'                        => ToolsHelper::generateId(),
                'jurnal_id'                 => $request->jurnal_id,
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

            return redirect()->route('penghargaan.jurnal.daftar')
                ->with('success', 'Pengajuan penghargaan jurnal berhasil diajukan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('penghargaan.jurnal.pilih')
                ->with('error', 'Gagal mengajukan penghargaan jurnal: ' . $e->getMessage());
        }
    }

    // =====================================================
    // ========== BAGIAN DOSEN: SEMINAR PENGHARGAAN =========
    // =====================================================

    public function daftarSeminar(Request $request)
    {
        $auth = $request->attributes->get('auth');

        $dosen = DosenModel::where('user_id', $auth->id)->first();

        if (!$dosen) {
            $seminarList = [];
        } else {
            $seminarList = DB::table('p_seminar_user as psu')
                ->join('m_seminar as s', 'psu.seminar_id', '=', 's.id')
                ->leftJoin('t_penghargaan_seminar as ps', 's.id', '=', 'ps.seminar_id')
                ->where('psu.user_id', $auth->id)
                ->select(
                    's.id',
                    's.nama_forum as judul',
                    's.website as url',
                    's.created_at as tanggal_pengajuan',
                    DB::raw("CASE 
                        WHEN ps.tgl_cair IS NOT NULL THEN 'Sudah Dicairkan'
                        WHEN ps.tgl_approve_hrd IS NOT NULL THEN 'Disetujui HRD'
                        WHEN ps.tgl_verifikasi_lppm IS NOT NULL THEN 'Diverifikasi LPPM'
                        WHEN ps.id IS NOT NULL THEN 'Menunggu Verifikasi'
                        ELSE 'Belum Diajukan'
                    END as status")
                )
                ->orderBy('s.created_at', 'desc')
                ->get()
                ->map(function ($seminar) use ($auth) {
                    return [
                        'id'                => $seminar->id,
                        'judul'             => $seminar->judul,
                        'penulis'           => $auth->name,
                        'status'            => $seminar->status,
                        'tanggal_pengajuan' => $seminar->tanggal_pengajuan,
                    ];
                })
                ->toArray();
        }

        return Inertia::render('app/penghargaan/daftar-seminar-page', [
            'auth'        => Inertia::always($auth),
            'pageName'    => Inertia::always('Daftar Seminar'),
            'seminarList' => $seminarList,
        ]);
    }

    public function pilihProsiding(Request $request)
    {
        $auth = $request->attributes->get('auth');

        $dosen = DosenModel::where('user_id', $auth->id)->first();

        if (!$dosen) {
            return redirect()->route('penghargaan.seminar.daftar')
                ->with('error', 'Data dosen tidak ditemukan. Silakan hubungi admin.');
        }

        $prosidingList = DB::table('m_seminar as s')
            ->leftJoin('t_penghargaan_seminar as ps', 's.id', '=', 'ps.seminar_id')
            ->whereNull('ps.id')
            ->select('s.id', 's.nama_forum as judul', 's.website', 's.biaya')
            ->get()
            ->map(function ($p) use ($dosen) {
                return [
                    'id'        => $p->id,
                    'value'     => $p->id,
                    'label'     => $p->judul . ($p->website ? ' (' . $p->website . ')' : ''),
                    'judul'     => $p->judul,
                    'sinta_id'  => $dosen->sinta_id ?? '',
                    'scopus_id' => $dosen->scopus_id ?? '',
                    'website'   => $p->website ?? '',
                ];
            })
            ->toArray();

        return Inertia::render('app/penghargaan/pilih-prosiding-page', [
            'auth'          => Inertia::always($auth),
            'pageName'      => Inertia::always('Pilih Prosiding'),
            'prosidingList' => $prosidingList,
        ]);
    }

    public function formSeminar(Request $request)
    {
        $auth        = $request->attributes->get('auth');
        $prosidingId = $request->query('prosiding_id');

        if (!$prosidingId) {
            return redirect()->route('penghargaan.seminar.pilih')
                ->with('error', 'Pilih prosiding terlebih dahulu');
        }

        $dosen = DosenModel::where('user_id', $auth->id)->first();

        if (!$dosen) {
            return redirect()->route('home')->with('error', 'Data dosen tidak ditemukan');
        }

        $selectedProsiding = SeminarModel::find($prosidingId);

        if (!$selectedProsiding) {
            return redirect()->route('penghargaan.seminar.pilih')
                ->with('error', 'Prosiding tidak ditemukan');
        }

        return Inertia::render('app/penghargaan/pengajuan-seminar-page', [
            'auth'     => Inertia::always($auth),
            'pageName' => Inertia::always('Pengajuan Penghargaan Seminar'),
            'selectedProsiding' => [
                'id'                      => $selectedProsiding->id,
                'judul'                   => $selectedProsiding->nama_forum,
                'sinta_id'                => $dosen->sinta_id ?? '',
                'scopus_id'               => $dosen->scopus_id ?? '',
                'nama_forum'              => $selectedProsiding->nama_forum,
                'penulis'                 => $auth->name,
                'institusi_penyelenggara' => 'IT Del',
                'waktu_pelaksanaan'       => now()->format('Y-m-d'),
                'tempat_pelaksanaan'      => 'Sitoluama',
                'url'                     => $selectedProsiding->website ?? '',
            ],
        ]);
    }

    public function storeSeminar(Request $request)
    {
        $auth = $request->attributes->get('auth');

        $dosen = DosenModel::where('user_id', $auth->id)->first();
        if (!$dosen) {
            return redirect()->route('home')->with('error', 'Data dosen tidak ditemukan');
        }

        $request->validate([
            'prosiding_id' => 'required|exists:m_seminar,id',
        ]);

        try {
            DB::beginTransaction();

            $existing = PenghargaanSeminarModel::where('seminar_id', $request->prosiding_id)->first();
            if ($existing) {
                DB::rollBack();
                return redirect()->route('penghargaan.seminar.daftar')
                    ->with('error', 'Seminar ini sudah pernah diajukan untuk penghargaan!');
            }

            $seminarUserExists = SeminarUserModel::where('seminar_id', $request->prosiding_id)
                ->where('user_id', $auth->id)
                ->exists();

            if (!$seminarUserExists) {
                SeminarUserModel::create([
                    'id'         => ToolsHelper::generateId(),
                    'user_id'    => $auth->id,
                    'seminar_id' => $request->prosiding_id,
                ]);
            }

            PenghargaanSeminarModel::create([
                'id'                        => ToolsHelper::generateId(),
                'seminar_id'                => $request->prosiding_id,
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

            return redirect()->route('penghargaan.seminar.daftar')
                ->with('success', 'Pengajuan penghargaan seminar berhasil diajukan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('penghargaan.seminar.pilih')
                ->with('error', 'Gagal mengajukan penghargaan seminar: ' . $e->getMessage());
        }
    }
}
