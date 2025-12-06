<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenghargaanJurnalModel extends Model
{
    protected $table = 't_penghargaan_jurnal';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'id',
        'jurnal_id',
        'tanggal_diajukan',
        'status_pengajuan',
        'nominal_usulan',
        'nominal_disetujui',
        'status',
        'tgl_pengajuan_penghargaan',
        'tgl_verifikasi_lppm',
        'tgl_approve_hrd',
        'tgl_cair'
    ];
}