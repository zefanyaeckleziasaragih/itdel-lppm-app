<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DosenModel extends Model
{
    protected $table = 'm_dosen';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'id',
        'user_id',
        'nidn',
        'prodi_id',
        'fakultas_id',
        'sinta_id',
        'scopus_id'
    ];

    // ========== RELASI ==========
    
    /**
     * Relasi ke tabel p_jurnal_user (jurnal yang dimiliki dosen)
     */
    public function jurnalUsers()
    {
        return $this->hasMany(JurnalUserModel::class, 'user_id', 'user_id');
    }

    /**
     * Relasi ke tabel m_jurnal melalui p_jurnal_user
     */
    public function jurnals()
    {
        return $this->hasManyThrough(
            JurnalModel::class,
            JurnalUserModel::class,
            'user_id',    // FK di p_jurnal_user
            'id',         // PK di m_jurnal
            'user_id',    // PK di m_dosen
            'jurnal_id'   // FK di p_jurnal_user
        );
    }

    /**
     * Relasi ke tabel p_seminar_user (seminar yang dimiliki dosen)
     */
    public function seminarUsers()
    {
        return $this->hasMany(SeminarUserModel::class, 'user_id', 'user_id');
    }

    /**
     * Relasi ke tabel m_seminar melalui p_seminar_user
     */
    public function seminars()
    {
        return $this->hasManyThrough(
            SeminarModel::class,
            SeminarUserModel::class,
            'user_id',     // FK di p_seminar_user
            'id',          // PK di m_seminar
            'user_id',     // PK di m_dosen
            'seminar_id'   // FK di p_seminar_user
        );
    }

    /**
     * Relasi ke penghargaan jurnal
     */
    public function penghargaanJurnals()
    {
        return $this->hasMany(PenghargaanJurnalModel::class, 'jurnal_id');
    }

    /**
     * Relasi ke penghargaan seminar
     */
    public function penghargaanSeminars()
    {
        return $this->hasMany(PenghargaanSeminarModel::class, 'seminar_id');
    }
}