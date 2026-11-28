<?php

namespace App\Support;

use PDO;

/**
 * Logika psikotes: akun, peran, sesi, hasil tes, pengaturan, dan log.
 * Tidak bergantung pada Laravel (hanya PDO), sehingga mudah diuji.
 * Setiap aksi dipanggil lewat handle($a, ...) dan mengembalikan array yang dikirim sebagai JSON.
 */
class Psikotes
{
    const RANK = ['employee' => 1, 'admin' => 2, 'super' => 3];
    const RN = ['employee' => 'Employee', 'admin' => 'Admin', 'super' => 'Super Admin'];
    const UN = '/^[a-z0-9_.-]{3,30}$/';

    private $pdo;
    private $key;
    private $requireKey;
    private $cur = '';
    private $allow = [];
    private $seedFile = '';
    private $wajibGanti = true;
    /** Mode ketat: Super Admin hanya akun awal (staf-awal.json); akun baru hanya bisa Admin; pendaftaran Super Admin pertama ditutup. */
    private $ketat = false;
    private $pendingAlumni = [];

    public function __construct(PDO $pdo, $setupKey = '', $requireKey = false, $allow = [], $seedFile = '', $wajibGanti = true, $ketat = false)
    {
        $this->wajibGanti = (bool) $wajibGanti;
        $this->ketat = (bool) $ketat;
        $this->seedFile = (string) $seedFile;
        $this->allow = array_values(array_map('strtolower', array_filter((array) $allow, 'is_string')));
        $this->pdo = $pdo;
        $this->key = (string) $setupKey;
        $this->requireKey = (bool) $requireKey;
    }

    /** Membuat tabel MySQL jika belum ada, lalu mengisi akun awal. Penanda: tabel iq_files (tabel terakhir yang dibuat). */
    public function ensureSchema()
    {
        $ada = $this->q("SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'iq_files'")->fetchColumn();
        if (!$ada) {
            foreach (Schema::MYSQL as $sql) {
                $this->pdo->exec($sql);
            }
        }
        // Pemisahan data per Super Admin (tim): kolom team untuk database yang dibuat sebelum fitur ini
        $adaTeam = (int) $this->q("SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND column_name = 'team' AND table_name IN ('users', 'results', 'logs', 'alumni', 'links')")->fetchColumn();
        if ($adaTeam < 5) {
            foreach (['users', 'results', 'logs', 'alumni', 'links'] as $t) {
                if (!$this->q("SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? AND column_name = 'team'", [$t])->fetchColumn()) {
                    $this->pdo->exec("ALTER TABLE $t ADD COLUMN team VARCHAR(60) NOT NULL DEFAULT '', ADD KEY idx_{$t}_team (team)");
                }
            }
            $this->q("UPDATE users SET team = username WHERE role = 'super' AND team = ''");
            $this->q("UPDATE alumni a JOIN users u ON u.username = a.username AND u.role = 'super' SET a.team = u.team WHERE a.team = ''");
        }
        // Kolom nim (NIM tetap 26 Super Admin awal) untuk database yang dibuat sebelum fitur ini
        if (!$this->q("SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'users' AND column_name = 'nim'")->fetchColumn()) {
            $this->pdo->exec("ALTER TABLE users ADD COLUMN nim VARCHAR(30) NOT NULL DEFAULT '', ADD KEY idx_users_nim (nim)");
        }
        $this->ubahNamaLama();
        $this->seedStaf();
        $this->seedAlumni();
        $this->syncNim();
    }

    /** Koreksi ejaan akun awal pada database yang sudah ada: yofi.yulianto -> yofi.yuliyanto (semua data ikut dipindahkan). */
    private function ubahNamaLama()
    {
        $lama = 'yofi.yulianto';
        $baru = 'yofi.yuliyanto';
        if (!$this->q('SELECT 1 FROM users WHERE username = ?', [$lama])->fetchColumn() || $this->q('SELECT 1 FROM users WHERE username = ?', [$baru])->fetchColumn()) {
            return;
        }
        $this->q("UPDATE users SET username = ?, name = 'Yofi Yuliyanto' WHERE username = ?", [$baru, $lama]);
        $this->q('UPDATE users SET team = ? WHERE team = ?', [$baru, $lama]);
        foreach (['results', 'logs', 'alumni', 'links'] as $t) {
            $this->q("UPDATE $t SET team = ? WHERE team = ?", [$baru, $lama]);
        }
        $this->q('UPDATE links SET owner = ? WHERE owner = ?', [$baru, $lama]);
        $this->q('UPDATE sessions SET username = ? WHERE username = ?', [$baru, $lama]);
        $this->q("UPDATE alumni SET name = 'Yofi Yuliyanto' WHERE team = ? AND name = 'Yofi Yulianto'", [$baru]);
        $this->q("UPDATE logs SET who = REPLACE(who, ?, ?) WHERE who LIKE ?", [$lama, $baru, $lama . '%']);
    }

    /** Database yang sudah ada: isi NIM tetap Super Admin awal dan buat akun masuk (NIM) tanpa password; hapus akun alumni lama bernama staf. */
    private function syncNim()
    {
        if ($this->seedFile === '' || !is_file($this->seedFile) || $this->q("SELECT 1 FROM users WHERE nim <> '' LIMIT 1")->fetchColumn()) {
            return;
        }
        $daftar = json_decode((string) file_get_contents($this->seedFile), true);
        foreach (is_array($daftar) ? $daftar : [] as $x) {
            $u = strtolower((string) ($x['u'] ?? ''));
            $nim = strtolower((string) ($x['nim'] ?? ''));
            if ($u === '' || $nim === '' || empty($x['n'])) {
                continue;
            }
            $this->q("UPDATE users SET nim = ? WHERE username = ? AND role = 'super'", [$nim, $u]);
            $this->q("INSERT IGNORE INTO alumni (username, name, pass_hash, active, created, team) VALUES (?, ?, '', 1, ?, ?)", [$nim, (string) $x['n'], time(), $u]);
            $this->q('DELETE FROM alumni WHERE username = ?', [$u]);
        }
    }

    /** NIM tetap milik Super Admin tim ini (yang dimasukkan kandidat saat masuk). */
    private function nimTim(array $row)
    {
        $tm = (string) ($row['team'] ?? '');
        if ($tm === '' && ($row['role'] ?? '') === 'super') {
            $tm = (string) $row['username'];
        }
        if ($tm === '') {
            return '';
        }
        return strtoupper((string) $this->q('SELECT nim FROM users WHERE username = ?', [$tm])->fetchColumn());
    }

