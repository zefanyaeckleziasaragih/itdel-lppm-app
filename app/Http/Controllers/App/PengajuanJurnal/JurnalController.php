<?php

namespace App\Http\Controllers\App\PengajuanJurnal;

use App\Helper\ToolsHelper;
use App\Http\Controllers\Controller;
use App\Models\LPPM\DosenModel;
use App\Models\LPPM\JurnalModel;
use App\Models\LPPM\PenghargaanJurnalModel;
use Illuminate\Http\Request;
use Inertia\Inertia;

class JurnalController extends Controller
{
    /**
     * Halaman Daftar Jurnal
     */
    public function index(Request $request)
    {
        $auth = $request->attributes->get('auth');
        
        // Cari dosen berdasarkan user_id
        $dosen = DosenModel::where('user_id', $auth->id)->first();
        
        if (!$dosen) {
            return redirect()->route('home')->with('error', 'Data dosen tidak ditemukan');
        }

        // Ambil jurnal milik dosen ini dengan penghargaan
        $jurnalList = $dosen->jurnals()
            ->with('penghargaan')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($jurnal) {
                return [
                    'id' => $jurnal->id,
                    'judul' => $jurnal->judul,
                    'penulis' => $jurnal->dosens->pluck('user_id')->join(', '), // Sesuaikan dengan kebutuhan
                    'status' => $jurnal->penghargaan ? $jurnal->penghargaan->status : 'Belum Diajukan',
                    'tanggal' => $jurnal->created_at->format('Y-m-d'),
                ];
            });

        return Inertia::render('app/PengajuanJurnal/DaftarJurnalPage', [
            'jurnal' => $jurnalList,
        ]);
    }

    /**
     * Halaman Form Penghargaan (Form Input)
     */
    public function form(Request $request)
    {
        $auth = $request->attributes->get('auth');
        
        // Cari dosen
        $dosen = DosenModel::where('user_id', $auth->id)->first();
        
        if (!$dosen) {
            return redirect()->route('home')->with('error', 'Data dosen tidak ditemukan');
        }

        return Inertia::render('app/PengajuanJurnal/FormPenghargaanJurnalPage', [
            'sinta_id' => $dosen->sinta_id,
            'scopus_id' => $dosen->scopus_id,
            'isEdit' => false,
        ]);
    }

    /**
     * Submit Form - Simpan Data Baru
     */
    public function store(Request $request)
    {
        $auth = $request->attributes->get('auth');
        
        // Cari dosen
        $dosen = DosenModel::where('user_id', $auth->id)->first();
        
        if (!$dosen) {
            return redirect()->route('home')->with('error', 'Data dosen tidak ditemukan');
        }

        $validated = $request->validate([
            'judulMakalah' => 'required|string|max:500',
            'issn' => 'required|string|max:50',
            'volume' => 'nullable|string|max:50',
            'nomor' => 'nullable|string|max:50',
            'halPaper' => 'nullable|string|max:50',
            'tempatPelaksanaan' => 'nullable|string|max:255',
            'url' => 'nullable|url|max:500',
            'quartile' => 'nullable|string|max:10',
        ]);

        // Simpan jurnal
        $jurnal = JurnalModel::create([
            'id' => ToolsHelper::generateId(),
            'judul' => $validated['judulMakalah'],
            'nama_jurnal' => '', // Sesuaikan dengan kebutuhan
            'issn' => $validated['issn'],
            'volume' => $validated['volume'],
            'nomor' => $validated['nomor'],
            'halaman' => $validated['halPaper'],
            'tahun_terbit' => now()->year,
            'url' => $validated['url'],
            'quartile' => $validated['quartile'],
            'kategori' => '', // Sesuaikan
        ]);

        // Hubungkan dengan dosen
        $jurnal->dosens()->attach($dosen->id);

        return redirect()->route('pengajuan.jurnal.daftar')
            ->with('success', 'Data jurnal berhasil diajukan!');
    }

    /**
     * Edit Jurnal - Tampilkan Form untuk Edit
     */
    public function edit($id)
    {
        $jurnal = JurnalModel::findOrFail($id);

        return Inertia::render('app/PengajuanJurnal/FormPenghargaanJurnalPage', [
            'jurnal' => [
                'id' => $jurnal->id,
                'judulMakalah' => $jurnal->judul,
                'issn' => $jurnal->issn,
                'volume' => $jurnal->volume,
                'nomor' => $jurnal->nomor,
                'halPaper' => $jurnal->halaman,
                'url' => $jurnal->url,
                'quartile' => $jurnal->quartile,
            ],
            'isEdit' => true,
        ]);
    }

    /**
     * Update Jurnal - Simpan Perubahan
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'judulMakalah' => 'required|string|max:500',
            'issn' => 'required|string|max:50',
            'volume' => 'nullable|string|max:50',
            'nomor' => 'nullable|string|max:50',
            'halPaper' => 'nullable|string|max:50',
            'url' => 'nullable|url|max:500',
            'quartile' => 'nullable|string|max:10',
        ]);

        $jurnal = JurnalModel::findOrFail($id);
        
        $jurnal->update([
            'judul' => $validated['judulMakalah'],
            'issn' => $validated['issn'],
            'volume' => $validated['volume'],
            'nomor' => $validated['nomor'],
            'halaman' => $validated['halPaper'],
            'url' => $validated['url'],
            'quartile' => $validated['quartile'],
        ]);

        return redirect()->route('pengajuan.jurnal.daftar')
            ->with('success', 'Data jurnal berhasil diupdate!');
    }

    /**
     * Delete Jurnal
     */
    public function delete($id)
    {
        $jurnal = JurnalModel::findOrFail($id);
        
        // Hapus relasi dosen
        $jurnal->dosens()->detach();
        
        // Hapus jurnal
        $jurnal->delete();

        return redirect()->route('pengajuan.jurnal.daftar')
            ->with('success', 'Data jurnal berhasil dihapus!');
    }
}