<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JurnalModel extends Model
{
    protected $table = 'm_jurnal';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'id',
        'issn',
        'judul_paper',
        'nama_jurnal',
        'volume',
        'nomor',
        'jumlah_halaman',
        'url'
    ];
}