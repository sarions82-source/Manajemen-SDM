# Psikotes msdm: project Laravel + MySQL

Project ini menjalankan seluruh aplikasi (DISC, PAPI Kostick, Kraepelin, IQ, Asesmen gabungan, IST, login Super Admin/Admin/Employee, Register dan persetujuan, bagikan tes ke kandidat, laporan gabungan, log aktivitas, tombol menu dengan logo msdm) **langsung di komputer Anda**. Semua data tersimpan di **database MySQL** (hanya MySQL/MariaDB yang didukung).

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
Perintah ini menguji logika akun, peran, Register dan persetujuan, hasil tes, durasi, dan log pada database sementara di memori (data Anda tidak tersentuh). Hasil yang benar: **`SEMUA LULUS (33 pemeriksaan)`**. Jika ada baris **[GAGAL]**, kirimkan hasilnya ke pembuat aplikasi.

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

- **Peran:** Super Admin hanya 26 akun awal (dari `database/staf-awal.json`). Super Admin membuat akun baru lewat **Kelola pengguna**, dan akun baru hanya bisa **Admin** (tidak ada pilihan Super Admin atau Employee); pendaftaran Super Admin pertama dan pendaftaran mandiri ditutup. Username 3-30 karakter (huruf kecil, angka, titik, garis bawah, atau strip), password minimal 8 karakter, lalu centang alat tes yang boleh dibagikan. Mode ini bisa dimatikan dengan `PERAN_KETAT=false`. Pembatasan nama staf per daftar (`staff_allow`) tetap opsional lewat `STAFF_ALLOW_ONLY=true`.
- **Password awal:** akun Super Admin awal tidak dipaksa ganti password saat login pertama (`WAJIB_GANTI_SANDI` mati). Nyalakan dengan `WAJIB_GANTI_SANDI=true` bila perlu. Karena password awal bisa diketahui orang lain, sebaiknya tetap diganti lewat menu pengaturan akun.
- **Penanda di layar login:** baris kecil "Database: MySQL (permanen)".
- **Uji otomatis:** buat database kosong khusus uji berakhiran `_test` (mis. `psikotes_test`), lalu jalankan `SELFTEST_DB=psikotes_test php artisan psikotes:selftest`. Isi database itu akan DIKOSONGKAN, jadi jangan pakai database yang berisi data asli.

## Data terpisah per Super Admin (tim)

Setiap Super Admin adalah satu tim yang terpisah. Super Admin lain tidak bisa melihat data tim Anda.

- **Admin buatan Super Admin** otomatis masuk tim Super Admin yang membuatnya.
- **Hasil tes kandidat** milik tim dari akun alumni (kandidat) yang mengerjakan: alumni yang diimpor lewat menu alumni oleh Super Admin/Admin A masuk tim A. Super Admin awal yang juga punya akun alumni sendiri masuk timnya sendiri.
- **Terpisah per tim:** daftar hasil, laporan gabungan dan ekspor CSV, arsip dan hapus permanen, lampiran bukti IQ, daftar pengguna (Super Admin hanya melihat dirinya dan Admin buatannya), tautan tes, daftar alumni, dan log aktivitas.
- Admin melihat hasil seluruh timnya, tetapi hanya tautan miliknya sendiri.
- NIM alumni yang sama tidak bisa dipakai dua tim sekaligus; baris yang bentrok ditolak saat impor.
- **Masih bersama:** pengaturan durasi tes (`savecfg`) berlaku untuk semua tim.
- Database lama otomatis mendapat kolom `team`; Super Admin lama otomatis menjadi pemilik timnya. Admin lama yang belum punya tim perlu dibuat ulang oleh Super Admin yang bersangkutan.

## Login kandidat dengan NIM (tanpa password)

Kandidat masuk hanya dengan **NIM**, tanpa password. NIM yang dimasukkan menentukan tim Super Admin yang menerima hasilnya. Ada 26 NIM tetap, satu untuk tiap Super Admin awal (disimpan di `database/staf-awal.json`, kolom `nim`):

