<?php

namespace App\Http\Controllers\App\PenghargaanJurnal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PenghargaanJurnalController extends Controller
{
    /**
     * Halaman daftar penghargaan jurnal
     */
    public function index()
    {
        return inertia("PenghargaanJurnal/DaftarPenghargaanJurnalPage");
    }

    /**
     * Halaman tambah penghargaan jurnal
     */
    public function create()
    {
        return inertia("PenghargaanJurnal/TambahPenghargaanJurnalPage");
    }

    /**
     * Menyimpan data penghargaan jurnal
     */
    public function store(Request $request)
    {
        $request->validate([
            "judul" => "required",
            "kategori" => "required",
            "penerbit" => "required",
            "tahun" => "required|numeric",
        ]);

        // Simulasi penyimpanan
        // Nanti kamu ganti dengan model
        return redirect()->route("penghargaan.jurnal.daftar")
            ->with("success", "Penghargaan jurnal berhasil ditambahkan!");
    }

    /**
     * Halaman edit penghargaan jurnal
     */
    public function edit($id)
    {
        return inertia("PenghargaanJurnal/EditPenghargaanJurnalPage", [
            "id" => $id,
        ]);
    }

    /**
     * Update data
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            "judul" => "required",
            "kategori" => "required",
            "penerbit" => "required",
            "tahun" => "required|numeric",
        ]);

        // Simulasi update

        return redirect()->route("penghargaan.jurnal.daftar")
            ->with("success", "Penghargaan jurnal berhasil diperbarui!");
    }

    /**
     * Hapus data
     */
    public function destroy($id)
    {
        // Simulasi hapus

        return redirect()->back()->with("success", "Data berhasil dihapus!");
    }
}
