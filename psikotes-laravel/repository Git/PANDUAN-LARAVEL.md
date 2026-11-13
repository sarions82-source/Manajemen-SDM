# Psikotes msdm: project Laravel + SQLite (berjalan di komputer sendiri)

Project ini menjalankan seluruh aplikasi (DISC, PAPI Kostick, Kraepelin, login Super Admin/Admin/Employee, Register dan persetujuan, bagikan tes ke kandidat, laporan gabungan, log aktivitas, tombol menu dengan logo msdm) **langsung di komputer Anda**. Semua data tersimpan di **satu file SQLite**: `database/database.sqlite`. Tidak perlu hosting atau MySQL.

## Yang dibutuhkan
- **PHP 8.2 atau lebih baru** dengan ekstensi `pdo_sqlite`, `sqlite3`, `mbstring`, `openssl`, `fileinfo`, `xml`/`dom`, `ctype`, `tokenizer`.
- **Composer** (pengelola paket PHP).
- Internet **hanya saat `composer install` pertama kali**.

### Memasang PHP dan Composer
- **Windows (paling mudah): Laragon.** Unduh **Laragon Full** dari https://laragon.org dan pasang. Paket ini sudah memuat PHP dan Composer. Setelah terpasang, buka **Laragon > Menu > Terminal**, lalu masuk ke folder project ini. (Alternatif: XAMPP dengan PHP 8.2+ dan Composer dari https://getcomposer.org.)
- **Mac:** pasang Homebrew, lalu `brew install php composer`.
- **Ubuntu/Debian:** `sudo apt install php-cli php-sqlite3 php-mbstring php-xml php-curl unzip composer`.
Cek dengan `php -v` (harus 8.2 atau lebih baru) dan `composer -V`.

## Cara termudah: klik dua kali
- **Windows:** klik dua kali **`mulai.bat`**. Untuk bisa dibuka dari HP atau komputer lain di jaringan yang sama, klik dua kali **`mulai-jaringan.bat`**.
- **Mac:** klik dua kali **`mulai.command`** (jika diblokir: klik kanan > Open). **Linux:** `./mulai.sh` (tambahkan `--lan` untuk akses jaringan).

Saat pertama kali, file ini menjalankan `composer install` (beberapa menit), membuat file `.env`, lalu membuka **http://localhost:8000**. Tutup jendela hitam (terminal) untuk menghentikan server.

## Menjalankan manual
```
composer install --no-dev --optimize-autoloader
cp .env.example .env          (Windows: copy .env.example .env)
php artisan serve
```
Buka **http://localhost:8000**. Karena belum ada akun, tampil **Daftar Super Admin (pertama kali)** dengan password yang sudah terisi otomatis. Klik **Register**, salin atau catat data login di layar berikutnya, lalu **Lanjut ke Menu utama**.

## Uji mandiri (disarankan dijalankan sekali)
```
php artisan psikotes:selftest
```
Perintah ini menguji logika akun, peran, Register dan persetujuan, hasil tes, durasi, dan log pada database sementara di memori (data Anda tidak tersentuh). Hasil yang benar: **`SEMUA LULUS (27 pemeriksaan)`**. Jika ada baris **[GAGAL]**, kirimkan hasilnya ke pembuat aplikasi.

## Dibuka dari HP atau komputer lain (satu Wi-Fi/jaringan)
1. Jalankan lewat `mulai-jaringan.bat` / `./mulai.sh --lan` (atau `php artisan serve --host=0.0.0.0`).
2. Izinkan **port 8000** pada firewall komputer jika diminta.
3. Masuk sebagai Admin/Super Admin, buka **Menu utama > Bagikan tes ke kandidat**. Di kolom **Alamat situs** muncul tombol alamat jaringan komputer ini (misalnya `http://192.168.1.10:8000`). Klik alamat itu, centang tes yang akan dibagikan, lalu salin link atau kirim lewat WhatsApp.
4. Komputer harus tetap menyala dan server berjalan selama tes berlangsung. **Disarankan** mengisi `SETUP_KEY` di `.env` sebelum membuka ke jaringan.

> Server bawaan `php artisan serve` cocok untuk kelompok kecil. Di Windows ia melayani satu permintaan pada satu waktu (cukup untuk puluhan kandidat bergantian); di Mac/Linux peluncur memakai beberapa proses. Untuk pemakaian lebih besar, arahkan Apache/Nginx ke folder `public`.

