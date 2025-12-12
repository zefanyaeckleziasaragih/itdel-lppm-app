<?php

namespace App\Models\PenghargaanLppmKelompok5;

use Illuminate\Database\Eloquent\Model;

class PenghargaanJurnal extends Model
{
    // Nama tabel di PostgreSQL
    protected $table = 't_penghargaan_jurnal';

    // Primary key UUID
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    // Kalau tabel punya kolom created_at & updated_at, biarkan true.
    // Kalau tidak ada, ganti ke false.
    public $timestamps = true;

    // Kolom yang boleh diisi mass-assignment
    protected $fillable = [
        'id',
        'jurnal_id',
        'tanggal_diajukan',
        'status_pengajuan',
        'nominal_usulan',
        'nominal_disetujui',
        'status_lppm',
        'status_hrd',
        'tgl_pengajuan_penghargaan',
        'created_at',
        'updated_at',
    ];
}
