<?php

namespace App\Http\Controllers\App\HRD;

use App\Helper\ToolsHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use App\Models\PenghargaanLppmKelompok5\PenghargaanJurnal;
use App\Models\PenghargaanLppmKelompok5\PenghargaanSeminar;

class DaftarPenghargaanController extends Controller
{
    // ====================================
    // HELPER KONVERSI STATUS HRD -> LABEL
    // ====================================
    private function mapStatusHrdDbToLabel(?string $status): string
    {
        return match ($status) {
            'belum_dicairkan' => 'Belum dicairkan',
            'sudah_dicairkan' => 'Sudah dicairkan',
            default           => '-',
        };
    }

    /**
     * Daftar semua penghargaan (jurnal + seminar)
     * yang sudah disetujui LPPM.
     */
    public function index(Request $request)
    {
        $auth      = $request->attributes->get('auth');
        $authToken = ToolsHelper::getAuthToken();

        // =========================
        // 1. PENGHARGAAN JURNAL
        // =========================
        $jurnal = PenghargaanJurnal::query()
            ->join('m_jurnal', 'm_jurnal.id', '=', 't_penghargaan_jurnal.jurnal_id')
            ->where('t_penghargaan_jurnal.status_pengajuan', 'disetujui')
            ->orderBy('t_penghargaan_jurnal.tgl_pengajuan_penghargaan', 'desc')
            ->select(
                't_penghargaan_jurnal.id',
                't_penghargaan_jurnal.tgl_pengajuan_penghargaan',
                't_penghargaan_jurnal.status_hrd',
                'm_jurnal.judul_paper as judul'
            )
            ->get()
            ->map(function ($p) {
                $date = $p->tgl_pengajuan_penghargaan ?? $p->created_at;

                return [
                    'id'      => $p->id,
                    'jenis'   => 'jurnal',
                    'title'   => $p->judul,
                    'penulis' => ['Dosen 1', 'Dosen 2'],
                    'date'    => $date ? Carbon::parse($date)->toDateString() : null,
                    'status'  => $this->mapStatusHrdDbToLabel($p->status_hrd),
                ];
            });

        // =========================
        // 2. PENGHARGAAN SEMINAR
        // =========================
        $seminar = PenghargaanSeminar::query()
            ->join('m_seminar', 'm_seminar.id', '=', 't_penghargaan_seminar.seminar_id')
            ->where('t_penghargaan_seminar.status_pengajuan', 'disetujui')
            ->orderBy('t_penghargaan_seminar.tgl_pengajuan_penghargaan', 'desc')
            ->select(
                't_penghargaan_seminar.id',
                't_penghargaan_seminar.tgl_pengajuan_penghargaan',
                't_penghargaan_seminar.status_hrd',
                'm_seminar.nama_forum as judul'
            )
            ->get()
            ->map(function ($p) {
                $date = $p->tgl_pengajuan_penghargaan ?? $p->created_at;

                return [
                    'id'      => $p->id,
                    'jenis'   => 'seminar',
                    'title'   => $p->judul,
                    'penulis' => ['Lola Simanjuntak'],
                    'date'    => $date ? Carbon::parse($date)->toDateString() : null,
                    'status'  => $this->mapStatusHrdDbToLabel($p->status_hrd),
                ];
            });

        // =========================
        // 3. GABUNG + SORT DESC BY TANGGAL
        // =========================
        $penghargaanList = $jurnal
            ->merge($seminar)
            ->sortByDesc('date')
            ->values();

        return Inertia::render('app/daftar-penghargaan/daftar-penghargaan', [
            'auth'            => Inertia::always($auth),
            'pageName'        => Inertia::always('Daftar Penghargaan'),
            'authToken'       => Inertia::always($authToken),
            'penghargaanList' => $penghargaanList,
        ]);
    }

