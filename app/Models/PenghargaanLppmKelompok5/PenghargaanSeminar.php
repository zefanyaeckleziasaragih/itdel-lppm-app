<?php

namespace App\Models\PenghargaanLppmKelompok5;

use Illuminate\Database\Eloquent\Model;

class PenghargaanSeminar extends Model
{
    // Nama tabel di PostgreSQL
    protected $table = 't_penghargaan_seminar';

    // Primary key UUID
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    // Sesuaikan dengan kolom created_at & updated_at
    public $timestamps = true;

    protected $fillable = [
        'id',
        'seminar_id',
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
