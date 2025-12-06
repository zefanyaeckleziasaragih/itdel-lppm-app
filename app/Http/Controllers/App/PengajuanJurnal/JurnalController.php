<?php

namespace App\Http\Controllers\App\PengajuanJurnal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class JurnalController extends Controller
{
    /**
     * Halaman Daftar Jurnal
     */
    public function index()
    {
        // TODO: Ganti dengan data dari database
        $jurnal = [
            [
                'id' => 1,
                'judul' => 'Implementasi Machine Learning dalam Prediksi Cuaca',
                'penulis' => 'Dr. Ahmad Yani',
                'status' => 'Sudah Diverifikasi',
                'tanggal' => '2024-01-15'
            ],
            [
                'id' => 2,
                'judul' => 'Analisis Big Data untuk Sistem Rekomendasi',
                'penulis' => 'Prof. Siti Nurhaliza',
                'status' => 'Belum Diverifikasi',
                'tanggal' => '2024-02-20'
            ],
            [
                'id' => 3,
                'judul' => 'Pengembangan Aplikasi IoT untuk Smart Home',
                'penulis' => 'Dr. Budi Santoso',
                'status' => 'Sudah Diverifikasi',
                'tanggal' => '2024-03-10'
            ],
        ];

        return Inertia::render('PengajuanJurnal/DaftarJurnalPage', [
            'jurnal' => $jurnal
        ]);
    }

    /**
     * Halaman Pilih Data (Sebelum Form)
     */
    public function pilihData(Request $request)
    {
        // TODO: Ganti dengan data dari database
        $sintaList = [
            ['id' => 'S001', 'nama' => 'Dosen A - SINTA'],
            ['id' => 'S002', 'nama' => 'Dosen B - SINTA'],
            ['id' => 'S003', 'nama' => 'Dosen C - SINTA'],
        ];

        $scopusList = [
            ['id' => 'SC001', 'nama' => 'Dosen A - SCOPUS'],
            ['id' => 'SC002', 'nama' => 'Dosen B - SCOPUS'],
            ['id' => 'SC003', 'nama' => 'Dosen C - SCOPUS'],
        ];

        return Inertia::render('PengajuanJurnal/PilihDataPenghargaanPage', [
            'sintaList'  => $sintaList,
            'scopusList' => $scopusList,

            // Passing query parameters jika ada
            'sinta_id'   => $request->query('sinta_id'),
            'scopus_id'  => $request->query('scopus_id'),
            'prosiding'  => $request->query('prosiding'), // Tetap pakai 'prosiding' untuk backward compatibility
        ]);
    }

    /**
     * Halaman Form Penghargaan (Form Input)
     */
    public function form(Request $request)
    {
        return Inertia::render('PengajuanJurnal/FormPenghargaanJurnalPage', [
            'sinta_id'   => $request->query('sinta_id'),
            'scopus_id'  => $request->query('scopus_id'),
            'prosiding'  => $request->query('prosiding'), // Data jurnal yang dipilih
            'isEdit'     => false,
        ]);
    }

    /**
     * Submit Form - Simpan Data Baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sintaId' => 'nullable|string|max:255',
            'scopusId' => 'nullable|string|max:255',
            'prosiding' => 'nullable|string|max:255', // Field untuk data jurnal
            'judulMakalah' => 'required|string|max:500',
            'issn' => 'required|string|max:50',
            'volume' => 'nullable|string|max:50',
            'penulis' => 'nullable|string|max:255',
            'nomor' => 'nullable|string|max:50',
            'halPaper' => 'nullable|string|max:50',
            'tempatPelaksanaan' => 'nullable|string|max:255',
            'url' => 'nullable|url|max:500',
        ]);

        // TODO: Simpan ke database
        // Example: Jurnal::create($validated);
        
        \Log::info('Data Jurnal Baru:', $validated);

        return redirect()->route('pengajuan.jurnal.daftar')
            ->with('success', 'Data jurnal berhasil diajukan!');
    }

    /**
     * Edit Jurnal - Tampilkan Form untuk Edit
     */
    public function edit($id)
    {
        // TODO: Ambil data dari database berdasarkan ID
        // Example: $jurnal = Jurnal::findOrFail($id);
        
        $jurnal = [
            'id' => $id,
            'sintaId' => '123456',
            'scopusId' => '789012',
            'prosiding' => 'jurnal1',
            'judulMakalah' => 'Contoh Judul Makalah',
            'issn' => '1234-5678',
            'volume' => '10',
            'penulis' => 'penulis1',
            'nomor' => '2',
            'halPaper' => '10-20',
            'tempatPelaksanaan' => 'Jakarta',
            'url' => 'https://example.com',
        ];

        return Inertia::render('PengajuanJurnal/FormPenghargaanJurnalPage', [
            'jurnal' => $jurnal,
            'isEdit' => true
        ]);
    }

    /**
     * Update Jurnal - Simpan Perubahan
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'sintaId' => 'nullable|string|max:255',
            'scopusId' => 'nullable|string|max:255',
            'prosiding' => 'nullable|string|max:255',
            'judulMakalah' => 'required|string|max:500',
            'issn' => 'required|string|max:50',
            'volume' => 'nullable|string|max:50',
            'penulis' => 'nullable|string|max:255',
            'nomor' => 'nullable|string|max:50',
            'halPaper' => 'nullable|string|max:50',
            'tempatPelaksanaan' => 'nullable|string|max:255',
            'url' => 'nullable|url|max:500',
        ]);

        // TODO: Update data di database
        // Example: $jurnal = Jurnal::findOrFail($id);
        // $jurnal->update($validated);
        
        \Log::info("Update Jurnal ID $id:", $validated);

        return redirect()->route('pengajuan.jurnal.daftar')
            ->with('success', 'Data jurnal berhasil diupdate!');
    }

    /**
     * Delete Jurnal
     */
    public function delete($id)
    {
        // TODO: Hapus data dari database
        // Example: Jurnal::findOrFail($id)->delete();
        
        \Log::info("Hapus Jurnal ID: $id");

        return redirect()->route('pengajuan.jurnal.daftar')
            ->with('success', 'Data jurnal berhasil dihapus!');
    }
}