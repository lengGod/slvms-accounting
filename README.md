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

## Prerequisites

Sebelum memulai, pastikan Anda telah menginstal perangkat lunak berikut di komputer Anda:

*   **Web Server Environment:** Anda bisa menggunakan [Laragon](https://laragon.org/download/), [XAMPP](https://www.apachefriends.org/index.html), atau [WAMP](https://www.wampserver.com/en/). Ini akan menyediakan Apache/Nginx, MySQL/MariaDB, dan PHP.
*   **PHP:** Pastikan versi PHP Anda `^8.2` atau lebih tinggi.
*   **Composer:** Dependency manager untuk PHP. Anda bisa mengunduhnya [di sini](https://getcomposer.org/).
*   **Node.js & npm:** JavaScript runtime dan package manager. Anda bisa mengunduhnya [di sini](https://nodejs.org/).
*   **Git:** Sistem kontrol versi untuk meng-clone repositori. Anda bisa mengunduhnya [di sini](https://git-scm.com/).

## 📦 Instalasi & Menjalankan Proyek

1.  **Clone Repositori:**
    ```bash
    git clone https://github.com/lengGod/slvms-accounting.git
    cd slvms-accounting
    ```

2.  **Install Dependensi PHP:**
    ```bash
    composer install
    ```

3.  **Install Dependensi JavaScript:**
    ```bash
    npm install
    ```

4.  **Konfigurasi Environment:**
    Salin file `.env.example` menjadi `.env` dan sesuaikan isinya, terutama untuk koneksi database.
    ```bash
    cp .env.example .env
    ```
    Kemudian, generate kunci aplikasi:
    ```bash
    php artisan key:generate
    ```

5.  **Migrasi Database:**
    Jalankan migrasi untuk membuat tabel-tabel yang dibutuhkan. Jika Anda ingin menjalankan seeder juga, tambahkan flag `--seed`.
    ```bash
    php artisan migrate
    ```

6.  **Menjalankan Aplikasi:**
    Jalankan server pengembangan bawaan Laravel dan kompilasi aset dengan `npm run build`.

    ```bash
    # Kompilasi aset (jalankan setiap ada perubahan pada file CSS/JS)
    npm run build
    ```

    ```bash
    # Jalankan server
    php artisan serve
    ```

### Mengakses Aplikasi dari Jaringan Lokal (Multiuser)

Jika Anda ingin mengakses aplikasi dari perangkat lain di jaringan lokal yang sama (misalnya, untuk pengujian di perangkat seluler atau kolaborasi):

1.  **Temukan Alamat IP Lokal Anda:**
    *   **Windows:** Buka Command Prompt dan ketik `ipconfig`. Cari alamat IPv4 Anda (biasanya dimulai dengan `192.168.x.x` atau `10.x.x.x`).
    *   **macOS/Linux:** Buka Terminal dan ketik `ifconfig` atau `ip addr`. Cari alamat `inet` Anda.

2.  **Jalankan Server dengan Host IP Lokal:**
    Ganti `<YOUR_LOCAL_IP>` dengan alamat IP yang Anda temukan.
    ```bash
    php artisan serve --host=<YOUR_LOCAL_IP>
    ```

3.  **Akses dari Perangkat Lain:**
    Dari perangkat lain di jaringan yang sama, buka browser dan navigasikan ke `http://<YOUR_LOCAL_IP>:8000`.

    **Catatan Penting:** Pastikan firewall di komputer Anda mengizinkan koneksi masuk ke port `8000`.

Aplikasi Anda sekarang dapat diakses di `http://localhost:8000` atau `http://<YOUR_LOCAL_IP>:8000`.
