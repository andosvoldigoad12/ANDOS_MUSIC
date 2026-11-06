# AM (AndosMusic) 🎵
> **"Your Music, Your World."**  
> Modern Web-based Music Streaming & Music Sharing Platform built with **Laravel**, **MySQL**, and **HTML5 Audio Engine**.

---

## 🌟 Tentang AndosMusic
**AM (AndosMusic)** adalah platform streaming musik modern dan berbagi musik berbasis website. Dirancang dengan estetika visual **Cyan Dark Mode** (default) dan **Cyan Light Mode**, AndosMusic memberikan pengalaman mendengarkan musik berkualitas tinggi, pengelolaan playlist personal, fitur sosial (komentar, favorit, laporan masalah), serta studio bagi kreator musik untuk mengunggah karya orisinal mereka dengan sistem moderasi terpadu.

---

## 🚀 Fitur Unggulan

### 1. Hierarki Tiga Role Pengguna (RBAC)
- 👑 **Super Admin (Kekuasaan Tertinggi)**:
  - Dashboard statistik analitik sistem lengkap (pertumbuhan pengguna & tren pemutaran harian dengan Chart.js).
  - Manajemen Admin: Tambah, edit, dan aktifkan/nonaktifkan akun Admin.
  - Manajemen Super Admin: Tambah dan kelola akun Super Admin dengan **proteksi anti-lockout** (tidak bisa menonaktifkan diri sendiri atau akun Super Admin aktif terakhir).
  - Manajemen katalog seluruh musik: Upload musik (auto-approved), edit, dan hapus.
  - Moderasi upload pengguna (setujui / tolak dengan alasan tercatat).
  - Audit jejak aktivitas lengkap (**Activity Logs**) dengan pencatatan IP & User-Agent.
  - Pengaturan informasi website (Nama Brand, Tagline, Email Kontak, Pengumuman Global).
- 🛡️ **Admin (Content Moderator & Curator)**:
  - Dashboard statistik moderasi.
  - Verifikasi dan persetujuan unggahan musik (Approve / Reject dengan alasan).
  - Kelola seluruh musik dan upload musik baru langsung publikasi.
  - Kelola pengguna biasa (aktifkan / nonaktifkan akun pengguna).
  - Tangani laporan masalah lagu (Hak cipta, audio rusak, konten tidak pantas).
  - Moderasi & hapus komentar yang melanggar aturan.
  - Akses statistik pemutaran dan distribusi genre musik.
- 🎧 **User (Pengguna Reguler & Musisi Kreator)**:
  - Registrasi publik otomatis mendapatkan role `user` (dilindungi dari manipulasi request).
  - Streaming audio lancar dengan pemutar HTML5 Audio interaktif.
  - Jelajahi musik dengan filter genre, pencarian dinamis (judul, artist, album), dan sorting.
  - Tangga lagu **Trending Music** (Top 10 terpopuler: hari ini, minggu ini, bulan ini, sepanjang waktu).
  - Buat, edit, dan hapus **Playlist** kustom serta putar seluruh lagu playlist (**Play All**).
  - Tambah lagu ke **Favorit** (like / unlike) dan riwayat **Recently Played** otomatis.
  - **Studio Unggah Musik**: Unggah lagu (MP3, WAV, OGG) dengan cover art. Lagu tersimpan dengan status `Pending` hingga disetujui moderator.
  - Halaman **Upload Saya**: Cek status moderasi (Disetujui, Menunggu, Ditolak dengan alasan lengkap).
  - Profil personal, upload avatar, dan ganti password.

### 2. Modern HTML5 Audio Player Engine
- Pemutar audio sticky persisten di bagian bawah layar.
- Kontrol penuh: Play, Pause, Next, Previous, Shuffle (Acak), Repeat (Ulangi).
- Progress bar interaktif dengan fitur seeking (lompat ke detik lagu).
- Pengatur volume presisi dengan tombol Mute instan (tersimpan di `localStorage`).
- Penghitung durasi real-time dan pencatatan riwayat pemutaran otomatis tanpa duplikasi berlebih.

