# Panduan Memasang Psikotes dengan PHP dan MySQL

Semua data (hasil tes, akun pengguna, pengaturan durasi, dan log aktivitas) disimpan di **database MySQL** milik hosting Anda, bukan di file.

## Isi paket
| File | Fungsi |
|---|---|
| `index.html` | Halaman psikotes (layar login, tes, laporan) |
| `api.php` | Server PHP: menyimpan dan membaca data di MySQL |
| `config.php` | Pengaturan koneksi database dan kunci pemasangan |
| `install.sql` | Rancangan tabel (opsional, tabel juga dibuat otomatis) |
| `.htaccess` | Memblokir akses langsung ke `config.php` dan `install.sql` |

## Yang dibutuhkan
- Hosting dengan **PHP 7.4 atau lebih baru** dan **MySQL/MariaDB** (hampir semua hosting cPanel sudah ada). Ekstensi **pdo_mysql** harus aktif (standar di kebanyakan hosting).
- Domain atau subdomain dengan **HTTPS/SSL aktif** (di cPanel: *SSL/TLS Status* lalu *Run AutoSSL*).

## Langkah 1: Buat database MySQL (cPanel)
1. Di cPanel buka **MySQL® Database Wizard**.
2. **Langkah 1** Buat database: isi nama, misalnya `psikotes`. Nama lengkapnya akan berawalan nama akun, misalnya `akunanda_psikotes`. **Catat nama lengkap ini.**
3. **Langkah 2** Buat pengguna database: isi nama pengguna (misalnya `psikotes`, jadi `akunanda_psikotes`) dan password yang kuat. **Catat keduanya.**
4. **Langkah 3** Centang **ALL PRIVILEGES**, lalu **Next Step**.

## Langkah 2: Unggah file
1. Di **File Manager**, buka `public_html`, buat subfolder (misalnya `psikotes`), lalu masuk ke dalamnya.
2. **Upload** file ZIP, klik kanan lalu **Extract**. Pastikan `index.html`, `api.php`, dan `config.php` berada langsung di folder itu. Aktifkan *Settings > Show Hidden Files* agar `.htaccess` terlihat. Hapus file ZIP setelahnya.

## Langkah 3: Isi pengaturan
Klik kanan `config.php` > **Edit**, lalu isi:
- `$DB_HOST`: biasanya `localhost`
- `$DB_NAME`: nama lengkap database (Langkah 1)
- `$DB_USER`: nama lengkap pengguna database
- `$DB_PASS`: password database
- `$SETUP_KEY`: kunci pemasangan, yaitu kata sandi acak panjang yang hanya dipakai sekali untuk mendaftarkan Super Admin pertama

Klik **Save Changes**.

## Langkah 4: Daftarkan Super Admin pertama
1. Buka `https://namaanda.com/psikotes/`. Tabel database dibuat otomatis saat halaman pertama dibuka.
2. Karena belum ada akun, tampil formulir **Daftar Super Admin (pertama kali)**. Isi kunci pemasangan, nama pengguna, nama lengkap, dan password (minimal 6 karakter), lalu klik **Register**. Akun pertama ini otomatis menjadi Super Admin.
3. Selanjutnya halaman pertama adalah layar login: pilih tombol peran, isi nama pengguna dan password, lalu klik **Login**.

**Jika muncul "Database belum tersambung":** periksa lagi isi `config.php` (nama database, pengguna, dan password, termasuk awalan akun cPanel). **Jika tabel tidak terbentuk otomatis:** buka **phpMyAdmin**, pilih database Anda, tab **Import**, pilih `install.sql`, lalu **Go**.

## Peran pengguna
| Kemampuan | Super Admin | Admin | Employee |
|---|:-:|:-:|:-:|
| Lihat hasil dan laporan gabungan | Ya | Ya | Ya |
| Cetak dan unduh laporan | Ya | Ya | Ya |
| Ekspor CSV | Ya | Ya | Tidak |
| Atur durasi tes | Ya | Ya | Tidak |
| Arsipkan semua hasil | Ya | Tidak | Tidak |
| Kelola pengguna dan lihat log aktivitas | Ya | Tidak | Tidak |

