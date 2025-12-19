# ITDel Starter APP

Aplikasi starter untuk kebutuhan pengembangan internal IT Del berbasis **Laravel + Inertia + React** dengan standar kualitas kode dan testing yang terstruktur.

---

## 📌 Logs
### [17-12-2025] 
- [Glen Rejeki Sitorus] Rapikan PengajuanController: hapus duplikasi field/select, pakai fromSub(union) untuk sorting union, konsisten label jenis & indeks.
- [Glen Rejeki Sitorus] Tambah/rapikan flow pengajuan jurnal dosen (daftar/pilih/form/store) dan pastikan insert tanpa kolom status (pakai status_pengajuan = pengajuan_baru).
- [Glen Rejeki Sitorus] Fix query jurnal agar pakai pivot p_jurnal_user (hindari error kolom j.user_id).
- [Glen Rejeki Sitorus] Perbaiki HakAksesPage.jsx: default props aman, handle user null, filter & render lebih robust.

### [10-12-2025] 
- [Glen Rejeki Sitorus] Memperbaiki perhitungan approval rate berdasarkan status_hrd.
- [Glen Rejeki Sitorus] Memperbaiki total pengajuan (jurnal & seminar) berdasarkan status_pengajuan = disetujui.
- [Glen Rejeki Sitorus] Memperbaiki rekap jenis bulan ini (jurnal/seminar).
- [Glen Rejeki Sitorus] Memperbaiki total penghargaan yang sudah dicairkan dari DB.
- [Glen Rejeki Sitorus] Menambahkan dummy sisaDana = 9.750.000 tanpa mengganggu data lain.
- [Glen Rejeki Sitorus] Mengatur ulang anggaran agar mengikuti totalDanaApprove + sisaDana.
- [Glen Rejeki Sitorus] Membersihkan dan merapikan controller DashboardHrdController.



### [10-12-2025] 
- [Glen Rejeki Sitorus] Memperbaiki controler HRD detail
- [Glen Rejeki Sitorus] Memperbaiki tampilan HRD detail dimana tidak statis melainkan dinamis 
- [Glen Rejeki Sitorus] Memperbaiki web.php supaya dari tampilan detail ke daftar approve 
- [Glen Rejeki Sitorus] Menggabungkan pekerjaan Zefanya pada fitur dosen dengan modul yang saya kerjakan (Dashboard LPPM, detail LPPM, detail HRD) dan menyelesaikan conflict controller + routing.


### [8-12-2025] 
- [Glen Rejeki Sitorus] Mengganti seluruh dummy data statistik dengan query nyata dari tabel penghargaan jurnal & seminar.
- [Glen Rejeki Sitorus] Menambahkan perhitungan Total Penghargaan Bulan Ini, Total Dana Approve, dan Sisa Dana berdasarkan data DB.
- [Glen Rejeki Sitorus]Menyelaraskan grafik (line chart) agar menggunakan data jurnal & seminar yang diperbarui.
- [Glen Rejeki Sitorus]Membersihkan kode lama yang tidak relevan dan menyesuaikan struktur response Inertia.


### [1-12-2025] 
- [Glen Rejeki Sitorus] Memeperbaiki tampilan sidebar untuk dosen 
- [Glen Rejeki Sitorus] Memeperbaiki tampilan sidebar untuk Admin 

### [28-11-2025] 
- [Glen Rejeki Sitorus] Memperbaiki tampilan sidebar untuk menu LPPM dan HRD
- [Glen Rejeki Sitorus] Memperbaiki masalah dashboard HRD yang tidak muncul setelah update sebelumnya
- [Glen Rejeki Sitorus] Menyempurnakan tampilan dashboard LPPM
- [Glen Rejeki Sitorus] Memperbarui deskripsi pada README sesuai perubahan terbaru

### [27-11-2025]
- [Glen Rejeki Sitorus] memperbaiki code dimana tampilan dashboard hrd dari yosep kemarin tidak muncul dan memperbaiki tampilan dashboard lppm
  
  
### [26-11-2025]
- [Glen Rejeki Sitorus] add LPPM and HRD as allowed editors in hak akses



### [25-11-2025]
- [Glen Rejeki Sitorus] Menggabungkan tampilan HRD, pengajuan penghargaan seminar (Zefanya), dan pengajuan penghargaan jurnal (Lofelyn)
- [Glen Rejeki Sitorus] Sinkronisasi route, sidebar, controller, dan konfigurasi Ziggy
- [Glen Rejeki Sitorus] Penyelesaian konflik pada beberapa file utama


