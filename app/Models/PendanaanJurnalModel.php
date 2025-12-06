<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendanaanJurnalModel extends Model
{
    protected $table = 't_pendanaan_jurnal';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'id',
        'jurnal_id',
        'nominal',
        'website',
        'mssn_pendukung',
        'biaya_publikasi',
        'luaran'
    ];
}