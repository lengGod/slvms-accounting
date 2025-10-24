# SLVMS – Smart Loan & Value Management System

SLVMS adalah aplikasi manajemen pinjaman dan transaksi berbasis Laravel 12 yang dirancang untuk mengelola data debitur, transaksi keuangan, dan pelaporan secara efisien. Aplikasi ini dilengkapi dengan antarmuka modern, sistem otentikasi, dan fitur ekspor data ke Excel.

## 🚀 Fitur Utama

-   Manajemen Debitur dan Transaksi
-   Otentikasi dan Role-based Access Control (RBAC)
-   Ekspor data ke Excel (menggunakan `maatwebsite/excel`)
-   UI klasik berbasis Laravel UI (Bootstrap)
-   Queue listener dan log viewer (via `laravel/pail`)
-   Testing dengan PHPUnit
-   Migrasi dan seeder otomatis
-   Dukungan untuk Laravel Sail (Docker)

## 🛠️ Teknologi

-   PHP ^8.2
-   Laravel 12
-   Laravel UI (Bootstrap)
-   Laravel Tinker
-   Laravel Pail & Pint
-   Maatwebsite Excel
-   FakerPHP
-   PHPUnit

## 📦 Instalasi

```bash
git clone https://github.com/your-username/slvms.git
cd slvms
composer run setup
```
