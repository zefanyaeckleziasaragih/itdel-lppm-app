<?php

namespace App\Helper;

class ConstHelper
{
    /**
     * Daftar role yang tersedia dalam sistem dalam urutan prioritas.
     *
     * Urutan ini dipakai:
     * - untuk menampilkan checkbox di halaman Hak Akses
     * - untuk mengurutkan badge Hak Akses (Admin, HRD, LPPM, dst)
     *
     * @var array<int, string>
     */
    const OPTION_ROLES = [
        'Admin',
        'HRD',
        'Ketua LPPM',
        'Anggota LPPM',
        'Dosen',
        'Todo',
    ];

    /**
     * Mengembalikan daftar role sesuai urutan prioritas di atas.
     *
     * @return array<int, string>
     */
    public static function getOptionRoles(): array
    {
        // TIDAK disort lagi, langsung pakai urutan di OPTION_ROLES
        return self::OPTION_ROLES;
    }

    /**
     * Opsi jumlah baris per halaman (misalnya untuk pagination tabel)
     *
     * @var array<int, int>
     */
    const OPTION_ROWS_PER_PAGE = [3, 5, 10, 25, 50, 100];
}
