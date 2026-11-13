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

    public function __construct(PDO $pdo, $setupKey = '', $requireKey = false)
    {
        $this->pdo = $pdo;
        $this->key = (string) $setupKey;
        $this->requireKey = (bool) $requireKey;
    }

    /** Membuat tabel jika belum ada. */
    public function ensureSchema()
    {
        try {
            $this->pdo->query('PRAGMA journal_mode = WAL')->fetchAll();
            $this->pdo->exec('PRAGMA busy_timeout = 5000');
        } catch (\Throwable $e) {
            // abaikan: tidak semua lingkungan mendukung
        }
        $ada = $this->q("SELECT 1 FROM sqlite_master WHERE type = 'table' AND name = 'users'")->fetchColumn();
        if (!$ada) {
            $this->pdo->exec(Schema::SQL);
        }
        if (!$this->q("SELECT 1 FROM sqlite_master WHERE type = 'table' AND name = 'attempts'")->fetchColumn()) {
            $this->pdo->exec('CREATE TABLE IF NOT EXISTS attempts (id INTEGER PRIMARY KEY AUTOINCREMENT, k TEXT NOT NULL, t INTEGER NOT NULL); CREATE INDEX IF NOT EXISTS idx_attempts_k ON attempts (k, t);');
        }
        $kol = array_column($this->q('PRAGMA table_info(users)')->fetchAll(PDO::FETCH_ASSOC), 'name');
        if (!in_array('must_change', $kol, true)) {
            $this->pdo->exec('ALTER TABLE users ADD COLUMN must_change INTEGER NOT NULL DEFAULT 0');
        }
        if (!in_array('tests', $kol, true)) {
            $this->pdo->exec("ALTER TABLE users ADD COLUMN tests TEXT NOT NULL DEFAULT ''");
        }
        if (!$this->q("SELECT 1 FROM sqlite_master WHERE type = 'table' AND name = 'alumni'")->fetchColumn()) {
            $this->pdo->exec('CREATE TABLE IF NOT EXISTS alumni (username TEXT PRIMARY KEY, name TEXT NOT NULL, pass_hash TEXT NOT NULL, active INTEGER NOT NULL DEFAULT 1, created INTEGER NOT NULL); CREATE TABLE IF NOT EXISTS alumni_sessions (token TEXT PRIMARY KEY, username TEXT NOT NULL, expires INTEGER NOT NULL);');
        }
    }

    /** Pembatas percobaan: true bila kunci $key sudah mencapai $max dalam $win detik. */
    private function limited($key, $max, $win)
    {
        return (int) $this->q('SELECT COUNT(*) FROM attempts WHERE k = ? AND t > ?', [$key, time() - $win])->fetchColumn() >= $max;
    }

    private const ALAT = ['disc', 'papi', 'kraepelin'];

    /** Alat tes yang boleh dibagikan: Super Admin semuanya, peran lain sesuai pemberian Super Admin. */
    private function testsOf($role, $csv)
    {
        if ($role === 'super') {
            return self::ALAT;
        }
        return array_values(array_intersect(self::ALAT, array_filter(explode(',', (string) $csv))));
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
        $this->q('INSERT INTO logs (created_at, who, msg) VALUES (?, ?, ?)', [
            $this->now(), mb_substr((string) $who, 0, 100), mb_substr((string) $msg, 0, 255),
        ]);
        if (random_int(1, 50) === 1) {
            // simpan 5000 log terbaru saja agar database tidak membengkak
            $this->q('DELETE FROM logs WHERE id <= (SELECT MAX(id) FROM logs) - 5000');
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
            'SELECT u.username, u.name, u.role, u.must_change, u.tests FROM sessions s JOIN users u ON u.username = s.username WHERE s.token = ? AND s.expires > ? AND u.active = 1',
            [$tk, time()]
        )->fetch(PDO::FETCH_ASSOC);
        if (!$r) {
            return [null, ['ok' => false, 'err' => 'auth']];
        }
        if ((int) $r['must_change'] === 1 && !in_array($this->cur, ['me', 'chpw'], true)) {
            return [null, ['ok' => false, 'err' => 'ganti']];
        }
        if (self::RANK[$r['role']] < $min) {
            return [null, ['ok' => false, 'err' => 'izin']];
        }
        return [['u' => $r['username'], 'n' => $r['name'], 'r' => $r['role'], 'm' => (int) $r['must_change'], 't' => $this->testsOf($r['role'], $r['tests'])], null];
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
            return ['ok' => true, 'setup' => $total === 0, 'roles' => $c];
        }
        if ($method !== 'POST') {
            return ['ok' => false];
        }
        if (strlen($raw) > 65536) {
            return ['ok' => false];
        }
        $in = json_decode($raw, true);
        if (!is_array($in)) {
            return ['ok' => false];
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
            $test = in_array(($in['test'] ?? 'disc'), ['papi', 'kraepelin'], true) ? $in['test'] : 'disc';
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
                'INSERT INTO results (test, name, age, sex, test_date, minutes, auto_end, payload, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
                [
                    $test, mb_substr(trim($in['name']), 0, 100), mb_substr((string) ($in['age'] ?? ''), 0, 3),
                    mb_substr((string) ($in['sex'] ?? ''), 0, 20), mb_substr((string) ($in['date'] ?? ''), 0, 10),
                    $min, !empty($in['auto']) ? 1 : 0, json_encode($payload), $this->now(),
                ]
            );
            return ['ok' => true];
        }

        // ---- pendaftaran mandiri: menunggu persetujuan Super Admin ----
        if ($a === 'register') {
            if ((int) $this->q('SELECT COUNT(*) FROM users')->fetchColumn() === 0) {
                return ['ok' => false, 'err' => 'belum'];
            }
            if ($this->limited('reg:' . $ip, 10, 3600)) {
                return ['ok' => false, 'err' => 'tunggu'];
            }
            $this->hit('reg:' . $ip);
            $u = strtolower(trim((string) ($in['u'] ?? '')));
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
        if ($a === 'alogin') {
            $n = strtolower(trim((string) ($in['u'] ?? '')));
            $p = (string) ($in['p'] ?? '');
            if ($this->limited('alogin:ip:' . $ip, 30, 900) || ($n !== '' && $this->limited('alogin:u:' . $n, 10, 900))) {
                usleep(300000);
                return ['ok' => false, 'err' => 'tunggu'];
            }
            $f = $this->q('SELECT * FROM alumni WHERE username = ?', [$n])->fetch(PDO::FETCH_ASSOC);
            if (!$f || (int) $f['active'] !== 1 || !password_verify($p, $f['pass_hash'])) {
                $this->hit('alogin:ip:' . $ip);
                if ($n !== '') {
                    $this->hit('alogin:u:' . $n);
                }
                usleep(700000);
                return ['ok' => false, 'err' => 'alogin'];
            }
            $this->q('DELETE FROM attempts WHERE k = ?', ['alogin:u:' . $n]);
            $this->q('DELETE FROM alumni_sessions WHERE expires < ?', [time()]);
            $tk = bin2hex(random_bytes(24));
            $this->q('INSERT INTO alumni_sessions (token, username, expires) VALUES (?, ?, ?)', [$tk, $f['username'], time() + 8 * 3600]);
            return ['ok' => true, 'token' => $tk, 'name' => $f['name']];
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
            if ((int) $f['active'] !== 1 || $f['role'] !== $role) {
                return $gagal();
            }
            $this->q('DELETE FROM attempts WHERE k = ?', ['login:u:' . $u]);
            $this->q('DELETE FROM sessions WHERE expires < ?', [time()]);
            $tk = bin2hex(random_bytes(24));
            $this->q('INSERT INTO sessions (token, username, expires) VALUES (?, ?, ?)', [$tk, $f['username'], time() + 8 * 3600]);
            $this->log($f['username'] . ' (' . $RN[$f['role']] . ')', 'Masuk');
            return ['ok' => true, 'token' => $tk, 'must' => (int) $f['must_change'] === 1, 'user' => ['u' => $f['username'], 'n' => $f['name'], 'r' => $f['role'], 't' => $this->testsOf($f['role'], $f['tests'])]];
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
        $need = ['me' => 1, 'list' => 1, 'act' => 1, 'info' => 2, 'savecfg' => 2, 'chpw' => 1, 'clear' => 3, 'purge' => 3, 'alist' => 2, 'adel' => 2, 'aimport' => 2, 'users' => 3, 'saveuser' => 3, 'deluser' => 3, 'log' => 3];
        if (!isset($need[$a])) {
            return ['ok' => false];
        }
        [$u, $err] = $this->me($in, $need[$a]);
        if ($err) {
            return $err;
        }
        $mw = $this->whoMe($u);

        if ($a === 'me') {
            return ['ok' => true, 'user' => ['u' => $u['u'], 'n' => $u['n'], 'r' => $u['r'], 't' => $u['t']], 'must' => ($u['m'] ?? 0) === 1];
        }
        if ($a === 'act') {
            $this->log($mw, (string) ($in['m'] ?? ''));
            return ['ok' => true];
        }
        if ($a === 'info') {
            return ['ok' => true, 'lan' => $this->lanAddresses($scheme, (int) $port)];
        }

        if ($a === 'list') {
            $rows = [];
            foreach ($this->q('SELECT * FROM results WHERE archived = 0 ORDER BY id ASC')->fetchAll(PDO::FETCH_ASSOC) as $r) {
                $rec = [
                    'test' => $r['test'], 'name' => $r['name'], 'age' => $r['age'], 'sex' => $r['sex'], 'date' => $r['test_date'],
                    'minutes' => $r['minutes'] === null ? null : (float) $r['minutes'],
                    'auto' => (int) $r['auto_end'] === 1, 't' => $r['created_at'],
                ];
                $pl = json_decode($r['payload'], true);
                if (is_array($pl)) {
                    $rec = array_merge($rec, $pl);
                }
                $rows[] = $rec;
            }
            return ['ok' => true, 'entries' => $rows];
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
                'krp' => [
                    'kolom' => $this->clampInt($k['kolom'] ?? 40, 1, 100, 40),
                    'angka' => $this->clampInt($k['angka'] ?? 60, 10, 120, 60),
                    'detik' => $this->clampInt($k['detik'] ?? 15, 5, 120, 15),
                ],
            ];
            $this->q("INSERT INTO settings (k, v) VALUES ('cfg', ?) ON CONFLICT(k) DO UPDATE SET v = excluded.v", [json_encode($cfg)]);
            return ['ok' => true];
        }

        if ($a === 'clear') {
            $this->q('UPDATE results SET archived = 1 WHERE archived = 0');
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

        if ($a === 'alist') {
            $rows = $this->q('SELECT username, name FROM alumni ORDER BY name LIMIT 2000')->fetchAll(PDO::FETCH_ASSOC);
            return ['ok' => true, 'list' => $rows];
        }

        if ($a === 'adel') {
            $x = strtolower(trim((string) ($in['u'] ?? '')));
            $this->q('DELETE FROM alumni WHERE username = ?', [$x]);
            $this->q('DELETE FROM alumni_sessions WHERE username = ?', [$x]);
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
                $ada = (bool) $this->q('SELECT 1 FROM alumni WHERE username = ?', [$nim])->fetchColumn();
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
                        'INSERT INTO alumni (username, name, pass_hash, active, created) VALUES (?, ?, ?, 1, ?) ON CONFLICT(username) DO UPDATE SET name = excluded.name, pass_hash = excluded.pass_hash, active = 1',
                        [$nim, $nm, password_hash($pw, PASSWORD_DEFAULT), time()]
                    );
                }
                $n++;
            }
            $this->log($mw, 'Impor akun alumni: ' . $n . ' akun');
            return ['ok' => true, 'n' => $n, 'bad' => $bad, 'made' => $made];
        }

        if ($a === 'purge') {
            $n = (int) $this->q('SELECT COUNT(*) FROM results WHERE archived = 1')->fetchColumn();
            $this->q('DELETE FROM results WHERE archived = 1');
            $this->log($mw, 'Menghapus permanen ' . $n . ' hasil arsip');
            return ['ok' => true, 'n' => $n];
        }

        if ($a === 'users') {
            $rows = [];
            foreach ($this->q('SELECT username, name, role, active, pending, tests FROM users ORDER BY id ASC')->fetchAll(PDO::FETCH_ASSOC) as $v) {
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
            if (!$was) {
                if (strlen($p) < 8) {
                    return ['ok' => false, 'err' => 'pw'];
                }
                $this->q(
                    'INSERT INTO users (username, name, role, pass_hash, active, pending, created_at) VALUES (?, ?, ?, ?, ?, 0, ?)',
                    [$nu, $n !== '' ? $n : $nu, $r, password_hash($p, PASSWORD_DEFAULT), $on, $this->now()]
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
            if (!$w) {
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
            foreach ($this->q('SELECT created_at, who, msg FROM logs ORDER BY id DESC LIMIT 300')->fetchAll(PDO::FETCH_ASSOC) as $v) {
                $rows[] = ['t' => $v['created_at'], 'u' => $v['who'], 'm' => $v['msg']];
            }
            return ['ok' => true, 'log' => $rows];
        }

        return ['ok' => false];
    }
}
