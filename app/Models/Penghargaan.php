<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penghargaan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'tanggal',
        'status',
    ];

    // Menambahkan fungsi untuk mendapatkan total dana approve atau sisa dana jika diperlukan
    public function getTotalDanaApprove()
    {
        return $this->where('status', 'approved')->sum('dana');
    }

    public function getSisaDana()
    {
        return $this->anggaran - $this->getTotalDanaApprove();
    }
}