## Pengaturan (file `.env`)
- `SETUP_KEY`: kunci pemasangan (opsional). Jika diisi, wajib dimasukkan saat mendaftarkan Super Admin pertama.
- `DB_DATABASE`: lokasi file database (bawaan `database/database.sqlite`).
- Ganti port: `php artisan serve --port=8080`.
Restart server setelah mengubah `.env`.

## Login, peran, dan fitur
- **Layar login:** pilih tombol peran (Super Admin, Admin, atau Employee), isi nama pengguna dan password, lalu **Login**. Tombol **Register** untuk pendaftaran mandiri; akun baru **menunggu persetujuan** Super Admin (di **Kelola pengguna**).
- **Tombol msdm** (kapsul berlogo di pojok kiri atas) membuka menu: Menu utama, Hasil dan laporan, Bagikan tes ke kandidat, Atur durasi, Kelola pengguna, Log aktivitas, Keluar.

| Kemampuan | Super Admin | Admin | Employee |
|---|:-:|:-:|:-:|
| Lihat hasil dan laporan gabungan | Ya | Ya | Ya |
| Cetak dan unduh laporan | Ya | Ya | Ya |
| Ekspor CSV | Ya | Ya | Tidak |
| Bagikan tes ke kandidat | Ya | Ya | Tidak |
| Atur durasi tes | Ya | Ya | Tidak |
| Arsipkan semua hasil | Ya | Tidak | Tidak |
| Kelola pengguna dan log aktivitas | Ya | Tidak | Tidak |

Kandidat tidak perlu akun: cukup buka link tes, misalnya `.../#disc-papi-kraepelin` (berurutan) atau `.../#disc`.

## Data dan cadangan
- Database: **`database/database.sqlite`** (beserta `.sqlite-wal` dan `.sqlite-shm` saat server berjalan).
- **Cadangan:** matikan server (Ctrl+C), lalu salin folder `database` ke tempat aman. **Pulihkan** dengan mengembalikannya.
- Selain itu pakai tombol **Ekspor CSV** secara berkala. *Hapus semua* hanya **mengarsipkan** hasil (tetap ada di database).
- **Mulai dari nol:** matikan server, kosongkan file `database/database.sqlite` (hapus lalu buat file kosong dengan nama yang sama), jalankan lagi.
- Database bisa dibuka dengan *DB Browser for SQLite*. Tabel: `users`, `sessions`, `results`, `logs`, `settings`.

## Jika ada masalah
- **`could not find driver`:** aktifkan `extension=pdo_sqlite` dan `extension=sqlite3` di `php.ini` (Laragon: Menu > PHP > php.ini), lalu restart.
- **`composer install` gagal:** pastikan PHP 8.2+ dan ekstensi di atas aktif, serta internet tersambung.
- **Layar "Server belum siap" / "Database belum tersambung":** periksa `.env` dan pastikan folder `database` bisa ditulis.
- **Halaman error 500:** buka `storage/logs/laravel.log`; untuk pesan lebih rinci ubah `APP_DEBUG=true` di `.env` sementara.
- **Port sudah dipakai:** `php artisan serve --port=8080`.

## Struktur project
```
routes/api.php                    pintu API (/api?a=...)
app/Http/Controllers/             ApiController (API), HomeController (halaman)
app/Support/Psikotes.php          logika: akun, peran, sesi, hasil, log (PDO + SQLite)
app/Support/Schema.php            rancangan tabel (juga dipakai migrasi)
app/Console/Commands/SelfTest.php php artisan psikotes:selftest
database/migrations/              migrasi tabel
resources/page.html               kerangka halaman msdm
public/psikotes.js, .css          tampilan dan logika aplikasi (tes, skoring, laporan)
config/psikotes.php               pengaturan aplikasi (SETUP_KEY)
```
Tabel dibuat otomatis saat pertama kali dipakai (`php artisan migrate` juga tersedia).

