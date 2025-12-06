<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeminarModel extends Model
{
    protected $table = 'm_seminar';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'id',
        'nama_forum',
        'website',
        'biaya'
    ];

    // ========== RELASI ==========
    
    /**
     * Relasi ke tabel p_seminar_user
     */
    public function seminarUsers()
    {
        return $this->hasMany(SeminarUserModel::class, 'seminar_id', 'id');
    }

    /**
     * Relasi ke dosen yang memiliki seminar ini
     */
    public function users()
    {
        return $this->belongsToMany(
            DosenModel::class,
            'p_seminar_user',
            'seminar_id',
            'user_id',
            'id',
            'user_id'
        );
    }

    /**
     * Relasi ke pendanaan seminar
     */
    public function pendanaan()
    {
        return $this->hasOne(PendanaanSeminarModel::class, 'seminar_id', 'id');
    }

    /**
     * Relasi ke penghargaan seminar
     */
    public function penghargaan()
    {
        return $this->hasOne(PenghargaanSeminarModel::class, 'seminar_id', 'id');
    }

    // ========== ACCESSOR ==========
    
    /**
     * Cek apakah seminar sudah diajukan untuk penghargaan
     */
    public function getIsPenghargaanAttribute()
    {
        return $this->penghargaan()->exists();
    }

    /**
     * Get status penghargaan seminar
     */
    public function getStatusPenghargaanAttribute()
    {
        $penghargaan = $this->penghargaan;
        return $penghargaan ? $penghargaan->status : 'Belum Diajukan';
    }
}