    /**
     * Detail penghargaan untuk HRD (Form Detail Pencairan Dana).
     */
    public function show(Request $request, string $id)
    {
        $auth      = $request->attributes->get('auth');
        $authToken = ToolsHelper::getAuthToken();

        // Coba sebagai penghargaan Jurnal
        $jurnal = PenghargaanJurnal::query()
            ->join('m_jurnal', 'm_jurnal.id', '=', 't_penghargaan_jurnal.jurnal_id')
            ->where('t_penghargaan_jurnal.id', $id)
            ->select(
                't_penghargaan_jurnal.*',
                'm_jurnal.judul_paper',
                'm_jurnal.nama_jurnal'
            )
            ->first();

        if ($jurnal) {
            $date = $jurnal->tgl_pengajuan_penghargaan
                ?? $jurnal->tanggal_diajukan
                ?? $jurnal->created_at;

            $penghargaan = [
                'id'                => $jurnal->id,
                'jenis'             => 'jurnal',
                'nama_dosen'        => 'Dosen 1, Dosen 2',
                'nip'               => '1987654321',
                'fakultas'          => 'FITE',
                'prodi'             => 'Informatika',
                'jenis_penghargaan' => 'Penghargaan Jurnal',
                'judul_penghargaan' => $jurnal->judul_paper,
                'status'            => $this->mapStatusHrdDbToLabel($jurnal->status_hrd),
                'bukti_pengajuan'   => '#',
                'nominal_disetujui' => $jurnal->nominal_disetujui,
                'tanggal'           => $date ? Carbon::parse($date)->toDateString() : null,
            ];

            return Inertia::render('app/daftar-penghargaan/detail-penghargaan', [
                'auth'        => Inertia::always($auth),
                'pageName'    => Inertia::always('Detail Penghargaan'),
                'authToken'   => Inertia::always($authToken),
                'penghargaan' => $penghargaan,
            ]);
        }

        // Kalau bukan jurnal → anggap seminar
        $seminar = PenghargaanSeminar::query()
            ->join('m_seminar', 'm_seminar.id', '=', 't_penghargaan_seminar.seminar_id')
            ->where('t_penghargaan_seminar.id', $id)
            ->select(
                't_penghargaan_seminar.*',
                'm_seminar.nama_forum'
            )
            ->firstOrFail();

        $date = $seminar->tgl_pengajuan_penghargaan
            ?? $seminar->tanggal_diajukan
            ?? $seminar->created_at;

        $penghargaan = [
            'id'                => $seminar->id,
            'jenis'             => 'seminar',
            'nama_dosen'        => 'Lola Simanjuntak',
            'nip'               => '1987654321',
            'fakultas'          => 'FTI',
            'prodi'             => 'Teknik Industri',
            'jenis_penghargaan' => 'Penghargaan Seminar',
            'judul_penghargaan' => $seminar->nama_forum,
            'status'            => $this->mapStatusHrdDbToLabel($seminar->status_hrd),
            'bukti_pengajuan'   => '#',
            'nominal_disetujui' => $seminar->nominal_disetujui,
            'tanggal'           => $date ? Carbon::parse($date)->toDateString() : null,
        ];

        return Inertia::render('app/daftar-penghargaan/detail-penghargaan', [
            'auth'        => Inertia::always($auth),
            'pageName'    => Inertia::always('Detail Penghargaan'),
            'authToken'   => Inertia::always($authToken),
            'penghargaan' => $penghargaan,
        ]);
    }

    /**
     * Aksi ketika tombol "Dana Dicairkan" diklik.
     * - Ubah status_hrd menjadi 'sudah_dicairkan'
     * - Redirect ke Dashboard HRD
     */
    public function cairkanDana(Request $request, string $id)
    {
        // Cek di jurnal dulu
        $jurnal = PenghargaanJurnal::find($id);

        if ($jurnal) {
            if ($jurnal->status_pengajuan !== 'disetujui') {
                return back()->with('error', 'Pengajuan belum disetujui LPPM.');
            }

            $updateData = [
                'status_hrd' => 'sudah_dicairkan',
            ];
            // $updateData['tgl_pencairan_dana'] = Carbon::now(); // kalau ada kolomnya

            $jurnal->update($updateData);

        } else {
            // Kalau bukan jurnal, anggap seminar
            $seminar = PenghargaanSeminar::findOrFail($id);

            if ($seminar->status_pengajuan !== 'disetujui') {
                return back()->with('error', 'Pengajuan belum disetujui LPPM.');
            }

            $updateData = [
                'status_hrd' => 'sudah_dicairkan',
            ];
            // $updateData['tgl_pencairan_dana'] = Carbon::now(); // kalau ada kolomnya

            $seminar->update($updateData);
        }

        // ⬇⬇⬇ DI SINI DIUBAH: langsung ke Dashboard HRD
        return redirect()
            ->route('penghargaan.dashboard-hrd')
            ->with('success', 'Status dana berhasil diubah menjadi "Sudah dicairkan".');
    }
}