D.23.02.003 Imelda Inka, D.23.02.004 Kadarisman, D.23.02.005 Agus Mustofa, D.23.02.009 A. Romadhan, D.23.02.010 Endang Silatur, D.23.02.013 Mohammad Feryzal, D.23.02.014 Petrus Damianus, D.23.02.016 Achmad Fanani, D.23.02.017 Deny Gita, D.23.02.018 Fajar Ramadhan, D.23.02.019 Ririn Kurniawati, D.23.02.020 Puguh Kurniawan, D.23.02.022 Irwan Candra, D.23.02.023 Williyanto Sakunab, D.23.02.024 Muhammad Fuad, D.23.02.025 Yoyanda Krisma, D.23.02.027 Yaladriya Raras, D.23.02.029 Ahmad Yusuf, D.23.02.030 Mochamad Sugeng, D.23.02.031 Bambang Irawan, D.23.02.032 Mohammad Subhan, D.23.02.034 Edwin Budi, D.23.02.036 Achmad Hamam, D.23.02.037 Iwan Agus, D.23.02.038 Yofi Yuliyanto, D.23.02.041 Martha Cahya.

- Admin memakai NIM Super Admin pemiliknya: kandidat Admin memasukkan NIM Super Admin tersebut. NIM itu tampil di menu **Bagikan tes** dan ikut tertulis di pesan WhatsApp.
- NIM tetap tidak bisa dihapus dari menu alumni. Penulisan NIM boleh tanpa titik atau huruf besar (D230234 dianggap sama).
- Karena tanpa password, siapa pun yang tahu NIM bisa mengerjakan tes atas nama tim itu. Bagikan NIM hanya ke kandidat.
- Impor alumni (menu alumni) cukup `NIM;Nama`; alumni impor juga masuk hanya dengan NIM, dan hasilnya masuk ke tim pengimpor.
- Nama kandidat diisi sendiri di formulir identitas (tidak otomatis memakai nama pemilik NIM tetap).

## Daftar kandidat dan laporan gabungan

- Daftar hasil di panel staf sekarang **satu baris per kandidat** (nama yang sama digabung). Klik nama untuk membuka daftar tes yang sudah dikerjakan (DISC, PAPI, Kraepelin, IQ), lalu klik **Lihat** untuk detail satu tes atau **Laporan gabungan** untuk semuanya. Penanda di samping nama menunjukkan tes mana yang sudah selesai. Tersedia kotak **Cari nama kandidat**.
- Laporan gabungan memuat semua tes, termasuk hasil IQ dari website penyedia (skor dan lampiran buktinya). Tombol **Cetak / PDF** menghasilkan **satu PDF** untuk seluruh tes kandidat itu.
- Lampiran bukti berupa **gambar** langsung tampil di laporan. Lampiran **PDF** diubah menjadi gambar (maksimal 4 halaman pertama) memakai pustaka pdf.js dari cdnjs, jadi perlu koneksi internet; jika gagal dimuat, tersedia tombol "Buka PDF asli". Tunggu lampiran selesai tampil sebelum mencetak.
- Kandidat digabung berdasarkan nama yang sama (huruf besar/kecil diabaikan). Dua orang dengan nama persis sama akan tergabung, jadi pastikan nama diisi lengkap dan unik.

## Logo di tab browser (favicon)

Ikon tab browser memakai logo MSDM: `public/favicon.ico`, `favicon-32.png`, `apple-touch-icon.png` (ikon layar utama HP), serta `icon-192.png` dan `icon-512.png`. Ikon ditautkan di `<head>` pada `resources/page.html`. Untuk menggantinya, timpa berkas-berkas itu (sebaiknya dari logo berukuran besar, minimal 512 px) dan naikkan angka `?v=1` pada tautannya agar browser memuat ulang. Berkas asli logo tersimpan di `public/logo-msdm.jpg`.

## Jika halaman tampil kosong

1. Buka `https://alamat-situs-anda/api?a=status` di browser. Jika muncul teks JSON (`{"ok":true,...}`), server dan database sehat. Jika muncul error, kosong, atau sangat lama, periksa koneksi MySQL (variabel `DB_*`) dan log aplikasi di Wasmer.
2. Aplikasi menampilkan pesan "Halaman tidak bisa dimuat" beserta penyebabnya bila skrip gagal atau server tidak menjawab dalam 12 detik. Kirim pesan itu (atau screenshot-nya) saat melaporkan masalah.
3. Tekan Ctrl+F5 (atau hapus cache) bila browser masih memakai berkas lama setelah deploy ulang.
4. Pastikan browser HP tidak dalam mode "Situs desktop"; mode itu membuat tampilan mengecil.