    private function seedStaf()
    {
        // Database kosong: buat akun Super Admin awal dari berkas (hanya hash password; wajib ganti saat login pertama).
        if ($this->seedFile !== '' && is_file($this->seedFile)
            && (int) $this->q('SELECT COUNT(*) FROM users')->fetchColumn() === 0) {
            $daftar = json_decode((string) file_get_contents($this->seedFile), true);
            foreach (is_array($daftar) ? $daftar : [] as $x) {
                $u = strtolower((string) ($x['u'] ?? ''));
                if (!preg_match(self::UN, $u) || !$this->diizinkan($u) || empty($x['h']) || empty($x['n'])) {
                    continue;
                }
                $this->q(
                    "INSERT IGNORE INTO users (username, name, role, pass_hash, active, pending, created_at, must_change, team, nim) VALUES (?, ?, 'super', ?, 1, 0, ?, " . ($this->wajibGanti ? '1' : '0') . ", ?, ?)",
                    [$u, (string) $x['n'], (string) $x['h'], date('c'), $u, strtolower((string) ($x['nim'] ?? ''))]
                );
                if (!empty($x['nim'])) {
                    $this->pendingAlumni[] = [strtolower((string) $x['nim']), (string) $x['n'], '', $u];
                }
            }
        }
    }

    private function seedAlumni()
    {
        // Nama yang sama juga boleh masuk lewat layar Masuk Alumni UTS (untuk mengerjakan tes).
        foreach ($this->pendingAlumni as $al) {
            $this->q('INSERT IGNORE INTO alumni (username, name, pass_hash, active, created, team) VALUES (?, ?, ?, 1, ?, ?)', [$al[0], $al[1], $al[2], time(), $al[3]]);
        }
        $this->pendingAlumni = [];
    }

    /** Pembatas percobaan: true bila kunci $key sudah mencapai $max dalam $win detik. */
    private function limited($key, $max, $win)
    {
        return (int) $this->q('SELECT COUNT(*) FROM attempts WHERE k = ? AND t > ?', [$key, time() - $win])->fetchColumn() >= $max;
    }

    private const ALAT = ['disc', 'papi', 'kraepelin', 'iq', 'asesmen', 'ist', 'wpt'];

    /** Alat tes yang boleh dibagikan: Super Admin semuanya, peran lain sesuai pemberian Super Admin. */
    private function testsOf($role, $csv)
    {
        if ($role === 'super') {
            return self::ALAT;
        }
        return array_values(array_intersect(self::ALAT, array_filter(explode(',', (string) $csv))));
    }

    /** Daftar nama staf yang diizinkan. Bila daftar kosong, tidak ada pembatasan. */
    private function diizinkan($u)
    {
        return $this->allow === [] || in_array(strtolower((string) $u), $this->allow, true);
    }

    /** Nomor induk alumni pemilik token sesi alumni yang sah, atau null. */
    private function alumniFrom($tk)
    {
        if (!is_string($tk) || !preg_match('/^[a-f0-9]{48}$/', $tk)) {
            return null;
        }
        $r = $this->q('SELECT a.username FROM alumni_sessions s JOIN alumni a ON a.username = s.username WHERE s.token = ? AND s.expires > ? AND a.active = 1', [$tk, time()])->fetchColumn();
        return $r === false ? null : (string) $r;
    }

    private function hit($key)
    {
        $this->q('INSERT INTO attempts (k, t) VALUES (?, ?)', [$key, time()]);
        if (random_int(1, 50) === 1) {
            $this->q('DELETE FROM attempts WHERE t < ?', [time() - 86400]);
        }
    }

    // ---------- pembantu ----------

    private function q($sql, array $args = [])
    {
        $st = $this->pdo->prepare($sql);
        $st->execute($args);
        return $st;
    }

    private function now()
    {
        return gmdate('Y-m-d\TH:i:s\Z');
    }

    private function log($who, $msg)
    {
        $nm = (string) strtok((string) $who, ' ');
        $this->q("INSERT INTO logs (created_at, who, msg, team) VALUES (?, ?, ?, COALESCE((SELECT team FROM users WHERE username = ?), ''))", [
            $this->now(), mb_substr((string) $who, 0, 100), mb_substr((string) $msg, 0, 255), $nm,
        ]);
        if (random_int(1, 50) === 1) {
            // simpan 5000 log terbaru saja agar database tidak membengkak
            $this->q('DELETE FROM logs WHERE id <= (SELECT m FROM (SELECT MAX(id) AS m FROM logs) x) - 5000');
        }
    }

    private function whoMe(array $m)
    {
        return $m['u'] . ' (' . self::RN[$m['r']] . ')';
    }

    private function supers()
    {
        return (int) $this->q("SELECT COUNT(*) FROM users WHERE role = 'super' AND active = 1")->fetchColumn();
    }

    /** Memeriksa jawaban WPT: 50 butir berupa teks pendek. Null bila bentuknya salah. */
    private function validWpt(array $in)
    {
        $A = $in['A'] ?? null;
        if (!is_array($A) || count($A) !== 50) {
            return null;
        }
        $out = [];
        foreach ($A as $v) {
            if (!is_string($v) || mb_strlen($v) > 40) {
                return null;
            }
            $out[] = $v;
        }
        return ['A' => $out];
    }

    /** Memeriksa jawaban IST: 6 subtes pilihan ganda (20 butir), GE (16 kata), RA dan ZR (20 angka). Null bila bentuknya salah. */
    private function validIst(array $in)
    {
        $A = $in['A'] ?? null;
        if (!is_array($A)) {
            return null;
        }
        $out = [];
        foreach (IstKey::MC as $k) {
            if (!$this->validIntArr($A[$k] ?? null, 20, 4)) {
                return null;
            }
            $out[$k] = array_values($A[$k]);
        }
        foreach (['GE' => 16, 'RA' => 20, 'ZR' => 20] as $k => $n) {
            $x = $A[$k] ?? null;
            if (!is_array($x) || count($x) !== $n) {
                return null;
            }
            $o = [];
            foreach ($x as $v) {
                if (!is_string($v) || mb_strlen($v) > 40) {
                    return null;
                }
                $o[] = $v;
            }
            $out[$k] = $o;
        }
        $edu = in_array($in['edu'] ?? '', IstKey::EDU, true) ? $in['edu'] : '';
        return ['A' => $out, 'edu' => $edu];
    }

    /** Memeriksa larik bilangan bulat sepanjang $n dengan nilai di antara $lo dan $hi. */
    private function rangeIntArr($x, $n, $lo, $hi)
    {
        if (!is_array($x) || count($x) !== $n) {
            return false;
        }
        foreach ($x as $v) {
            if (!is_int($v) || $v < $lo || $v > $hi) {
                return false;
            }
        }
        return true;
    }