- **Mendaftarkan rekan:** di Menu utama klik **Buka hasil dan laporan** > **Kelola pengguna** > **Daftarkan pengguna baru**. Atau rekan mendaftar sendiri lewat tombol **Register** di layar login, lalu Super Admin menyetujuinya di kartu **Menunggu persetujuan** (pilih peran akhir lalu **Setujui**, atau **Tolak**). Peran Super Admin tidak bisa diminta lewat Register. Antrean pendaftar dibatasi 30 orang.
- **Mengubah peran, reset password, atau menonaktifkan:** klik **Ubah** pada baris pengguna (password dikosongkan jika tidak diganti). Perubahan berlaku langsung.
- **Memantau:** **Log aktivitas** mencatat siapa (nama pengguna dan perannya) yang masuk, termasuk percobaan gagal, membuka laporan kandidat siapa, mengekspor data, atau mengubah pengaturan (300 aktivitas terakhir).
- Sistem menolak menghapus atau menonaktifkan Super Admin terakhir. Sesi masuk berlaku 8 jam.

## Mengatur durasi tes
Masuk sebagai Admin atau Super Admin, klik **Atur durasi**, isi menit untuk DISC dan PAPI Kostick, serta detik per kolom, jumlah kolom, dan angka per kolom untuk Kraepelin, lalu **Simpan**. Nilai bawaan: DISC 10 menit, PAPI 30 menit, Kraepelin 40 kolom x 15 detik.

## Link untuk dibagikan
- **Kandidat** (langsung ke satu tes, tanpa login): `.../#disc`, `.../#papi`, `.../#kraepelin`
- **Staf**: alamat utama (layar login). Di layar login ada tautan kecil **Peserta tes? Buka menu tes** untuk peserta yang hanya tahu alamat utama.

## Uji coba (wajib sebelum dibagikan)
1. Buka `.../#disc` di HP, kerjakan sampai selesai: harus muncul "Jawaban Anda sudah terkirim".
2. Daftarkan Super Admin, masuk, klik **Buka hasil dan laporan**: hasil tadi harus muncul. Buka laporan gabungannya.
3. Daftarkan satu Admin dan satu Employee. Masuk dengan masing-masing akun (browser lain) dan pastikan tombolnya sesuai tabel peran.
4. Coba **Register** dengan akun baru: harus berstatus menunggu, tidak bisa login sebelum disetujui.
5. Coba password salah, dan masuk lewat tombol peran yang tidak sesuai: harus ditolak. Klik **Keluar**, muat ulang halaman: harus masuk lagi.
6. Atur durasi, lalu buka link tes di perangkat lain: durasinya harus berubah.
7. Cek **Log aktivitas**: semua langkah tadi harus tercatat.
8. Di phpMyAdmin, buka database Anda: tabel `psi_users`, `psi_results`, `psi_logs`, `psi_sessions`, `psi_settings` harus ada dan terisi.
9. **Cek keamanan:** buka `.../config.php` dan `.../install.sql`: keduanya harus **Forbidden / 403**. Lalu buka `.../api.php?a=users`: harus berupa penolakan (`"ok":false`), bukan daftar pengguna. Jika ada yang terbuka, jangan bagikan link ke kandidat dan hubungi penyedia hosting.

## Cadangan
- **Database:** di cPanel buka **Backup** > *Download a MySQL Database Backup*, atau di phpMyAdmin pilih database > **Export** > **Go**. Lakukan rutin, terutama setelah tes besar.
- **Tampilan:** *Ekspor CSV* di halaman hasil. *Hapus semua* hanya menandai hasil lama sebagai arsip (tetap ada di database), bukan menghapus.

## Catatan penting
- **Privasi.** Nama dan jawaban kandidat adalah data pribadi. Beri tahu kandidat bagaimana data dipakai, dan berikan akses hanya ke yang berhak. Password pengguna disimpan dalam bentuk hash (tidak bisa dibaca).
- **Kunci skoring** ikut tertanam di `index.html`, jadi kandidat yang teliti bisa melihatnya lewat "View Source". Untuk DISC (tes preferensi) risikonya rendah.
- **Pembatasan Employee** untuk ekspor CSV diterapkan di tampilan. Employee tetap bisa membuka dan mengunduh laporan per kandidat.
- **Server Nginx.** Tambahkan aturan `location ~ ^/psikotes/(config\.php|install\.sql) { deny all; }` pada konfigurasi situs (minta bantuan penyedia hosting).
- **Hak pakai.** Pastikan Anda berhak memakai tiap alat tes. Hasil bersifat indikatif dan perlu diinterpretasikan psikolog atau asesor yang berwenang.
