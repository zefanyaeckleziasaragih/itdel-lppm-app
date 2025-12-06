<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrdModel extends Model
{
    protected $table = 'm_hrd';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'id',
        'user_id',
        'jabatan'
    ];
}