    /** Memeriksa bentuk jawaban asesmen gabungan (MBTI, LI, OCHAI, Self Assessment). Mengembalikan payload bersih atau null. */
    private function validAsesmen(array $in)
    {
        $M = $in['M'] ?? null;
        if (!$this->validIntArr($M, 60, 1)) {
            return null;
        }
        $L = $in['L'] ?? null;
        if (!is_array($L) || count($L) !== 7) {
            return null;
        }
        $Lc = [];
        foreach ($L as $row) {
            if (!$this->rangeIntArr($row, 8, 0, 10)) {
                return null;
            }
            $Lc[] = array_values($row);
        }
        $O = $in['O'] ?? null;
        if (!is_array($O)) {
            return null;
        }
        $Oc = [];
        foreach (['n', 'e'] as $k) {
            $m = $O[$k] ?? null;
            if (!is_array($m) || count($m) !== 6) {
                return null;
            }
            $Oc[$k] = [];
            foreach ($m as $row) {
                if (!$this->rangeIntArr($row, 4, 0, 100)) {
                    return null;
                }
                $Oc[$k][] = array_values($row);
            }
        }
        $S = $in['S'] ?? null;
        if (!is_array($S) || count($S) !== 20) {
            return null;
        }
        $Sc = [];
        foreach ($S as $x) {
            if (!is_array($x) || !is_int($x['r'] ?? null) || $x['r'] < 0 || $x['r'] > 5) {
                return null;
            }
            $mk = $x['m'] ?? '';
            if (!in_array($mk, ['', '+', '-'], true)) {
                return null;
            }
            $Sc[] = ['r' => $x['r'], 'm' => $mk];
        }
        return ['M' => array_values($M), 'L' => $Lc, 'O' => $Oc, 'S' => $Sc];
    }

    private function validIntArr($x, $n, $max)
    {
        if (!is_array($x) || count($x) !== $n) {
            return false;
        }
        foreach ($x as $v) {
            if (!is_int($v) || $v < -1 || $v > $max) {
                return false;
            }
        }
        return true;
    }

    /** Menyimpan lampiran bukti hasil tes IQ (PDF/PNG/JPG, maks 2 MB). Mengembalikan nama berkas atau null. */
    private function simpanBuktiIq($b64)
    {
        if (!is_string($b64) || $b64 === '' || strlen($b64) > 3000000) {
            return null;
        }
        $bin = base64_decode($b64, true);
        if ($bin === false || strlen($bin) < 16 || strlen($bin) > 2097152) {
            return null;
        }
        if (strncmp($bin, '%PDF', 4) === 0) {
            $ext = 'pdf';
        } elseif (strncmp($bin, "\x89PNG", 4) === 0) {
            $ext = 'png';
        } elseif (strncmp($bin, "\xFF\xD8\xFF", 3) === 0) {
            $ext = 'jpg';
        } else {
            return null;
        }
        $nm = bin2hex(random_bytes(16)) . '.' . $ext;
        $mime = ['pdf' => 'application/pdf', 'png' => 'image/png', 'jpg' => 'image/jpeg'][$ext];
        $this->q('INSERT INTO iq_files (name, mime, data, created) VALUES (?, ?, ?, ?)', [$nm, $mime, base64_encode($bin), time()]);
        return $nm;
    }

    private function clampInt($v, $lo, $hi, $d)
    {
        $v = (int) round((float) $v);
        return ($v >= $lo && $v <= $hi) ? $v : $d;
    }

    /** Memeriksa token sesi dan peran minimum. Mengembalikan [pengguna, galat]. */
    private function me(array $in, $min)
    {
        $tk = $in['token'] ?? '';
        if (!is_string($tk) || !preg_match('/^[a-f0-9]{48}$/', $tk)) {
            usleep(300000);
            return [null, ['ok' => false, 'err' => 'auth']];
        }
        $r = $this->q(
            'SELECT u.username, u.name, u.role, u.must_change, u.tests, u.team FROM sessions s JOIN users u ON u.username = s.username WHERE s.token = ? AND s.expires > ? AND u.active = 1',
            [$tk, time()]
        )->fetch(PDO::FETCH_ASSOC);
        if (!$r) {
            return [null, ['ok' => false, 'err' => 'auth']];
        }
        if (!$this->diizinkan($r['username'])) {
            return [null, ['ok' => false, 'err' => 'auth']];
        }
        if ((int) $r['must_change'] === 1 && !in_array($this->cur, ['me', 'chpw'], true)) {
            return [null, ['ok' => false, 'err' => 'ganti']];
        }
        if (self::RANK[$r['role']] < $min) {
            return [null, ['ok' => false, 'err' => 'izin']];
        }
        $tm = (string) $r['team'];
        if ($tm === '' && $r['role'] === 'super') {
            $tm = $r['username'];
            $this->q('UPDATE users SET team = username WHERE username = ?', [$tm]);
        }
        return [['u' => $r['username'], 'n' => $r['name'], 'r' => $r['role'], 'm' => (int) $r['must_change'], 't' => $this->testsOf($r['role'], $r['tests']), 'tm' => $tm], null];
    }