### 3. Tema Desain Cyan Dark & Light Mode
- **Dark Mode (Default)**:
  - Background: `#0B1418`
  - Sidebar: `#101E24`
  - Primary Cyan: `#00D9E8`
  - Secondary Cyan: `#00AFC1`
  - Card: `#16272E`
  - Text Primary: `#FFFFFF`
  - Text Secondary: `#A0B4BA`
- **Light Mode**: Nuansa elegan dengan tetap mempertahankan identitas Cyan AndosMusic.
- Dilengkapi animasi hover halus, transisi mulus, toast alert modern, dan modal interaktif.

---

## 🛠️ Persyaratan Sistem
- **Sistem Operasi**: Windows 10/11 (atau macOS / Linux)
- **Local Server**: XAMPP (Apache & MySQL)
- **PHP**: Versi 8.2 atau lebih baru (ekstensi `pdo_mysql`, `fileinfo`, `mbstring`, `openssl`, `curl`)
- **Composer**: Versi 2.x
- **Web Browser**: Chrome, Edge, Firefox, atau Safari modern

---

## 📦 Panduan Instalasi Langkah Demi Langkah

### 1. Menjalankan XAMPP
1. Buka aplikasi **XAMPP Control Panel**.
2. Klik tombol **Start** pada modul **Apache** dan **MySQL**.
3. Pastikan kedua modul berwarna hijau dengan port default:
   - Apache: `80`, `443`
   - MySQL: `3306`

### 2. Membuat Database MySQL
1. Buka browser dan akses phpMyAdmin di `http://localhost/phpmyadmin`
2. Buat database baru bernama:
   ```sql
   CREATE DATABASE db_andosmusic CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
   *(Atau database akan dibuat otomatis jika menjalankan script setup).*

### 3. Mengatur Konfigurasi File `.env`
Buka file `.env` di direktori utama `c:\xampp\htdocs\ANDOS_MUSIC` dan pastikan konfigurasi database sesuai dengan pengaturan XAMPP Anda:
```env
APP_NAME="AM (AndosMusic)"
APP_ENV=local
APP_KEY=base64:EzuhoiOZWIVyElKrBOMOoxGM+yCrbzBzhj+HFoSQcTU=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_andosmusic
DB_USERNAME=root
DB_PASSWORD=

FILESYSTEM_DISK=public
```

> **Catatan Keamanan Kredensial Super Admin:**  
> Password Super Admin awal dapat dikonfigurasi secara aman melalui variabel lingkungan di file `.env`:
> ```env
> SUPER_ADMIN_PASSWORD=KombinasiPasswordAman123!
> ```
> Jika tidak diisi, seeder akan menggunakan password default bawaan instalasi lokal: `Admin123!`.

### 4. Menjalankan Migration Database
Buka terminal / PowerShell di direktori `c:\xampp\htdocs\ANDOS_MUSIC` dan jalankan:
```bash
php artisan migrate:fresh
```
Perintah ini akan membuat 10 tabel inti:
1. `users`
2. `music`
3. `playlists`
4. `playlist_music`
5. `favorites`
6. `play_histories`
7. `comments`
8. `music_reports`
9. `notifications`
10. `activity_logs`
11. `website_settings`

### 5. Menjalankan Database Seeder
Jalankan seeder untuk mengisi akun awal, sampel musik yang dapat langsung diputar, playlist, komentar, dan pengaturan platform:
```bash
php artisan db:seed
```

### 6. Menghubungkan Storage (Symbolic Link / Junction)
Agar cover album dan berkas audio yang diunggah dapat diakses oleh browser melalui `/storage/...`, pastikan tautan penyimpanan publik telah dibuat:
- Di Windows / XAMPP:
  ```bash
  cmd /c "mklink /J public\storage storage\app\public"
  ```
  *(Atau melalui artisan: `php artisan storage:link`).*

### 7. Menjalankan Aplikasi Laravel
Jalankan server pengembangan Laravel:
```bash
php artisan serve
```
Akses aplikasi melalui browser di:
👉 **`http://127.0.0.1:8000`** atau **`http://localhost:8000`**

*(Jika menggunakan virtual host Apache XAMPP langsung, aplikasi juga dapat dibuka di `http://localhost/ANDOS_MUSIC/public`).*

