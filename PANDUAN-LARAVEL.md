# Psikotes msdm: project Laravel + MySQL

Project ini menjalankan seluruh aplikasi (DISC, PAPI Kostick, Kraepelin, login Super Admin/Admin/Employee, Register dan persetujuan, bagikan tes ke kandidat, laporan gabungan, log aktivitas, tombol menu dengan logo msdm) **langsung di komputer Anda**. Semua data tersimpan di **database MySQL** (hanya MySQL/MariaDB yang didukung).

## Yang dibutuhkan
- **PHP 8.2 atau lebih baru** dengan ekstensi `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `xml`/`dom`, `ctype`, `tokenizer`.
- **MySQL 8 atau MariaDB 10.4+** yang sudah berjalan, dengan satu database kosong (mis. `psikotes`). Laragon Full sudah memuat MySQL.
- **Composer** (pengelola paket PHP).
- Internet **hanya saat `composer install` pertama kali**.

### Memasang PHP dan Composer
- **Windows (paling mudah): Laragon.** Unduh **Laragon Full** dari https://laragon.org dan pasang. Paket ini sudah memuat PHP dan Composer. Setelah terpasang, buka **Laragon > Menu > Terminal**, lalu masuk ke folder project ini. (Alternatif: XAMPP dengan PHP 8.2+ dan Composer dari https://getcomposer.org.)
- **Mac:** pasang Homebrew, lalu `brew install php composer`.
- **Ubuntu/Debian:** `sudo apt install php-cli php-mysql mysql-server php-mbstring php-xml php-curl unzip composer`.
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
- `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USERNAME`, `DB_PASSWORD`: koneksi MySQL. Buat database kosong dulu (mis. `CREATE DATABASE psikotes CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;`). Tabel dibuat otomatis saat permintaan pertama.
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
- Data tersimpan di database MySQL (tabel `users`, `sessions`, `results`, `logs`, `settings`, `attempts`, `alumni`, `alumni_sessions`, `links`, `iq_files`).
- **Cadangan:** `mysqldump -u USER -p psikotes > cadangan.sql` (atau lewat phpMyAdmin/Adminer/DB Explorer). **Pulihkan** dengan mengimpor berkas itu.
- Selain itu pakai tombol **Ekspor CSV** secara berkala. *Hapus semua* hanya **mengarsipkan** hasil (tetap ada di database).
- **Mulai dari nol:** hapus semua tabel di database tersebut (atau buat database baru), lalu buka aplikasi sekali; tabel dibuat ulang otomatis.

## Jika ada masalah
- **`could not find driver`:** aktifkan `extension=pdo_mysql` di `php.ini` (Laragon: Menu > PHP > php.ini), lalu restart.
- **`composer install` gagal:** pastikan PHP 8.2+ dan ekstensi di atas aktif, serta internet tersambung.
- **Layar "Server belum siap" / "Database belum tersambung":** pastikan MySQL berjalan dan isi `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USERNAME`, `DB_PASSWORD` di `.env` sudah benar.
- **Halaman error 500:** buka `storage/logs/laravel.log`; untuk pesan lebih rinci ubah `APP_DEBUG=true` di `.env` sementara.
- **Port sudah dipakai:** `php artisan serve --port=8080`.

## Struktur project
```
routes/api.php                    pintu API (/api?a=...)
app/Http/Controllers/             ApiController (API), HomeController (halaman)
app/Support/Psikotes.php          logika: akun, peran, sesi, hasil, log (PDO + MySQL)
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

1. Arahkan root web server (document root) ke folder `public/`, bukan folder proyek. Kalau tidak, `.env` (berisi kata sandi database) bisa diunduh orang.
2. Pasang HTTPS. Password dan token login dikirim lewat permintaan ke server.
3. Di `.env`: `APP_ENV=production`, `APP_DEBUG=false`, dan isi `SETUP_KEY` dengan kunci rahasia. Kunci ini diminta saat membuat Super Admin pertama.
4. Buat Super Admin pertama segera setelah online, lalu cadangkan database MySQL secara rutin.
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

## Akun Super Admin awal

Saat database masih kosong, aplikasi otomatis membuat 26 akun Super Admin dari `database/staf-awal.json` (hanya hash password, bukan teks asli). Layar pendaftaran pertama kali tidak muncul; yang tampil langsung halaman Login. Setiap akun wajib mengganti password pada login pertama. Perintah `psikotes:seed-staff` tetap bisa dipakai untuk menambah akun lain yang namanya ada di `staff_allow`.

Ke-26 nama awal juga otomatis didaftarkan sebagai alumni (layar Masuk Alumni UTS) dengan password awal yang sama, jadi mereka bisa membuka link tes dan mengerjakan tes. Alumni lain ditambahkan lewat menu Alumni.

## Tautan tes buatan sendiri