### [24-11-2025]
- [Glen Rejeki Sitorus] Membuat tampilan statistik/beranda dan daftar dosen yang mengajukan penghargaan seminar dan dosen

### [21-11-2025]
- [Lofelyn Enzely Ambarita] Menambahkan tampilan pengajuan penghargaan jurnal

## Quality Checks

### Composer Audit

Composer Audit adalah perintah Composer yang digunakan untuk memeriksa keamanan paket PHP yang terinstal di proyek.

```bash
composer audit
```

### NPM Audit

NPM Audit adalah perintah NPM yang digunakan untuk memeriksa keamanan paket NodeJS yang terinstal di proyek.

```bash
npm audit --audit-level=moderate
```

### Laravel Pint

Laravel pint untuk memeriksa dan memperbaiki format kode PHP secara otomatis.

#### Install Laravel Pint

```bash
composer require laravel/pint --dev
```

#### Melakukan perbaikan format kode PHP

```bash
vendor/bin/pint
```

### Eslint

ESLint adalah tools analisis kode statis untuk JavaScript dan TypeScript yang berfungsi mendeteksi error, menjaga konsistensi style, dan menerapkan best practices dalam penulisan kode

```bash
npx eslint . --format table
```

#### Melakukan pengecekan format kode PHP

```bash
vendor/bin/pint --test
```

### Larastan

Larastan adalah static code analysis tool yang menganalisis kode PHP Laravel tanpa harus menjalankannya.

#### Membuatuhkan file: phpstan.neon

```bash
includes:
    - ./vendor/larastan/larastan/extension.neon

parameters:
    level: 8 # Level 1 - 8 (unstrict - strict)
    paths:
        - app
        - routes
```

#### Install Larastan

```bash
composer require --dev larastan/larastan
```

#### Melakukan Analisis dengan Larastan

```bash
vendor/bin/phpstan analyse
```

### Testing Coverage

Testing Coverage untuk melakukan pengujian yang mencakup semua kemungkinan pada kode program yang dibuat guna mengurangi kemungkinan bug yang akan tejadi pada saat sudah di production.

#### Melakukan Pengujian dengan Coverage

```bash
php artisan test --coverage
```

### Melakukan Pengujian Spesifik

```bash
# php artisan test PATH_FILE_TEST
php artisan test tests/Feature/Livewire/Auth/LoginLivewireTest.php

php artisan test tests/Unit/Middleware/HandleInertiaRequestsTest.php

php artisan test tests/Feature/Controllers/Home/HomeControllerTest.php

php artisan test tests/Feature/Controllers/Auth/AuthControllerTest.php

php artisan test tests/Feature/Controllers/HakAkses/HakAksesControllerTest.php
```

## Catatan Syntax

```bash
# 🎯 TYPE DEFINITIONS (TypeScript Support)
npm i --save-dev @types/react

# 🎨 UI COMPONENTS (shadcn/ui)
npx shadcn@latest add checkbox
npx shadcn@latest add tabs

# 🔗 LARAVEL ZIGGY (Route Helper)
php artisan ziggy:generate resources/js/ziggy.js

# 🔧 ESLINT & CODE QUALITY

# Core ESLint dan plugins
npm install -D eslint-plugin-react eslint-plugin-react-hooks eslint-plugin-unused-imports @eslint/js globals

# Babel parser dan preset
npm install -D @babel/eslint-parser
npm install -D @babel/preset-react

# Formatter dan additional plugins
npm install -D eslint-formatter-table
npm install eslint-plugin-react-hooks --save-dev
npm install --save-dev @eslint/js eslint-plugin-react eslint-plugin-react-hooks
```

## Template Tests

```php
<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ToolsHelperTest extends TestCase
{
    #[Test]
    public function contoh_format_pengujian()
    {
        // -------------------------------------
        // Arrange (Persiapan)
        // -------------------------------------
        $nilai1 = 10;
        $nilai2 = 20;
        $expected = 30;

        // -------------------------------------
        // Act (Aksi)
        // -------------------------------------
        $hasil = $nilai1 + $nilai2;

        // -------------------------------------
        // Assert (Verifikasi)
        // -------------------------------------
        $this->assertEquals($expected, $hasil);
    }
}
```