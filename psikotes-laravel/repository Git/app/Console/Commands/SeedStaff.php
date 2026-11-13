<?php

namespace App\Console\Commands;

use App\Support\Psikotes;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SeedStaff extends Command
{
    protected $signature = 'psikotes:seed-staff {file : Berkas teks, satu baris: username;Nama;Password} {--role=super : super, admin, atau employee}';

    protected $description = 'Membuat akun staf dari berkas teks. Password awal wajib diganti pada login pertama.';

    public function handle()
    {
        $role = (string) $this->option('role');
        if (!in_array($role, ['super', 'admin', 'employee'], true)) {
            $this->error('Peran harus super, admin, atau employee.');
            return 1;
        }
        $path = (string) $this->argument('file');
        if (!is_file($path)) {
            $this->error('Berkas tidak ditemukan: ' . $path);
            return 1;
        }
        $pdo = DB::connection()->getPdo();
        (new Psikotes($pdo))->ensureSchema();
        $baru = 0;
        $lewat = 0;
        foreach (preg_split('/\r\n|\r|\n/', (string) file_get_contents($path)) as $i => $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            $c = array_map('trim', explode(';', $line));
            $u = strtolower($c[0] ?? '');
            $n = $c[1] ?? '';
            $p = $c[2] ?? '';
            if (!preg_match('/^[a-z0-9._-]{3,30}$/', $u) || $n === '' || strlen($p) < 8) {
                $this->warn('Baris ' . ($i + 1) . ' dilewati (format salah).');
                $lewat++;
                continue;
            }
            $ada = $pdo->prepare('SELECT 1 FROM users WHERE username = ?');
            $ada->execute([$u]);
            if ($ada->fetchColumn()) {
                $this->warn($u . ' sudah ada, dilewati.');
                $lewat++;
                continue;
            }
            $pdo->prepare('INSERT INTO users (username, name, role, pass_hash, active, pending, created_at, must_change) VALUES (?, ?, ?, ?, 1, 0, ?, 1)')
                ->execute([$u, $n, $role, password_hash($p, PASSWORD_DEFAULT), date('c')]);
            $baru++;
        }
        $this->info("Selesai: $baru akun dibuat, $lewat dilewati. Semua wajib mengganti password saat login pertama.");
        return 0;
    }
}