Admin dan Super Admin membuat tautan lewat menu **Tautan tes**: isi nama, alamat tautan (boleh kosong, dibuat otomatis), dan pilih alat tes (hanya yang sudah diberikan Super Admin). Hasilnya berbentuk `https://alamat-situs-anda/alamat-anda` (tanpa tanda `#`). Tautan bisa diubah, dinonaktifkan, atau dihapus kapan saja, dan tautan yang sudah terkirim ikut berubah. Admin hanya melihat tautan miliknya, Super Admin melihat semuanya. Tautan lama berbentuk `#dpk` atau `#alamat-anda` tetap berfungsi. Menu **Bagikan tes** kini menghasilkan `https://alamat-situs-anda/disc-papi-kraepelin` (tanpa kode `#dpk`); Admin dan Super Admin juga bisa mengisi kolom "Alamat pendek" di sana untuk menyimpan alamat sendiri, misalnya `/angkatan-2026`. Alamat situs pada tautan otomatis mengikuti alamat yang sedang dibuka (tidak lagi tertulis tetap di kode); kolom "Alamat situs" tetap bisa diubah.

## Tes Penalaran (IQ)

Tes IQ berisi 40 soal pilihan ganda, waktu 30 menit. Kunci jawaban dan penilaian ada di server (`app/Support/IqKey.php`), jadi tidak terbaca dari kode halaman. Peserta mengisi pendidikan terakhir dan bidang studi sebelum mulai (boleh kosong); keduanya tampil di laporan dan kolom CSV. Super Admin memberi Admin hak membagikan tes ini lewat Kelola pengguna. Skor standar dan perbandingan per usia, pendidikan, dan bidang studi hanya tampil untuk staf, setelah ada minimal 30 peserta, dan hanya membandingkan dengan peserta aplikasi ini. Hasilnya bukan skor IQ resmi.

## Tes IQ di website eksternal

Di bagian atas `public/psikotes.js` ada dua pengaturan:

```js
const IQ_EKSTERNAL=true, IQ_URL="https://international-iq-test.com/results";
```

- `IQ_EKSTERNAL=true`: kartu "Tes Penalaran (IQ)" membuka website penyedia di tab baru. Setelah selesai, kandidat mengisi skor IQ (40-220), kategori (opsional), dan mengunggah bukti hasil (PDF/PNG/JPG, maks 2 MB), lalu klik Kirim hasil.
- `IQ_EKSTERNAL=false`: kembali ke 40 soal bawaan aplikasi (kunci jawaban tetap di `app/Support/IqKey.php`).
- Ganti `IQ_URL` bila alamat tes berubah (sebaiknya halaman mulai tes, bukan halaman hasil).

Lampiran disimpan di dalam database MySQL (tabel `iq_files`), jadi ikut ter-backup bersama database dan tidak butuh folder yang bisa ditulis. Lampiran hanya bisa dibuka lewat panel staf yang sudah login. Gambar ikut tercetak di laporan; PDF dibuka lewat tombol "Buka PDF" (bisa dicetak/diunduh dari tab yang terbuka). Lampiran ikut terhapus saat hasil arsip dihapus permanen. Skor pada mode ini diisi peserta sendiri, jadi cocokkan dengan lampiran.

## Hosting di Wasmer

Disk bawaan aplikasi di Wasmer Edge bersifat sementara, jadi semua data harus ada di database MySQL (aplikasi ini memang hanya memakai MySQL).

1. Salin `app.yaml.contoh` ke `app.yaml` (atau gabungkan dengan milik Anda): bagian `capabilities.database.engine: mysql` dan `locality.regions` dengan satu wilayah (database tersedia di `fr-roub1`).
2. Deploy (`wasmer deploy`). Wasmer membuat database dan mengisi `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USERNAME`, `DB_PASSWORD` otomatis. Tabel dibuat otomatis saat permintaan pertama masuk; tidak perlu `php artisan migrate`.
3. Simpan `APP_KEY` dan `SETUP_KEY` sebagai secret Wasmer.
4. Uji: kirim satu hasil, ganti satu password, lalu deploy ulang. Hasil dan password harus tetap ada.
5. Backup: Wasmer menyimpan cadangan otomatis minimal 14 hari (restore lewat support Wasmer). Anda juga bisa memakai Ekspor CSV, atau `mysqldump` dengan kredensial dari `wasmer app database list --with-password`. Database bisa dilihat lewat "Go to DB Explorer" di dashboard.
6. Jika koneksi gagal karena TLS, set `DB_SSL=false` atau isi `DB_SSL_CA`. Pengaturan koneksi ada di `config/database.php`.

## Daftar staf, password awal, dan uji

- **Daftar staf yang boleh dibuat:** akun Admin/Employee/Super Admin baru hanya bisa dibuat untuk username yang ada di daftar `staff_allow` pada `config/psikotes.php`. Tambahkan username baru lewat env `STAFF_ALLOW` (pisahkan koma), misalnya `STAFF_ALLOW="budi.santoso,siti.aminah"`, atau `STAFF_ALLOW=*` untuk mencabut pembatasan.
- **Password awal:** akun Super Admin awal tidak dipaksa ganti password saat login pertama (`WAJIB_GANTI_SANDI` mati). Nyalakan dengan `WAJIB_GANTI_SANDI=true` bila perlu. Karena password awal bisa diketahui orang lain, sebaiknya tetap diganti lewat menu pengaturan akun.
- **Penanda di layar login:** baris kecil "Database: MySQL (permanen)".
- **Uji otomatis:** buat database kosong khusus uji berakhiran `_test` (mis. `psikotes_test`), lalu jalankan `SELFTEST_DB=psikotes_test php artisan psikotes:selftest`. Isi database itu akan DIKOSONGKAN, jadi jangan pakai database yang berisi data asli.
