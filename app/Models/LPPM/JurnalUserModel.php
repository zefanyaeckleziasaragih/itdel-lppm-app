<?php

namespace App\Models\LPPM;

use Illuminate\Database\Eloquent\Model;

class JurnalUserModel extends Model
{
    protected $connection = 'pgsql_lppm';
    protected $table = 'p_jurnal_user';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'user_id',
        'jurnal_id',
    ];

    public $timestamps = true;
}