## Catatan penting
- **Privasi.** Nama dan jawaban kandidat adalah data pribadi. Jaga komputer dan cadangannya, dan beri tahu kandidat bagaimana data dipakai. Password disimpan dalam bentuk hash (bcrypt).
- **Kunci skoring** ikut tertanam di tampilan, jadi kandidat yang teliti bisa melihatnya lewat "View Source". Untuk DISC (tes preferensi) risikonya rendah.
- **Hak pakai.** Pastikan Anda berhak memakai tiap alat tes. Hasil bersifat indikatif dan perlu diinterpretasikan Manajemen SDM atau asesor yang berwenang.


## Dipasang online (misalnya www.msdm-uts.net)

1. Arahkan root web server (document root) ke folder `public/`, bukan folder proyek. Kalau tidak, `.env` dan `database/database.sqlite` bisa diunduh orang.
2. Pasang HTTPS. Password dan token login dikirim lewat permintaan ke server.
3. Di `.env`: `APP_ENV=production`, `APP_DEBUG=false`, dan isi `SETUP_KEY` dengan kunci rahasia. Kunci ini diminta saat membuat Super Admin pertama.
4. Buat Super Admin pertama segera setelah online, lalu cadangkan `database/database.sqlite` secara rutin.
5. Bila situs berada di belakang proxy atau CDN (misalnya Cloudflare), atur trusted proxies Laravel agar pembatas percobaan login membaca IP pengunjung yang asli, bukan IP proxy.

Pembatasan bawaan: login dibatasi 10 kali gagal per username dan 30 per IP dalam 15 menit; pendaftaran 10 per jam per IP; pengiriman hasil tes 200 per jam per IP.

Penghapusan data: tombol "Hapus semua" hanya mengarsipkan hasil (masih tersimpan di database). Untuk menghapus selamanya, Super Admin memakai tombol "Hapus permanen arsip", misalnya bila kandidat meminta datanya dihapus. Log aktivitas otomatis dibatasi 5000 catatan terbaru.

## Akses khusus alumni UTS

Kandidat harus login dulu dengan NIM dan password sebelum mengerjakan tes, dan hasil hanya diterima dari alumni yang sudah login. Admin atau Super Admin mengelola akunnya lewat menu **Alumni**: tempel daftar `NIM;Nama;Password` (satu baris per alumni), lalu klik Impor. Password yang dikosongkan dibuatkan otomatis dan hanya ditampilkan sekali setelah impor, jadi salin dan bagikan saat itu juga. Password diisi sendiri minimal 8 karakter.

Catatan: soal tes tetap berada di file `public/psikotes.js`, jadi pembatasan ini mengatur siapa yang bisa memakai aplikasi dan mengirim hasil, bukan menyembunyikan isi soal.

## Membuat akun staf dari daftar

Untuk membuat banyak akun Super Admin/Admin sekaligus, siapkan berkas teks (satu baris: `username;Nama;Password`) lalu jalankan di folder proyek:

    php artisan psikotes:seed-staff super-admin-impor.txt --role=super

Gunakan `--role=admin` atau `--role=employee` untuk peran lain. Akun yang sudah ada dilewati. Semua akun hasil perintah ini wajib mengganti password pada login pertama, dan sebelum itu tidak bisa memakai fitur lain. Hapus berkas daftar setelah dipakai karena berisi password awal.

## Alat tes yang boleh dibagikan

Super Admin otomatis boleh membagikan semua alat tes. Admin dan Employee baru belum diberi satu pun: menu "Bagikan tes" mereka kosong sampai Super Admin mencentang alat tes di Kelola pengguna, tombol Ubah, bagian "Alat tes yang boleh dibagikan". Pembatasan ini mengatur tampilan menu bagikan, bukan memblokir server: format link (`#dpk`) bersifat umum, jadi orang yang sudah tahu formatnya tetap bisa menulis link sendiri.

## Pembatasan akses staf

Hanya nama pengguna di `staff_allow` pada `config/psikotes.php` yang bisa mendaftar, dibuat, atau login sebagai staf. Akun lama yang namanya tidak ada di daftar tidak bisa login lagi. Menambah orang baru berarti menambahkan namanya ke daftar itu di server. Hak cipta dijelaskan di `HAK-CIPTA.txt`.

Pendaftaran mandiri dari halaman login sudah ditutup. Akun staf hanya dibuat oleh Super Admin yang sudah login (Kelola pengguna) atau lewat perintah `psikotes:seed-staff`. Tombol Register hanya muncul di layar pertama kali, untuk membuat Super Admin pertama saat database masih kosong.
