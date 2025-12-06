<?php

namespace App\Models\LPPM;

use Illuminate\Database\Eloquent\Model;

class JurnalModel extends Model
{
    protected $connection = 'pgsql_lppm';
    protected $table = 't_jurnal';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'judul',
        'nama_jurnal',
        'issn',
        'volume',
        'nomor',
        'halaman',
        'tahun_terbit',
        'url',
        'quartile',
        'kategori',
    ];

    protected $casts = [
        'tahun_terbit' => 'integer',
    ];

    public $timestamps = true;

    // Relasi ke dosen
    public function dosens()
    {
        return $this->belongsToMany(
            DosenModel::class,
            't_jurnal_user',
            'jurnal_id',
            'dosen_id'
        )->withTimestamps();
    }

    // Relasi ke penghargaan
    public function penghargaan()
    {
        return $this->hasOne(PenghargaanJurnalModel::class, 'jurnal_id');
    }
}