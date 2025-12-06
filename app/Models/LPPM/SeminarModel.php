<?php

namespace App\Models\LPPM;

use Illuminate\Database\Eloquent\Model;

class SeminarModel extends Model
{
    protected $connection = 'pgsql_lppm';
    protected $table = 't_seminar';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'judul_makalah',
        'nama_prosiding',
        'nama_forum',
        'penyelenggara',
        'lokasi',
        'tanggal_pelaksanaan',
        'url',
        'tingkat',
        'jenis',
    ];

    protected $casts = [
        'tanggal_pelaksanaan' => 'datetime',
    ];

    public $timestamps = true;

    // Relasi ke dosen
    public function dosens()
    {
        return $this->belongsToMany(
            DosenModel::class,
            't_seminar_user',
            'seminar_id',
            'dosen_id'
        )->withTimestamps();
    }

    // Relasi ke penghargaan
    public function penghargaan()
    {
        return $this->hasOne(PenghargaanSeminarModel::class, 'seminar_id');
    }
}