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

    // ========== RELASI ==========
    
    /**
     * Relasi ke tabel p_jurnal_user
     */
    public function jurnalUsers()
    {
        return $this->hasMany(JurnalUserModel::class, 'jurnal_id', 'id');
    }

    /**
     * Relasi ke dosen yang memiliki jurnal ini
     */
    public function users()
    {
        return $this->belongsToMany(
            DosenModel::class,
            'p_jurnal_user',
            'jurnal_id',
            'user_id',
            'id',
            'user_id'
        );
    }

    /**
     * Relasi ke pendanaan jurnal
     */
    public function pendanaan()
    {
        return $this->hasOne(PendanaanJurnalModel::class, 'jurnal_id', 'id');
    }

    /**
     * Relasi ke penghargaan jurnal
     */
    public function penghargaan()
    {
        return $this->hasOne(PenghargaanJurnalModel::class, 'jurnal_id', 'id');
    }

    // ========== ACCESSOR ==========
    
    /**
     * Cek apakah jurnal sudah diajukan untuk penghargaan
     */
    public function getIsPenghargaanAttribute()
    {
        return $this->penghargaan()->exists();
    }

    /**
     * Get status penghargaan jurnal
     */
    public function getStatusPenghargaanAttribute()
    {
        $penghargaan = $this->penghargaan;
        return $penghargaan ? $penghargaan->status : 'Belum Diajukan';
    }
}