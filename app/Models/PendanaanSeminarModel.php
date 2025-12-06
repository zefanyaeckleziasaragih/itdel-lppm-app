<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendanaanSeminarModel extends Model
{
    protected $table = 't_pendanaan_seminar';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'id',
        'seminar_id',
        'nominal',
        'status'
    ];
}