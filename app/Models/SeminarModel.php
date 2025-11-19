<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeminarModel extends Model
{
    protected $table = 'm_seminar';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'user_id',
        'sinta_id',
        'scopus_id',
        'prosiding',
        'nama_forum',
        'penulis',
        'institusi_penyelenggara',
        'waktu_pelaksanaan',
        'tempat_pelaksanaan',
        'url',
        'status',
    ];

    protected $casts = [
        'waktu_pelaksanaan' => 'datetime',
    ];

    public $timestamps = true;
}