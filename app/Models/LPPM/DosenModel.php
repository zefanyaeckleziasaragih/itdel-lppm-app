<?php

namespace App\Models\LPPM;

use Illuminate\Database\Eloquent\Model;

class DosenModel extends Model
{
    protected $connection = 'pgsql_lppm';
    protected $table = 'm_dosen';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'user_id',
        'nidn',
        'prodi_id',
        'fakultas_id',
        'sinta_id',
        'scopus_id',
    ];

    public $timestamps = true;

    // Relasi ke jurnal
    public function jurnals()
    {
        return $this->belongsToMany(
            JurnalModel::class,
            't_jurnal_user',
            'dosen_id',
            'jurnal_id'
        )->withTimestamps();
    }

    // Relasi ke seminar
    public function seminars()
    {
        return $this->belongsToMany(
            SeminarModel::class,
            't_seminar_user',
            'dosen_id',
            'seminar_id'
        )->withTimestamps();
    }
}