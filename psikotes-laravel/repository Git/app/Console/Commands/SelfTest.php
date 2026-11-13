<?php

namespace App\Console\Commands;

use App\Support\Psikotes;
use Illuminate\Console\Command;
use PDO;

class SelfTest extends Command
{
    protected $signature = 'psikotes:selftest';

    protected $description = 'Menguji logika psikotes (akun, peran, hasil, log) pada database sementara di memori';

    public function handle()
    {
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $s = new Psikotes($pdo, 'KUNCI');
        $s->ensureSchema();

        $ok = 0;
        $fail = 0;
        $cek = function ($nama, $kondisi) use (&$ok, &$fail) {
            if ($kondisi) {
                $ok++;
                $this->line("  [OK]    $nama");
            } else {
                $fail++;
                $this->error("  [GAGAL] $nama");
            }
        };
        $panggil = function ($a, $body = null) use ($s) {
            return $s->handle($a, $body === null ? 'GET' : 'POST', $body === null ? '' : json_encode($body), 'http', 8000);
        };

        $this->info('Menguji logika psikotes...');
        $st = $panggil('status');
        $cek('status awal: belum ada akun', $st['ok'] && $st['setup'] === true);
        $cek('daftar Super Admin dengan kunci salah ditolak', ($panggil('setup', ['key' => 'x', 'u' => 'owner', 'n' => 'Owner', 'p' => 'rahasia1'])['err'] ?? '') === 'kunci');
        $cek('password pendek ditolak', ($panggil('setup', ['key' => 'KUNCI', 'u' => 'owner', 'n' => 'Owner', 'p' => '123'])['err'] ?? '') === 'pw');
        $cek('daftar Super Admin pertama berhasil', $panggil('setup', ['key' => 'KUNCI', 'u' => 'owner', 'n' => 'Owner', 'p' => 'rahasia1'])['ok'] === true);
        $cek('Super Admin kedua lewat setup ditolak', ($panggil('setup', ['key' => 'KUNCI', 'u' => 'hax', 'n' => 'Hax', 'p' => 'rahasia1'])['err'] ?? '') === 'sudah');
        $cek('login lewat tombol peran yang salah ditolak', ($panggil('login', ['role' => 'admin', 'u' => 'owner', 'p' => 'rahasia1'])['err'] ?? '') === 'login');
        $l = $panggil('login', ['role' => 'super', 'u' => 'owner', 'p' => 'rahasia1']);
        $cek('login Super Admin berhasil', $l['ok'] === true && strlen($l['token']) === 48);
        $tk = $l['token'];
        $cek('daftar hasil tanpa token ditolak', ($panggil('list', [])['err'] ?? '') === 'auth');
        $cek('daftar hasil dengan token berhasil', $panggil('list', ['token' => $tk])['ok'] === true);

        $cek('Register mandiri masuk antrean', $panggil('register', ['u' => 'dewi', 'n' => 'Dewi', 'p' => 'dewi1234', 'r' => 'super'])['ok'] === true);
        $cek('pendaftar belum bisa login (menunggu persetujuan)', ($panggil('login', ['role' => 'employee', 'u' => 'dewi', 'p' => 'dewi1234'])['err'] ?? '') === 'pending');
        $cek('Super Admin menyetujui sebagai Admin', $panggil('saveuser', ['token' => $tk, 'u' => 'dewi', 'n' => 'Dewi', 'r' => 'admin', 'p' => '', 'on' => true])['ok'] === true);
        $d = $panggil('login', ['role' => 'admin', 'u' => 'dewi', 'p' => 'dewi1234']);
        $cek('Dewi login sebagai Admin', $d['ok'] === true);
        $cek('Admin tidak boleh membuka daftar pengguna', ($panggil('users', ['token' => $d['token']])['err'] ?? '') === 'izin');
        $cek('Admin boleh mengatur durasi', $panggil('savecfg', ['token' => $d['token'], 'cfg' => ['disc' => 7, 'papi' => 45, 'krp' => ['kolom' => 5, 'angka' => 30, 'detik' => 9]]])['ok'] === true);
        $cfg = $panggil('cfg');
        $cek('durasi tersimpan dan terbaca publik', ($cfg['cfg']['disc'] ?? 0) === 7 && ($cfg['cfg']['krp']['detik'] ?? 0) === 9);

        $disc = $panggil('save', ['token' => $tk, 'test' => 'disc', 'name' => 'Rina', 'age' => '24', 'sex' => 'Perempuan', 'date' => '2026-01-01', 'minutes' => 3.5, 'P' => array_fill(0, 24, 0), 'K' => array_fill(0, 24, 1)]);
        $cek('kandidat mengirim hasil DISC (tanpa login)', $disc['ok'] === true);
        $cek('hasil dengan data tidak valid ditolak', $panggil('save', ['token' => $tk, 'test' => 'papi', 'name' => 'X', 'A' => [0, 1]])['ok'] === false);
        $kr = $panggil('save', ['token' => $tk, 'test' => 'kraepelin', 'name' => 'Rina', 'C' => [['d' => '1234567890', 'a' => '5791']]]);
        $cek('kandidat mengirim hasil Kraepelin', $kr['ok'] === true);
        $ls = $panggil('list', ['token' => $d['token']]);
        $cek('daftar hasil berisi 2 data dengan jawaban', $ls['ok'] === true && count($ls['entries']) === 2 && count($ls['entries'][0]['P']) === 24 && $ls['entries'][1]['C'][0]['a'] === '5791');

        $cek('info jaringan bisa dibaca Admin', $panggil('info', ['token' => $d['token']])['ok'] === true);
        $cek('Admin tidak boleh mengarsipkan hasil', ($panggil('clear', ['token' => $d['token']])['err'] ?? '') === 'izin');
        $cek('simpan hasil tanpa login ditolak', ($panggil('save', ['test' => 'papi', 'name' => 'X', 'A' => [0, 1]])['err'] ?? '') === 'auth');
        $im = $panggil('aimport', ['token' => $tk, 'text' => "2023001;Budi;\n2023002;Siti;Rahasia123"]);
        $cek('impor alumni berhasil', ($im['ok'] ?? false) === true && ($im['n'] ?? 0) === 2 && count($im['made']) === 1);
        $al = $panggil('alogin', ['u' => '2023002', 'p' => 'Rahasia123']);
        $cek('alumni dapat masuk', ($al['ok'] ?? false) === true && strlen($al['token']) === 48);
        $cek('alumni salah password ditolak', ($panggil('alogin', ['u' => '2023002', 'p' => 'salahsalah'])['err'] ?? '') === 'alogin');
        $cek('token alumni lolos gerbang simpan', ($panggil('save', ['token' => $al['token'], 'test' => 'papi', 'name' => 'X', 'A' => [0, 1]])['err'] ?? '') !== 'auth');
        $cek('Super Admin terakhir tidak boleh dinonaktifkan', ($panggil('saveuser', ['token' => $tk, 'u' => 'owner', 'n' => 'Owner', 'r' => 'super', 'p' => '', 'on' => false])['err'] ?? '') === 'last');
        $cek('tidak bisa menghapus akun sendiri', ($panggil('deluser', ['token' => $tk, 'u' => 'owner'])['err'] ?? '') === 'self');
        $cek('Super Admin mengarsipkan hasil', $panggil('clear', ['token' => $tk])['ok'] === true && count($panggil('list', ['token' => $tk])['entries']) === 0);
        $lg = $panggil('log', ['token' => $tk]);
        $cek('log aktivitas tercatat', $lg['ok'] === true && count($lg['log']) >= 6);
        $cek('keluar menghapus sesi', $panggil('logout', ['token' => $d['token']])['ok'] === true && ($panggil('list', ['token' => $d['token']])['err'] ?? '') === 'auth');

        $this->newLine();
        if ($fail === 0) {
            $this->info("SEMUA LULUS ($ok pemeriksaan).");
            return self::SUCCESS;
        }
        $this->error("Ada $fail pemeriksaan yang gagal dari " . ($ok + $fail) . '.');
        return self::FAILURE;
    }
}