    /** Alamat jaringan komputer ini (untuk link yang dibagikan ke perangkat lain). */
    private function lanAddresses($scheme, $port)
    {
        $ips = [];
        if (function_exists('net_get_interfaces')) {
            $ifs = @net_get_interfaces();
            if (is_array($ifs)) {
                foreach ($ifs as $if) {
                    foreach (($if['unicast'] ?? []) as $u) {
                        $ip = $u['address'] ?? '';
                        if (($u['family'] ?? 0) === 2 && filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)
                            && strpos($ip, '127.') !== 0 && strpos($ip, '169.254.') !== 0) {
                            $ips[] = $ip;
                        }
                    }
                }
            }
        }
        if (!$ips) {
            $ip = @gethostbyname(@gethostname());
            if ($ip && filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) && strpos($ip, '127.') !== 0) {
                $ips[] = $ip;
            }
        }
        $out = [];
        foreach (array_unique($ips) as $ip) {
            $out[] = $scheme . '://' . $ip . (in_array($port, [80, 443], true) ? '' : ':' . $port);
        }
        return $out;
    }

    // ---------- pintu masuk ----------

    public function handle($a, $method, $raw, $scheme = 'http', $port = 8000, $ip = '')
    {
        $ip = (string) $ip !== '' ? (string) $ip : '-';
        $this->cur = (string) $a;
        $RN = self::RN;
        $RANK = self::RANK;

        // ---- publik (tanpa body) ----
        if ($a === 'cfg') {
            $v = $this->q("SELECT v FROM settings WHERE k = 'cfg'")->fetchColumn();
            return ['ok' => true, 'cfg' => $v ? json_decode($v, true) : null];
        }
        if ($a === 'status') {
            $c = ['super' => 0, 'admin' => 0, 'employee' => 0];
            $total = 0;
            foreach ($this->q('SELECT role, active FROM users')->fetchAll(PDO::FETCH_ASSOC) as $v) {
                $total++;
                if ((int) $v['active'] === 1) {
                    $c[$v['role']]++;
                }
            }
            return ['ok' => true, 'setup' => !$this->ketat && $total === 0, 'roles' => $c, 'db' => 'mysql'];
        }
        if ($method !== 'POST') {
            return ['ok' => false];
        }
        // Batas umum 64 KB; hanya pengiriman hasil IQ dengan lampiran (aksi save) yang boleh sampai ~3,3 MB
        if (strlen($raw) > ($a === 'save' ? 3300000 : 65536)) {
            return ['ok' => false, 'err' => 'besar'];
        }
        $in = json_decode($raw, true);
        if (!is_array($in)) {
            return ['ok' => false];
        }
        if (strlen($raw) > 65536 && empty($in['ext'])) {
            return ['ok' => false, 'err' => 'besar'];
        }

        // ---- kandidat mengirim jawaban (publik) ----
        if ($a === 'save') {
            if ($this->limited('save:' . $ip, 200, 3600)) {
                return ['ok' => false, 'err' => 'tunggu'];
            }
            $an = $this->alumniFrom($in['token'] ?? '');
            if ($an === null) {
                [$st, $er] = $this->me($in, 1);
                if ($er) {
                    return ['ok' => false, 'err' => 'auth'];
                }
            }
            // Hasil milik tim Super Admin dari akun alumni (kandidat) atau staf yang mengerjakan
            $tim = $an !== null ? (string) $this->q('SELECT team FROM alumni WHERE username = ?', [$an])->fetchColumn() : (string) ($st['tm'] ?? '');
            $test = in_array(($in['test'] ?? 'disc'), ['papi', 'kraepelin', 'iq', 'asesmen', 'ist', 'wpt'], true) ? $in['test'] : 'disc';
            if (!is_string($in['name'] ?? null) || trim($in['name']) === '') {
                return ['ok' => false];
            }
            if ($test === 'kraepelin') {
                $C = $in['C'] ?? null;
                if (!is_array($C) || count($C) < 1 || count($C) > 100) {
                    return ['ok' => false];
                }
                $cols = [];
                foreach ($C as $c) {
                    if (!is_array($c) || !is_string($c['d'] ?? null) || !is_string($c['a'] ?? null)) {
                        return ['ok' => false];
                    }
                    if (!preg_match('/^[0-9]{2,120}$/', $c['d']) || !preg_match('/^[0-9]{0,120}$/', $c['a']) || strlen($c['a']) > strlen($c['d']) - 1) {
                        return ['ok' => false];
                    }
                    $cols[] = ['d' => $c['d'], 'a' => $c['a']];
                }
                $payload = ['C' => $cols];
            } elseif ($test === 'iq' && !empty($in['ext'])) {
                $sc = $in['score'] ?? null;
                if (!is_numeric($sc) || (float) $sc < 40 || (float) $sc > 220) {
                    return ['ok' => false, 'err' => 'skor'];
                }
                $berkas = $this->simpanBuktiIq($in['file'] ?? null);
                if ($berkas === null) {
                    return ['ok' => false, 'err' => 'berkas'];
                }
                $payload = ['ext' => true, 'score' => (int) round((float) $sc), 'cat' => mb_substr(trim((string) ($in['cat'] ?? '')), 0, 60), 'file' => $berkas];
            } elseif ($test === 'iq') {
                if (!$this->validIntArr($in['A'] ?? null, 40, 4)) {
                    return ['ok' => false];
                }
                $edu = in_array($in['edu'] ?? '', IqKey::EDU, true) ? $in['edu'] : '';
                $fld = in_array($in['field'] ?? '', IqKey::FIELD, true) ? $in['field'] : '';
                $payload = ['A' => array_values($in['A']), 'edu' => $edu, 'field' => $fld];
            } elseif ($test === 'wpt') {
                $payload = $this->validWpt($in);
                if ($payload === null) {
                    return ['ok' => false];
                }
            } elseif ($test === 'ist') {
                $payload = $this->validIst($in);
                if ($payload === null) {
                    return ['ok' => false];
                }
            } elseif ($test === 'asesmen') {
                $payload = $this->validAsesmen($in);
                if ($payload === null) {
                    return ['ok' => false];
                }
            } elseif ($test === 'papi') {
                if (!$this->validIntArr($in['A'] ?? null, 90, 1)) {
                    return ['ok' => false];
                }
                $payload = ['A' => array_values($in['A'])];
            } else {
                if (!$this->validIntArr($in['P'] ?? null, 24, 3) || !$this->validIntArr($in['K'] ?? null, 24, 3)) {
                    return ['ok' => false];
                }
                $payload = ['P' => array_values($in['P']), 'K' => array_values($in['K'])];
            }
            $this->hit('save:' . $ip);
            $min = (isset($in['minutes']) && is_numeric($in['minutes'])) ? (float) $in['minutes'] : null;
            $this->q(
                'INSERT INTO results (test, name, age, sex, test_date, minutes, auto_end, payload, created_at, team) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                [
                    $test, mb_substr(trim($in['name']), 0, 100), mb_substr((string) ($in['age'] ?? ''), 0, 3),
                    mb_substr((string) ($in['sex'] ?? ''), 0, 20), mb_substr((string) ($in['date'] ?? ''), 0, 10),
                    $min, !empty($in['auto']) ? 1 : 0, json_encode($payload), $this->now(), $tim,
                ]
            );
            if ($test === 'iq' && !empty($payload['ext'])) {
                return ['ok' => true, 'iq' => ['ext' => true, 's' => $payload['score'], 'c' => $payload['cat']]];
            }
            if ($test === 'wpt') {
                return ['ok' => true, 'wpt' => WptKey::score($payload['A'])];
            }
            if ($test === 'ist') {
                return ['ok' => true, 'ist' => IstKey::score($payload['A'], $in['age'] ?? 0, $payload['edu'])];
            }
            return $test === 'iq' ? ['ok' => true, 'iq' => IqKey::score($payload['A'])] : ['ok' => true];
        }

        // ---- pendaftaran mandiri: menunggu persetujuan Super Admin ----
        if ($a === 'register') {
            // Pendaftaran mandiri ditutup: akun hanya dibuat Super Admin lewat Kelola pengguna.
            return ['ok' => false, 'err' => 'tutup'];
            if ((int) $this->q('SELECT COUNT(*) FROM users')->fetchColumn() === 0) {
                return ['ok' => false, 'err' => 'belum'];
            }
            if ($this->limited('reg:' . $ip, 10, 3600)) {
                return ['ok' => false, 'err' => 'tunggu'];
            }
            $this->hit('reg:' . $ip);
            $u = strtolower(trim((string) ($in['u'] ?? '')));
            if (!$this->diizinkan($u)) {
                return ['ok' => false, 'err' => 'daftar'];
            }
            $n = mb_substr(trim((string) ($in['n'] ?? '')), 0, 60);
            $p = (string) ($in['p'] ?? '');
            $r = (($in['r'] ?? '') === 'admin') ? 'admin' : 'employee';
            if (!preg_match(self::UN, $u)) {
                return ['ok' => false, 'err' => 'data'];
            }
            if (strlen($p) < 8) {
                return ['ok' => false, 'err' => 'pw'];
            }
            if ($this->q('SELECT 1 FROM users WHERE username = ?', [$u])->fetchColumn()) {
                return ['ok' => false, 'err' => 'ada'];
            }
            if ((int) $this->q('SELECT COUNT(*) FROM users WHERE pending = 1')->fetchColumn() >= 30) {
                return ['ok' => false, 'err' => 'penuh'];
            }
            usleep(300000);
            $this->q(
                'INSERT INTO users (username, name, role, pass_hash, active, pending, created_at) VALUES (?, ?, ?, ?, 0, 1, ?)',
                [$u, $n !== '' ? $n : $u, $r, password_hash($p, PASSWORD_DEFAULT), $this->now()]
            );
            $this->log($u . ' (pendaftar)', 'Mendaftar, meminta peran ' . $RN[$r]);
            return ['ok' => true];
        }

        // ---- akun Super Admin pertama ----
        if ($a === 'setup') {
            if ($this->ketat) {
                return ['ok' => false, 'err' => 'tutup'];
            }
            if ((int) $this->q('SELECT COUNT(*) FROM users')->fetchColumn() > 0) {
                return ['ok' => false, 'err' => 'sudah'];
            }
            $noKey = $this->key === '' || $this->key === 'GANTI-DENGAN-KUNCI-ANDA';
            if ($this->requireKey && $noKey) {
                return ['ok' => false, 'err' => 'kuncibelum'];
            }
            if ($this->limited('setup:' . $ip, 10, 3600)) {
                return ['ok' => false, 'err' => 'tunggu'];
            }
            if (!$noKey && !hash_equals($this->key, (string) ($in['key'] ?? ''))) {
                $this->hit('setup:' . $ip);
                usleep(700000);
                return ['ok' => false, 'err' => 'kunci'];
            }
            $u = strtolower(trim((string) ($in['u'] ?? '')));
            if (!$this->diizinkan($u)) {
                return ['ok' => false, 'err' => 'daftar'];
            }
            $n = mb_substr(trim((string) ($in['n'] ?? '')), 0, 60);
            $p = (string) ($in['p'] ?? '');
            if (!preg_match(self::UN, $u)) {
                return ['ok' => false, 'err' => 'data'];
            }
            if (strlen($p) < 8) {
                return ['ok' => false, 'err' => 'pw'];
            }
            $this->q(
                "INSERT INTO users (username, name, role, pass_hash, active, pending, created_at) VALUES (?, ?, 'super', ?, 1, 0, ?)",
                [$u, $n !== '' ? $n : $u, password_hash($p, PASSWORD_DEFAULT), $this->now()]
            );
            $this->log($u . ' (Super Admin)', 'Akun Super Admin pertama dibuat');
            return ['ok' => true];
        }

        // ---- masuk: peran + nama pengguna + password ----
        if ($a === 'lget') {
            if ($this->limited('lget:' . $ip, 300, 3600)) {
                return ['ok' => false, 'err' => 'tunggu'];
            }
            $this->hit('lget:' . $ip);
            $s = strtolower(trim((string) ($in['slug'] ?? '')));
            $r = $this->q('SELECT tests FROM links WHERE slug = ? AND active = 1', [$s])->fetchColumn();
            if ($r === false) {
                return ['ok' => false, 'err' => 'link'];
            }
            return ['ok' => true, 'tests' => array_values(array_intersect(self::ALAT, explode(',', (string) $r)))];
        }

        if ($a === 'alogin') {
            $n = strtolower(trim((string) ($in['u'] ?? '')));
            $p = (string) ($in['p'] ?? '');
            if ($this->limited('alogin:ip:' . $ip, 30, 900) || ($n !== '' && $this->limited('alogin:u:' . $n, 10, 900))) {
                usleep(300000);
                return ['ok' => false, 'err' => 'tunggu'];
            }
            $f = $this->q('SELECT * FROM alumni WHERE username = ?', [$n])->fetch(PDO::FETCH_ASSOC);
            if (!$f) {
                // toleransi penulisan: abaikan titik, strip, garis bawah, dan spasi
                $nn = preg_replace('/[^a-z0-9]/', '', $n);
                if ($nn !== '') {
                    $f = $this->q("SELECT * FROM alumni WHERE REPLACE(REPLACE(REPLACE(REPLACE(username, '.', ''), '-', ''), '_', ''), ' ', '') = ? LIMIT 1", [$nn])->fetch(PDO::FETCH_ASSOC);
                }
            }
            if (!$f || (int) $f['active'] !== 1) {
                $this->hit('alogin:ip:' . $ip);
                if ($n !== '') {
                    $this->hit('alogin:u:' . $n);
                }
                usleep(700000);
                return ['ok' => false, 'err' => 'alogin'];
            }
            $this->q('DELETE FROM attempts WHERE k = ?', ['alogin:u:' . $n]);
            // NIM tetap Super Admin hanya menentukan tim; nama kandidat diisi sendiri oleh kandidat
            $namaTampil = $this->q('SELECT 1 FROM users WHERE nim = ? LIMIT 1', [$f['username']])->fetchColumn() ? '' : $f['name'];
            $this->q('DELETE FROM alumni_sessions WHERE expires < ?', [time()]);
            $tk = bin2hex(random_bytes(24));
            $this->q('INSERT INTO alumni_sessions (token, username, expires) VALUES (?, ?, ?)', [$tk, $f['username'], time() + 8 * 3600]);
            return ['ok' => true, 'token' => $tk, 'name' => $namaTampil];
        }

        if ($a === 'login') {
            $role = (string) ($in['role'] ?? '');
            $u = strtolower(trim((string) ($in['u'] ?? '')));
            $p = (string) ($in['p'] ?? '');
            $f = $this->q('SELECT * FROM users WHERE username = ?', [$u])->fetch(PDO::FETCH_ASSOC);
            if ($this->limited('login:ip:' . $ip, 30, 900) || ($u !== '' && $this->limited('login:u:' . $u, 10, 900))) {
                usleep(300000);
                return ['ok' => false, 'err' => 'tunggu'];
            }
            $gagal = function () use ($u, $role, $RN, $ip) {
                $this->hit('login:ip:' . $ip);
                if ($u !== '') {
                    $this->hit('login:u:' . $u);
                }
                usleep(700000);
                $this->log(($u !== '' ? $u : '-') . ' (' . ($RN[$role] ?? '-') . ')', 'Gagal masuk');
                return ['ok' => false, 'err' => 'login'];
            };
            if (!$f || !password_verify($p, $f['pass_hash'])) {
                return $gagal();
            }
            if ((int) $f['pending'] === 1) {
                return ['ok' => false, 'err' => 'pending'];
            }
            if ((int) $f['active'] !== 1 || $f['role'] !== $role || !$this->diizinkan($u)) {
                return $gagal();
            }
            $this->q('DELETE FROM attempts WHERE k = ?', ['login:u:' . $u]);
            $this->q('DELETE FROM sessions WHERE expires < ?', [time()]);
            $tk = bin2hex(random_bytes(24));
            $this->q('INSERT INTO sessions (token, username, expires) VALUES (?, ?, ?)', [$tk, $f['username'], time() + 8 * 3600]);
            $this->log($f['username'] . ' (' . $RN[$f['role']] . ')', 'Masuk');
            return ['ok' => true, 'token' => $tk, 'must' => (int) $f['must_change'] === 1, 'user' => ['u' => $f['username'], 'n' => $f['name'], 'r' => $f['role'], 't' => $this->testsOf($f['role'], $f['tests']), 'nim' => $this->nimTim($f)]];
        }

        // ---- keluar ----
        if ($a === 'logout') {
            $tk = $in['token'] ?? '';
            if (is_string($tk) && preg_match('/^[a-f0-9]{48}$/', $tk)) {
                $s = $this->q('SELECT username FROM sessions WHERE token = ?', [$tk])->fetchColumn();
                if ($s) {
                    $this->log($s, 'Keluar');
                    $this->q('DELETE FROM sessions WHERE token = ?', [$tk]);
                }
            }
            return ['ok' => true];
        }

        // ---- butuh login ----
        $need = ['me' => 1, 'iqfile' => 1, 'list' => 1, 'act' => 1, 'info' => 2, 'savecfg' => 2, 'chpw' => 1, 'llist' => 2, 'lsave' => 2, 'ldel' => 2, 'clear' => 3, 'purge' => 3, 'alist' => 2, 'adel' => 2, 'aimport' => 2, 'users' => 3, 'saveuser' => 3, 'deluser' => 3, 'log' => 3];
        if (!isset($need[$a])) {
            return ['ok' => false];
        }
        [$u, $err] = $this->me($in, $need[$a]);
        if ($err) {
            return $err;
        }
        $mw = $this->whoMe($u);

        if ($a === 'me') {
            return ['ok' => true, 'user' => ['u' => $u['u'], 'n' => $u['n'], 'r' => $u['r'], 't' => $u['t'], 'nim' => $this->nimTim(['team' => $u['tm'], 'role' => $u['r'], 'username' => $u['u']])], 'must' => ($u['m'] ?? 0) === 1];
        }
        if ($a === 'act') {
            $this->log($mw, (string) ($in['m'] ?? ''));
            return ['ok' => true];
        }
        if ($a === 'info') {
            return ['ok' => true, 'lan' => $this->lanAddresses($scheme, (int) $port)];
        }

        if ($a === 'iqfile') {
            $nm = $in['name'] ?? '';
            if (!is_string($nm) || !preg_match('/^[a-f0-9]{32}\.(pdf|png|jpg)$/', $nm)) {
                return ['ok' => false];
            }
            if (!$this->q('SELECT 1 FROM results WHERE team = ? AND instr(payload, ?) > 0 LIMIT 1', [$u['tm'], $nm])->fetchColumn()) {
                return ['ok' => false, 'err' => 'izin'];
            }
            $f = $this->q('SELECT mime, data FROM iq_files WHERE name = ?', [$nm])->fetch(PDO::FETCH_ASSOC);
            if (!$f) {
                return ['ok' => false, 'err' => 'hilang'];
            }
            return ['ok' => true, 'mime' => $f['mime'], 'data' => $f['data']];
        }

        if ($a === 'list') {
            $rows = [];
            $last = [];
            foreach ($this->q('SELECT * FROM results WHERE archived = 0 AND team = ? ORDER BY id ASC', [$u['tm']])->fetchAll(PDO::FETCH_ASSOC) as $r) {
                $rec = [
                    'test' => $r['test'], 'name' => $r['name'], 'age' => $r['age'], 'sex' => $r['sex'], 'date' => $r['test_date'],
                    'minutes' => $r['minutes'] === null ? null : (float) $r['minutes'],
                    'auto' => (int) $r['auto_end'] === 1, 't' => $r['created_at'],
                ];
                $pl = json_decode($r['payload'], true);
                if (is_array($pl)) {
                    $rec = array_merge($rec, $pl);
                }
                if ($r['test'] === 'wpt' && isset($rec['A']) && is_array($rec['A'])) {
                    $rec['wpt'] = WptKey::score($rec['A']);
                    unset($rec['A']);
                }
                if ($r['test'] === 'ist' && isset($rec['A']) && is_array($rec['A'])) {
                    $rec['ist'] = IstKey::score($rec['A'], $r['age'], (string) ($rec['edu'] ?? ''));
                    unset($rec['A']);
                }
                if ($r['test'] === 'iq' && !empty($rec['ext'])) {
                    $rec['iq'] = ['ext' => true, 's' => (int) ($rec['score'] ?? 0), 'c' => (string) ($rec['cat'] ?? '')];
                } elseif ($r['test'] === 'iq' && isset($rec['A']) && is_array($rec['A'])) {
                    $rec['iq'] = IqKey::score($rec['A']);
                    $last[mb_strtolower(trim((string) $r['name']))] = ['s' => $rec['iq']['s'], 'a' => IqKey::ageGroup($r['age']), 'e' => (string) ($rec['edu'] ?? ''), 'f' => (string) ($rec['field'] ?? '')];
                }
                $rows[] = $rec;
            }
            return ['ok' => true, 'entries' => $rows, 'iqpool' => array_slice(array_values($last), 0, 5000)];
        }

        if ($a === 'savecfg') {
            $c = $in['cfg'] ?? null;
            if (!is_array($c)) {
                return ['ok' => false];
            }
            $k = is_array($c['krp'] ?? null) ? $c['krp'] : [];
            $cfg = [
                'disc' => $this->clampInt($c['disc'] ?? 10, 1, 180, 10),
                'papi' => $this->clampInt($c['papi'] ?? 30, 1, 180, 30),
                'asesmen' => $this->clampInt($c['asesmen'] ?? 60, 1, 240, 60),
                'wpt' => $this->clampInt($c['wpt'] ?? 12, 1, 120, 12),
                'krp' => [
                    'kolom' => $this->clampInt($k['kolom'] ?? 40, 1, 100, 40),
                    'angka' => $this->clampInt($k['angka'] ?? 60, 10, 120, 60),
                    'detik' => $this->clampInt($k['detik'] ?? 15, 5, 120, 15),
                ],
            ];
            $this->q("INSERT INTO settings (k, v) VALUES ('cfg', ?) ON DUPLICATE KEY UPDATE v = VALUES(v)", [json_encode($cfg)]);
            return ['ok' => true];
        }

        if ($a === 'clear') {
            $this->q('UPDATE results SET archived = 1 WHERE archived = 0 AND team = ?', [$u['tm']]);
            $this->log($mw, 'Mengarsipkan semua hasil');
            return ['ok' => true];
        }

        if ($a === 'chpw') {
            $old = (string) ($in['old'] ?? '');
            $new = (string) ($in['new'] ?? '');
            if ($this->limited('chpw:' . $u['u'], 10, 900)) {
                return ['ok' => false, 'err' => 'tunggu'];
            }
            $row = $this->q('SELECT pass_hash FROM users WHERE username = ?', [$u['u']])->fetch(PDO::FETCH_ASSOC);
            if (!$row || !password_verify($old, $row['pass_hash'])) {
                $this->hit('chpw:' . $u['u']);
                usleep(700000);
                return ['ok' => false, 'err' => 'login'];
            }
            if (strlen($new) < 8 || strlen($new) > 200 || $new === $old || $new === '12345678') {
                return ['ok' => false, 'err' => 'pw'];
            }
            $this->q('UPDATE users SET pass_hash = ?, must_change = 0 WHERE username = ?', [password_hash($new, PASSWORD_DEFAULT), $u['u']]);
            $this->q('DELETE FROM sessions WHERE username = ? AND token <> ?', [$u['u'], (string) ($in['token'] ?? '')]);
            $this->log($mw, 'Mengganti password sendiri');
            return ['ok' => true];
        }

        if ($a === 'llist') {
            $sql = 'SELECT slug, label, tests, active, owner FROM links WHERE ' . ($u['r'] === 'super' ? 'team = ?' : 'owner = ?') . ' ORDER BY created DESC LIMIT 500';
            $rows = $this->q($sql, [$u['r'] === 'super' ? $u['tm'] : $u['u']])->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as &$x) {
                $x['tests'] = array_values(array_intersect(self::ALAT, explode(',', (string) $x['tests'])));
                $x['active'] = (int) $x['active'] === 1;
            }
            return ['ok' => true, 'list' => $rows];
        }

        if ($a === 'ldel') {
            $s = strtolower(trim((string) ($in['slug'] ?? '')));
            $r = $this->q('SELECT owner, team FROM links WHERE slug = ?', [$s])->fetch(PDO::FETCH_ASSOC);
            if (!$r) {
                return ['ok' => false, 'err' => 'link'];
            }
            if ($r['owner'] !== $u['u'] && !($u['r'] === 'super' && (string) $r['team'] === $u['tm'])) {
                return ['ok' => false, 'err' => 'izin'];
            }
            $this->q('DELETE FROM links WHERE slug = ?', [$s]);
            $this->log($mw, 'Menghapus tautan ' . $s);
            return ['ok' => true];
        }

        if ($a === 'lsave') {
            $old = strtolower(trim((string) ($in['old'] ?? '')));
            $slug = strtolower(trim((string) ($in['slug'] ?? '')));
            $label = trim((string) ($in['label'] ?? ''));
            $label = $label === '' ? 'Tautan tes' : mb_substr($label, 0, 100);
            $tt = is_array($in['t'] ?? null) ? array_values(array_intersect($u['t'], array_filter($in['t'], 'is_string'))) : [];
            if (!$tt) {
                return ['ok' => false, 'err' => 'pilih'];
            }
            $on = !empty($in['on']) ? 1 : 0;
            if ($old !== '') {
                $r = $this->q('SELECT owner, team FROM links WHERE slug = ?', [$old])->fetch(PDO::FETCH_ASSOC);
                if (!$r) {
                    return ['ok' => false, 'err' => 'link'];
                }
                if ($r['owner'] !== $u['u'] && !($u['r'] === 'super' && (string) $r['team'] === $u['tm'])) {
                    return ['ok' => false, 'err' => 'izin'];
                }
            }
            if ($slug === '' && $old !== '') {
                $slug = $old;
            }
            if ($slug === '') {
                $abc = 'abcdefghjkmnpqrstuvwxyz23456789';
                do {
                    $slug = '';
                    for ($k = 0; $k < 6; $k++) {
                        $slug .= $abc[random_int(0, strlen($abc) - 1)];
                    }
                } while ($this->q('SELECT 1 FROM links WHERE slug = ?', [$slug])->fetchColumn());
            }
            $parts = explode('-', $slug);
            $cadang = $slug === 'admin' || preg_match('/^[dpki]+$/', $slug) || count(array_diff($parts, self::ALAT)) === 0;
            if (!preg_match('/^[a-z0-9-]{3,30}$/', $slug) || $cadang) {
                return ['ok' => false, 'err' => 'slug'];
            }
            if ($slug !== $old && $this->q('SELECT 1 FROM links WHERE slug = ?', [$slug])->fetchColumn()) {
                return ['ok' => false, 'err' => 'slugada'];
            }
            if ($old !== '') {
                $this->q('UPDATE links SET slug = ?, label = ?, tests = ?, active = ? WHERE slug = ?', [$slug, $label, implode(',', $tt), $on, $old]);
            } else {
                $this->q('INSERT INTO links (slug, label, tests, active, owner, created, team) VALUES (?, ?, ?, ?, ?, ?, ?)', [$slug, $label, implode(',', $tt), $on, $u['u'], time(), $u['tm']]);
            }
            $this->log($mw, ($old !== '' ? 'Mengubah' : 'Membuat') . ' tautan ' . $slug . ' (' . implode(', ', $tt) . ')');
            return ['ok' => true, 'slug' => $slug];
        }

        if ($a === 'alist') {
            $rows = $this->q('SELECT username, name FROM alumni WHERE team = ? ORDER BY name LIMIT 2000', [$u['tm']])->fetchAll(PDO::FETCH_ASSOC);
            return ['ok' => true, 'list' => $rows];
        }

        if ($a === 'adel') {
            $x = strtolower(trim((string) ($in['u'] ?? '')));
            if ($this->q('SELECT 1 FROM users WHERE nim = ? LIMIT 1', [$x])->fetchColumn()) {
                return ['ok' => false, 'err' => 'izin'];
            }
            $this->q('DELETE FROM alumni WHERE username = ? AND team = ?', [$x, $u['tm']]);
            $this->q('DELETE FROM alumni_sessions WHERE username = ? AND NOT EXISTS (SELECT 1 FROM alumni WHERE alumni.username = alumni_sessions.username)', [$x]);
            $this->log($mw, 'Menghapus akun alumni ' . mb_substr($x, 0, 30));
            return ['ok' => true];
        }

        if ($a === 'aimport') {
            $txt = (string) ($in['text'] ?? '');
            $lines = preg_split('/\r\n|\r|\n/', $txt);
            if (strlen($txt) > 60000 || count($lines) > 600) {
                return ['ok' => false, 'err' => 'besar'];
            }
            $n = 0;
            $bad = [];
            $made = [];
            $abc = 'abcdefghjkmnpqrstuvwxyz23456789';
            foreach ($lines as $i => $line) {
                $line = trim($line);
                if ($line === '') {
                    continue;
                }
                $c = array_map('trim', preg_split('/[;,\t]/', $line));
                $nim = strtolower($c[0] ?? '');
                $nm = $c[1] ?? '';
                $pw = $c[2] ?? '';
                if ($i === 0 && in_array($nim, ['nim', 'username', 'user'], true)) {
                    continue;
                }
                if (!preg_match('/^[a-z0-9._-]{3,30}$/', $nim) || $nm === '' || mb_strlen($nm) > 100 || ($pw !== '' && strlen($pw) < 8)) {
                    $bad[] = $i + 1;
                    continue;
                }
                $tm0 = $this->q('SELECT team FROM alumni WHERE username = ?', [$nim])->fetchColumn();
                if ($tm0 !== false && (string) $tm0 !== $u['tm']) {
                    $bad[] = $i + 1;
                    continue;
                }
                $ada = $tm0 !== false;
                if ($ada && $pw === '') {
                    $this->q('UPDATE alumni SET name = ?, active = 1 WHERE username = ?', [$nm, $nim]);
                } else {
                    if ($pw === '') {
                        for ($k = 0; $k < 8; $k++) {
                            $pw .= $abc[random_int(0, strlen($abc) - 1)];
                        }
                        $made[] = [$nim, $nm, $pw];
                    }
                    $this->q(
                        'INSERT INTO alumni (username, name, pass_hash, active, created, team) VALUES (?, ?, ?, 1, ?, ?) ON DUPLICATE KEY UPDATE name = VALUES(name), pass_hash = VALUES(pass_hash), active = 1',
                        [$nim, $nm, password_hash($pw, PASSWORD_DEFAULT), time(), $u['tm']]
                    );
                }
                $n++;
            }
            $this->log($mw, 'Impor akun alumni: ' . $n . ' akun');
            return ['ok' => true, 'n' => $n, 'bad' => $bad, 'made' => $made];
        }

        if ($a === 'purge') {
            $n = (int) $this->q('SELECT COUNT(*) FROM results WHERE archived = 1 AND team = ?', [$u['tm']])->fetchColumn();
            $this->q('DELETE FROM results WHERE archived = 1 AND team = ?', [$u['tm']]);
            $this->q('DELETE FROM iq_files WHERE NOT EXISTS (SELECT 1 FROM results WHERE instr(results.payload, iq_files.name) > 0)');
            $this->log($mw, 'Menghapus permanen ' . $n . ' hasil arsip');
            return ['ok' => true, 'n' => $n];
        }

        if ($a === 'users') {
            $rows = [];
            foreach ($this->q('SELECT username, name, role, active, pending, tests FROM users WHERE team = ? ORDER BY id ASC', [$u['tm']])->fetchAll(PDO::FETCH_ASSOC) as $v) {
                $rows[] = ['u' => $v['username'], 'n' => $v['name'], 'r' => $v['role'], 'on' => (int) $v['active'] === 1, 'pend' => (int) $v['pending'] === 1, 't' => $this->testsOf($v['role'], $v['tests'])];
            }
            return ['ok' => true, 'users' => $rows];
        }

        if ($a === 'saveuser') {
            $nu = strtolower(trim((string) ($in['u'] ?? '')));
            $n = mb_substr(trim((string) ($in['n'] ?? '')), 0, 60);
            $r = (string) ($in['r'] ?? '');
            $p = (string) ($in['p'] ?? '');
            $on = !empty($in['on']) ? 1 : 0;
            if (!preg_match(self::UN, $nu) || !isset($RANK[$r])) {
                return ['ok' => false, 'err' => 'data'];
            }
            $was = $this->q('SELECT * FROM users WHERE username = ?', [$nu])->fetch(PDO::FETCH_ASSOC);
            if ($was && (string) $was['team'] !== '' && (string) $was['team'] !== $u['tm']) {
                return ['ok' => false, 'err' => 'ada'];
            }
            if ($this->ketat) {
                // Akun baru hanya Admin; peran Super Admin/Employee tidak bisa diberikan atau diubah lewat sini
                if (!$was && $r !== 'admin') {
                    return ['ok' => false, 'err' => 'peran'];
                }
                if ($was && $r !== $was['role'] && !($r === 'admin' && $was['role'] === 'employee')) {
                    return ['ok' => false, 'err' => 'peran'];
                }
                if ($was && (int) $was['pending'] === 1 && $r !== 'admin') {
                    return ['ok' => false, 'err' => 'peran'];
                }
            }
            if (!$was) {
                if (!$this->diizinkan($nu)) {
                    return ['ok' => false, 'err' => 'daftar'];
                }
                if (strlen($p) < 8) {
                    return ['ok' => false, 'err' => 'pw'];
                }
                $this->q(
                    'INSERT INTO users (username, name, role, pass_hash, active, pending, created_at, team) VALUES (?, ?, ?, ?, ?, 0, ?, ?)',
                    [$nu, $n !== '' ? $n : $nu, $r, password_hash($p, PASSWORD_DEFAULT), $on, $this->now(), $u['tm']]
                );
                $this->log($mw, 'Mendaftarkan pengguna ' . $nu . ' (' . $RN[$r] . ')');
            } else {
                if ($p !== '' && strlen($p) < 8) {
                    return ['ok' => false, 'err' => 'pw'];
                }
                if ($was['role'] === 'super' && (int) $was['active'] === 1 && ($r !== 'super' || !$on) && $this->supers() <= 1) {
                    return ['ok' => false, 'err' => 'last'];
                }
                if ($p !== '') {
                    $this->q('UPDATE users SET name = ?, role = ?, active = ?, pending = 0, pass_hash = ? WHERE username = ?',
                        [$n !== '' ? $n : $nu, $r, $on, password_hash($p, PASSWORD_DEFAULT), $nu]);
                    if ($nu !== $u['u']) {
                        $this->q('DELETE FROM sessions WHERE username = ?', [$nu]);
                    }
                } else {
                    $this->q('UPDATE users SET name = ?, role = ?, active = ?, pending = 0 WHERE username = ?',
                        [$n !== '' ? $n : $nu, $r, $on, $nu]);
                }
                $this->q("UPDATE users SET team = ? WHERE username = ? AND team = ''", [$u['tm'], $nu]);
                if (!$on) {
                    $this->q('DELETE FROM sessions WHERE username = ?', [$nu]);
                }
                $this->log($mw, 'Mengubah pengguna ' . $nu . ' (' . $RN[$r] . ($on ? '' : ', nonaktif') . ($p !== '' ? ', password diubah' : '') . ')');
            }
            if (array_key_exists('t', $in)) {
                $tt = is_array($in['t']) ? array_values(array_intersect(self::ALAT, array_filter($in['t'], 'is_string'))) : [];
                $this->q('UPDATE users SET tests = ? WHERE username = ?', [implode(',', $tt), $nu]);
                $this->log($mw, 'Mengatur alat tes ' . $nu . ': ' . ($tt ? implode(', ', $tt) : '(kosong)'));
            }
            return ['ok' => true];
        }

        if ($a === 'deluser') {
            $du = strtolower(trim((string) ($in['u'] ?? '')));
            if ($du === $u['u']) {
                return ['ok' => false, 'err' => 'self'];
            }
            $w = $this->q('SELECT * FROM users WHERE username = ?', [$du])->fetch(PDO::FETCH_ASSOC);
            if (!$w || (string) $w['team'] !== $u['tm']) {
                return ['ok' => false, 'err' => 'data'];
            }
            if ($w['role'] === 'super' && (int) $w['active'] === 1 && $this->supers() <= 1) {
                return ['ok' => false, 'err' => 'last'];
            }
            $this->q('DELETE FROM users WHERE username = ?', [$du]);
            $this->q('DELETE FROM sessions WHERE username = ?', [$du]);
            $this->log($mw, 'Menghapus pengguna ' . $du);
            return ['ok' => true];
        }

        if ($a === 'log') {
            $rows = [];
            foreach ($this->q('SELECT created_at, who, msg FROM logs WHERE team = ? ORDER BY id DESC LIMIT 300', [$u['tm']])->fetchAll(PDO::FETCH_ASSOC) as $v) {
                $rows[] = ['t' => $v['created_at'], 'u' => $v['who'], 'm' => $v['msg']];
            }
            return ['ok' => true, 'log' => $rows];
        }

        return ['ok' => false];
    }
}
