<?php

namespace App\Models\LPPM;

use Illuminate\Database\Eloquent\Model;

class PenghargaanSeminarModel extends Model
{
    protected $connection = 'pgsql_lppm';
    protected $table = 't_penghargaan_seminar';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'seminar_id',
        'tanggal_diajukan',
        'status_pengajuan',
        'nominal_usulan',
        'nominal_disetujui',
        'status',
        'tgl_pengajuan_penghargaan',
        'tgl_verifikasi_lppm',
        'tgl_approve_hrd',
        'tgl_cair',
    ];

    protected $casts = [
        'tanggal_diajukan' => 'datetime',
        'tgl_pengajuan_penghargaan' => 'datetime',
        'tgl_verifikasi_lppm' => 'datetime',
        'tgl_approve_hrd' => 'datetime',
        'tgl_cair' => 'datetime',
        'nominal_usulan' => 'integer',
        'nominal_disetujui' => 'integer',
    ];

    public $timestamps = true;

    // Relasi ke seminar
    public function seminar()
    {
        return $this->belongsTo(SeminarModel::class, 'seminar_id');
    }
}