---

## 🔑 Akun Bawaan untuk Pengujian (Demo Accounts)

| Role | Nama Akun | Alamat Email | Password |
|---|---|---|---|
| **Super Admin** | Super Administrator | `superadmin@andosmusic.com` | `Admin123!` |
| **Admin** | Content Moderator Admin | `admin@andosmusic.com` | `Admin123!` |
| **User (Musisi 1)** | Dimas Andos | `user@andosmusic.com` | `User123!` |
| **User (Musisi 2)** | Luna Melody | `artistan@andosmusic.com` | `User123!` |

---

## 🧭 Panduan Login & Hak Akses Berdasarkan Role

1. **Login sebagai Super Admin**:
   - Masuk di `/login` dengan email `superadmin@andosmusic.com`.
   - Otomatis diarahkan ke **`/superadmin/dashboard`**.
   - Akses penuh menu di sidebar: Kelola Staf Admin, Kelola Super Admin, Moderasi Musik, Activity Logs, Pengaturan Website, dan Analytics.

2. **Login sebagai Admin**:
   - Masuk di `/login` dengan email `admin@andosmusic.com`.
   - Otomatis diarahkan ke **`/admin/dashboard`**.
   - Dapat memeriksa musik pending di menu **Persetujuan Upload**, menyetujui, atau menolak dengan catatan alasan.
   - Dilindungi agar tidak dapat mengakses dashboard atau menu Super Admin (otomatis dicegah dengan status `403 Forbidden`).

3. **Login sebagai User**:
   - Masuk di `/login` dengan email `user@andosmusic.com` atau daftarkan akun baru di `/register`.
   - Diarahkan ke **`/user/home`**.
   - Dapat mendengarkan seluruh lagu approved, membuat playlist pribadi di **My Playlists**, menyukai lagu (**Favorites**), melihat **Recently Played**, serta mengunggah karya musik di **Upload Musik**.
   - Dilindungi agar tidak dapat mengakses area Admin maupun Super Admin.

---

## 🧪 Menjalankan Pengujian Otomatis (Automated Testing)
Seluruh modul dan proteksi keamanan telah diuji menggunakan **PHPUnit**:
```bash
php vendor\bin\phpunit
```
Hasil pengujian:
- ✅ **14 Tests, 73 Assertions, 0 Failures, 0 Errors.**
- Mencakup: Akses halaman publik, validasi pendaftaran user, proteksi akun nonaktif, kontrol RBAC 3 role, alur upload & moderasi musik (Approve/Reject), penambahan play count & riwayat, relasi favorit & playlist, moderasi komentar & laporan, serta perlindungan Super Admin anti-lockout.

---

## 🛠️ Penyelesaian Masalah Umum (Troubleshooting)

1. **Error: `Base table or view not found` atau `Migration table not found`**
   - Pastikan MySQL di XAMPP aktif (berwarna hijau).
   - Pastikan database `db_andosmusic` sudah dibuat.
   - Jalankan `php artisan migrate:fresh --seed`.

2. **File audio atau cover album tidak muncul (404 Not Found)**
   - Periksa apakah folder `public/storage` sudah terhubung ke `storage/app/public`.
   - Jalankan perintah junction Windows:
     ```cmd
     cmd /c "mklink /J public\storage storage\app\public"
     ```

3. **Audio tidak berputar di browser**
   - Format audio yang didukung adalah MP3, WAV, dan OGG.
   - Pastikan lagu berstatus **Approved**. Lagu berstatus Pending atau Rejected tidak dapat diputar di halaman publik.
   - Beberapa browser memblokir auto-play tanpa interaksi klik dari pengguna; klik tombol play pada player bar.

4. **Error: `Akun Anda dinonaktifkan oleh administrator` saat login**
   - Akun tersebut memiliki status `inactive`.
   - Hubungi Administrator atau masuk menggunakan akun Super Admin untuk mengaktifkan kembali akun tersebut melalui menu **Kelola Pengguna**.

---

## 📄 Lisensi
Hak Cipta © 2026 **AM (AndosMusic)**. Seluruh hak cipta dilindungi undang-undang.