## Pesan undangan WhatsApp

Tombol WhatsApp di menu **Bagikan tes** dan di daftar **Tautan tes** mengirim pesan berisi link tes dan baris "NIM untuk masuk: ..." (NIM tetap Super Admin pemilik tim, tanpa password). NIM diambil dari akun yang sedang login: Super Admin memakai NIM-nya sendiri, Admin memakai NIM Super Admin pemiliknya. Tombol "Salin link" hanya menyalin link; kirim NIM terpisah bila memakai salin-tempel.

## Tampilan menu staf (sidebar)

Setelah login, di layar lebar (laptop/PC, lebar 900 px ke atas) menu staf berada di sidebar kiri dan isi halaman di kanan, memenuhi satu layar penuh (isi kanan bisa digulir sendiri). Menu: **Beranda**, **Hasil dan laporan**; **Kelola tes** (Bagikan tes, Tautan tes, Alumni / kandidat, Durasi tes; Super Admin dan Admin); **Administrasi** (Kelola pengguna, Log aktivitas; khusus Super Admin); dan **Keluar**. Di HP atau layar sempit tampilan tetap memakai tombol menu (hamburger) di bagian atas. Sidebar disembunyikan saat staf mengerjakan tes, saat membuka link kandidat, dan saat mencetak. Pengaturan tampilan ada di akhir `public/psikotes.css`.


## Asesmen gabungan (MBTI, LI, OCHAI, Self Assessment)

Satu alat tes baru bernama **Asesmen Kepribadian & Kepemimpinan** (id `asesmen`). Kandidat mengerjakan empat bagian berurutan dalam satu sesi (21 halaman, bawaan 60 menit):

1. **MBTI**: 60 nomor, pilih A atau B. Hasil: persentase tiap dimensi (I/E, S/N, T/F, J/P) dan tipe 4 huruf beserta karakteristik, saran pengembangan, profesi, dan pasangan alami (16 tipe).
2. **LI (Leadership Inventory)**: 7 bagian, tiap bagian membagi tepat 10 poin ke 8 pernyataan. Hasil: skor 8 peran (Coordinator, Driver, Finisher, Implementor, Monitor/Evaluator, Originator, Resource Investigator, Supporter), peringkat, peran dominan dan pendukung.
3. **OCHAI**: 6 pertanyaan, tiap pertanyaan membagi 100 poin ke 4 pernyataan (A Clan, B Adhocracy, C Market, D Hierarchy), untuk kondisi saat ini dan yang diharapkan. Hasil: rata-rata per tipe dan selisihnya.
4. **Self Assessment**: 20 kompetensi, nilai 1-5 dan tanda opsional (+ kekuatan, - perlu dikembangkan).

Hasil, grafik, dan **kesimpulan otomatis** muncul di panel staf (Hasil dan laporan) dan ikut di Laporan gabungan serta Ekspor CSV (kolom `asesmen_*`). Kunci skoring ada di `public/asesmen.js` (bersumber dari `Skoring_MBTI_LI_DISC_1.xlsx`); server hanya memeriksa bentuk jawaban. Durasi bisa diubah di menu Durasi tes. Super Admin memberi hak membagikan tes ini lewat Kelola pengguna seperti alat tes lain; tautan berbentuk `/disc-papi-kraepelin-iq-asesmen` atau `/asesmen`.

Catatan skoring LI: rumus di sheet SKORING LI pada berkas Excel asli memiliki kesalahan di bagian 3 (tujuh dari delapan peran membaca baris bagian 2). Aplikasi ini memakai pemetaan yang benar (bagian 3 membaca jawaban bagian 3), sehingga skor beberapa peran bisa berbeda dari Excel lama.

## IST (Intelligenz-Struktur-Test, Amthauer)

