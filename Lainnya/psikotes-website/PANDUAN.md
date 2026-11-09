# Panduan Memasang Website Psikotes (DISC, PAPI Kostick, dan Kraepelin)

## Isi paket
| File | Fungsi |
|---|---|
| `index.html` | Halaman tes untuk kandidat: menu pilihan tes, soal, timer, skoring (DISC, PAPI Kostick, dan Kraepelin) |
| `api.php` | Menerima dan menyimpan jawaban kandidat di server Anda |
| `config.php` | Tempat password admin |
| `data/` | Folder tempat hasil disimpan (diblokir dari akses langsung) |
| `.htaccess` | Pengaman tambahan (file tersembunyi) |

## Yang dibutuhkan
- Hosting dengan **PHP 7.4 atau lebih baru** (hampir semua hosting cPanel sudah ada)
- Domain atau subdomain, dengan **HTTPS/SSL aktif** (di cPanel: menu *SSL/TLS Status* lalu *Run AutoSSL*)

## Langkah pemasangan (cPanel)
1. Siapkan password admin yang panjang dan sulit ditebak (Anda akan memasangnya di langkah 6).
2. Siapkan file `psikotes-website.zip` di komputer Anda (tidak perlu dibuka).
3. Masuk **cPanel > File Manager**, buka folder `public_html`. Untuk website khusus psikotes, buat subfolder, misalnya `psikotes`, lalu masuk ke dalamnya. (Atau pakai subdomain, misalnya `tes.namaanda.com`, yang folder dokumennya Anda pilih sendiri.)
4. Klik **Upload**, pilih file `psikotes-website.zip`.
5. Setelah selesai, klik kanan file ZIP lalu **Extract**. Pastikan `index.html`, `api.php`, dan `config.php` berada langsung di folder itu (bukan di dalam folder tambahan). Aktifkan *Settings > Show Hidden Files* agar `.htaccess` terlihat.
6. **Ganti password admin:** di File Manager, klik kanan `config.php` lalu **Edit**, ubah `GANTI-DENGAN-PASSWORD-ANDA` menjadi password Anda, lalu **Save Changes**.
7. Pastikan folder `data` bisa ditulis: klik kanan `data > Change Permissions`. Biasanya **755** sudah cukup. Jika nanti hasil gagal tersimpan, ubah ke **775**.
8. Hapus file ZIP dari server.

## Uji coba (wajib)
1. Buka `https://namaanda.com/psikotes/`, kerjakan tes sampai selesai. Harus muncul "Jawaban Anda sudah terkirim".
2. Buka `https://namaanda.com/psikotes/#admin`, masukkan password. Hasil uji coba tadi harus tampil. Klik **Lihat** untuk melihat grafik dan interpretasi.
3. **Cek keamanan:** buka `https://namaanda.com/psikotes/data/results.jsonl` dan `.../config.php`. Keduanya harus menampilkan **Forbidden / 403**. Jika file hasil bisa terbuka, segera hubungi penyedia hosting (server Nginx butuh aturan khusus, lihat bagian bawah).

## Cara memakai
- **Menu pilihan tes:** `https://namaanda.com/psikotes/` (kandidat memilih DISC atau PAPI Kostick).
- **Link langsung per tes** (kandidat langsung masuk ke tes itu, tanpa menu):
  - DISC: `https://namaanda.com/psikotes/#disc`
  - PAPI Kostick: `https://namaanda.com/psikotes/#papi`
  - Kraepelin: `https://namaanda.com/psikotes/#kraepelin`
- **Admin (Anda):** `https://namaanda.com/psikotes/#admin`. Jangan bagikan link ini.
- **Laporan gabungan:** di halaman admin, bagian "Laporan gabungan per kandidat", klik tombol di baris kandidat. Semua hasil tes kandidat itu tampil dalam satu halaman beserta kolom kesimpulan asesor, lalu klik *Unduh laporan* untuk menyimpannya sebagai satu file. Kandidat dikelompokkan berdasarkan kesamaan nama.
- **Cadangan:** klik *Ekspor CSV* secara berkala. Tombol *Hapus semua* hanya memindahkan data lama ke file arsip di folder `data`, bukan menghapusnya.

## Pengaturan (di blok PENGATURAN pada script `index.html`)
- `menit` pada tiap tes: batas waktu (DISC 10 menit, PAPI 30 menit). Ubah angkanya sesuai kebijakan Anda.
- `KRP = {kolom:40, angka:60, detik:15}`: pengaturan Kraepelin (jumlah kolom, jumlah angka per kolom, detik per kolom).
- `TAMPILKAN_HASIL = false`: kandidat hanya melihat "Terima kasih", hasil hanya terlihat oleh admin. Ubah ke `true` jika kandidat boleh langsung melihat hasil.

## Catatan penting
- **Privasi data.** Jawaban dan nama kandidat adalah data pribadi. Beri tahu kandidat bagaimana data dipakai dan disimpan, dan batasi siapa yang tahu password admin.
- **Kunci skoring.** Aturan skoring ikut tertanam di `index.html`, jadi kandidat yang teliti bisa melihatnya lewat "View Source". Untuk DISC (tes preferensi, tidak ada jawaban benar-salah) ini risiko rendah.
- **Hak pakai.** Pastikan Anda berhak memakai alat tes ini untuk keperluan Anda.
- **Hasil indikatif.** Interpretasi perlu ditinjau psikolog atau asesor yang berwenang.
- **Server Nginx.** Tambahkan aturan `location ~ ^/psikotes/(data|config\.php) { deny all; }` pada konfigurasi situs (minta bantuan penyedia hosting).
- **Tanpa PHP.** Jika hosting Anda hanya static (Netlify, GitHub Pages), penyimpanan hasil perlu layanan lain seperti Google Sheets. Hubungi saya untuk versinya.
