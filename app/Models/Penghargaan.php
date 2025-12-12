<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penghargaan extends Model
{
    // Kita pakai VIEW gabungan
    protected $table = 'v_penghargaan_semua';

    // id kamu berupa UUID → non increment
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    // VIEW biasanya tidak punya updated_at/created_at Laravel
    public $timestamps = false;

    protected $fillable = [
        'id',
        'jenis',        // 'jurnal' / 'seminar'
        'ref_id',       // jurnal_id / seminar_id
        'tanggal',      // tanggal_diajukan
        'status',       // status_pengajuan/status_hrd
        'created_at',
    ];
}