Alat tes baru dengan id `ist`: 9 subtes, 176 soal, tiap subtes punya timer sendiri. Urutan: SE (6 menit), WA (6), AN (7), GE (8), menghafal kata ME (3), RA (10), ZR (10), FA (7), WU (9), lalu soal ME (6). Waktu ada di `public/ist.js` (konstanta `SUBS` dan `MEM_MIN`); waktu ini mengikuti pelaksanaan baku IST dan **tidak berasal dari berkas Excel**, jadi cocokkan dengan buku manual Anda.

- **Skor dihitung di server** (`app/Support/IstKey.php` dan `ist_data.json`), seperti tes IQ. Kunci dan norma tidak ada di kode halaman.
- Hasil: RW per subtes, SW dan kategori (norma usia), SW (norma pendidikan), Gesamt, IQ (usia dan pendidikan), dan dominasi. Logika mengikuti `IST_Norma_Usia.xls` dan `IST_Norma_Pendidikan.xls`, dan sudah dicocokkan dengan hasil hitung ulang kedua berkas itu pada 8 kasus acak (16 perbandingan, semuanya sama).
- Kandidat memilih pendidikan terakhir di awal tes (dipakai untuk norma pendidikan).
- Gambar soal FA dan WU ada di `public/ist/` (dipotong dari PDF).
- Subtes RA dan ZR: kandidat mengetik angka. Daftar jawaban yang diterima mengikuti Excel (urutan digit tidak berpengaruh).

Catatan kunci: kunci RA nomor 96 di Excel bernilai 9, padahal hitungan soalnya menghasilkan 3 anak laki-laki. Aplikasi memakai 3. Ubah di `ist_data.json` (`key.RA`, indeks 19) bila Anda ingin kembali ke nilai Excel.

## WPT (Wonderlic Personnel Test, Form A)

Alat tes baru dengan id `wpt`: 50 soal pemecahan masalah dalam satu timer (bawaan 12 menit, bisa diubah di menu Durasi tes). Satu halaman berisi 10 soal, kandidat boleh kembali ke halaman sebelumnya selama waktu masih ada.

- **Skor dihitung di server** (`app/Support/WptKey.php`). Skor = jumlah jawaban benar (0-50), lalu dikonversi ke IQ memakai tabel pada `Software_WPT.xlsx`. Hasil dicocokkan dengan hitungan ulang Excel itu pada 8 kasus acak (RS, IQ, soal terakhir, jumlah salah: semuanya sama).
- Soal bergambar (7, 38, 42, 49) ada di `public/wpt/`. Soal pilihan memakai tombol, soal hitungan diketik.
- Jawaban yang diterima lebih longgar dari rumus Excel pada hal berikut: pecahan dan desimal setara (1/8 = 0,125; 1/30 dan 1/9 boleh desimal 3 angka), pemisah ribuan (6.000), jawaban dua atau empat angka dalam urutan bebas (nomor 23, 38, 41, 42, 49), dan 0,24 untuk nomor 45. Huruf "t" tidak diterima untuk nomor 13, 20, 26 (di Excel dianggap benar, padahal bisa berarti "tidak tahu").
- Laporan: kartu ringkasan, posisi IQ, skor dibanding skor minimum kelompok jabatan (tabel Job Families dari Excel), jumlah benar per 10 soal, peta jawaban, dan kesimpulan otomatis. Tabel acuan lengkap dari Excel ikut tampil sebagai gambar. Tabel "Minimum Acceptable Score" per judul jabatan tidak dipakai karena susunan barisnya pada gambar Excel tidak jelas.

## Tampilan (tema baru)

Seluruh tampilan memakai tema gaya Mattermost: latar dan sidebar navy, panel isi putih membulat, tombol utama biru, teks lebih kecil (dasar 14 px). Semua gaya ada di bagian akhir `public/psikotes.css` (blok "Tema baru"); warna utama diatur lewat variabel `--ac` (biru) dan `--nv` (navy).

Tombol yang sedang tidak bisa diklik (atribut `disabled`) tidak ditampilkan (aturan `button:disabled:not(.mb){visibility:hidden}`), tetapi tempatnya tetap ada agar tata letak tidak bergeser. Tombol menu `.mb` di header dikecualikan karena berisi logo.
