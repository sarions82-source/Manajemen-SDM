<?php
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
require __DIR__ . '/config.php';
function out($d) { echo json_encode($d, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE); exit; }

$RANK = ['employee' => 1, 'admin' => 2, 'super' => 3];
$RN = ['employee' => 'Employee', 'admin' => 'Admin', 'super' => 'Super Admin'];
$KEY = isset($SETUP_KEY) ? $SETUP_KEY : '';

// ---- koneksi MySQL ----
if (!isset($DB_HOST, $DB_NAME, $DB_USER, $DB_PASS)) out(['ok' => false, 'err' => 'db']);
try {
  $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
  ]);
} catch (Throwable $e) { out(['ok' => false, 'err' => 'db']); }

// ---- buat tabel otomatis saat pertama kali ----
try { $pdo->query('SELECT 1 FROM psi_users LIMIT 1'); }
catch (Throwable $e) {
  try {
    $sqls = file_get_contents(__DIR__ . '/install.sql');
    foreach (array_filter(array_map('trim', explode(';', preg_replace('/^--.*$/m', '', $sqls)))) as $s) $pdo->exec($s);
  } catch (Throwable $e2) { out(['ok' => false, 'err' => 'db']); }
}

function q($sql, $args = []) { global $pdo; $st = $pdo->prepare($sql); $st->execute($args); return $st; }
function now() { return gmdate('Y-m-d H:i:s'); }
function iso($d) { return str_replace(' ', 'T', $d) . 'Z'; }
function logit($who, $msg) { q('INSERT INTO psi_logs (created_at, who, msg) VALUES (?, ?, ?)', [now(), mb_substr((string)$who, 0, 100), mb_substr((string)$msg, 0, 255)]); }
function whoMe($m) { global $RN; return $m['u'] . ' (' . $RN[$m['r']] . ')'; }
function supers() { return (int)q("SELECT COUNT(*) FROM psi_users WHERE role = 'super' AND active = 1")->fetchColumn(); }
function me($in, $min) {
  global $RANK;
  $tk = $in['token'] ?? '';
  if (!is_string($tk) || !preg_match('/^[a-f0-9]{48}$/', $tk)) { usleep(300000); out(['ok' => false, 'err' => 'auth']); }
  $r = q('SELECT u.username, u.name, u.role FROM psi_sessions s JOIN psi_users u ON u.username = s.username WHERE s.token = ? AND s.expires > ? AND u.active = 1', [$tk, time()])->fetch();
  if (!$r) out(['ok' => false, 'err' => 'auth']);
  if ($RANK[$r['role']] < $min) out(['ok' => false, 'err' => 'izin']);
  return ['u' => $r['username'], 'n' => $r['name'], 'r' => $r['role']];
}
function validIntArr($x, $n, $max) {
  if (!is_array($x) || count($x) !== $n) return false;
  foreach ($x as $v) if (!is_int($v) || $v < -1 || $v > $max) return false;
  return true;
}
function clampInt($v, $lo, $hi, $d) { $v = (int)round((float)$v); return ($v >= $lo && $v <= $hi) ? $v : $d; }

$a = $_GET['a'] ?? '';

// ---- publik (tanpa body) ----
if ($a === 'cfg') {
  $v = q("SELECT v FROM psi_settings WHERE k = 'cfg'")->fetchColumn();
  out(['ok' => true, 'cfg' => $v ? json_decode($v, true) : null]);
}
if ($a === 'status') {
  $c = ['super' => 0, 'admin' => 0, 'employee' => 0]; $total = 0;
  foreach (q('SELECT role, active FROM psi_users')->fetchAll() as $v) { $total++; if ((int)$v['active'] === 1) $c[$v['role']]++; }
  out(['ok' => true, 'setup' => $total === 0, 'roles' => $c]);
}

$in = json_decode(file_get_contents('php://input', false, null, 0, 65536), true);
if (!is_array($in)) out(['ok' => false]);

// ---- kandidat mengirim jawaban (publik) ----
if ($a === 'save') {
  $test = in_array(($in['test'] ?? 'disc'), ['papi', 'kraepelin'], true) ? $in['test'] : 'disc';
  if (!is_string($in['name'] ?? null) || trim($in['name']) === '') out(['ok' => false]);
  if ($test === 'kraepelin') {
    $C = $in['C'] ?? null;
    if (!is_array($C) || count($C) < 1 || count($C) > 100) out(['ok' => false]);
    $cols = [];
    foreach ($C as $c) {
      if (!is_array($c) || !is_string($c['d'] ?? null) || !is_string($c['a'] ?? null)) out(['ok' => false]);
      if (!preg_match('/^[0-9]{2,120}$/', $c['d']) || !preg_match('/^[0-9]{0,120}$/', $c['a']) || strlen($c['a']) > strlen($c['d']) - 1) out(['ok' => false]);
      $cols[] = ['d' => $c['d'], 'a' => $c['a']];
    }
    $payload = ['C' => $cols];
  } elseif ($test === 'papi') {
    if (!validIntArr($in['A'] ?? null, 90, 1)) out(['ok' => false]);
    $payload = ['A' => array_values($in['A'])];
  } else {
    if (!validIntArr($in['P'] ?? null, 24, 3) || !validIntArr($in['K'] ?? null, 24, 3)) out(['ok' => false]);
    $payload = ['P' => array_values($in['P']), 'K' => array_values($in['K'])];
  }
  $min = (isset($in['minutes']) && is_numeric($in['minutes'])) ? (float)$in['minutes'] : null;
  q('INSERT INTO psi_results (test, name, age, sex, test_date, minutes, auto_end, payload, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)', [
    $test, mb_substr(trim($in['name']), 0, 100), mb_substr((string)($in['age'] ?? ''), 0, 3), mb_substr((string)($in['sex'] ?? ''), 0, 20),
    mb_substr((string)($in['date'] ?? ''), 0, 10), $min, !empty($in['auto']) ? 1 : 0, json_encode($payload), now(),
  ]);
  out(['ok' => true]);
}

// ---- pendaftaran mandiri: menunggu persetujuan Super Admin ----
if ($a === 'register') {
  if ((int)q('SELECT COUNT(*) FROM psi_users')->fetchColumn() === 0) out(['ok' => false, 'err' => 'belum']);
  $u = strtolower(trim((string)($in['u'] ?? ''))); $n = mb_substr(trim((string)($in['n'] ?? '')), 0, 60); $p = (string)($in['p'] ?? '');
  $r = (($in['r'] ?? '') === 'admin') ? 'admin' : 'employee';
  if (!preg_match('/^[a-z0-9_.-]{3,30}$/', $u)) out(['ok' => false, 'err' => 'data']);
  if (strlen($p) < 6) out(['ok' => false, 'err' => 'pw']);
  if (q('SELECT 1 FROM psi_users WHERE username = ?', [$u])->fetchColumn()) out(['ok' => false, 'err' => 'ada']);
  if ((int)q('SELECT COUNT(*) FROM psi_users WHERE pending = 1')->fetchColumn() >= 30) out(['ok' => false, 'err' => 'penuh']);
  usleep(300000);
  q('INSERT INTO psi_users (username, name, role, pass_hash, active, pending, created_at) VALUES (?, ?, ?, ?, 0, 1, ?)', [$u, $n !== '' ? $n : $u, $r, password_hash($p, PASSWORD_DEFAULT), now()]);
  logit($u . ' (pendaftar)', 'Mendaftar, meminta peran ' . $RN[$r]);
  out(['ok' => true]);
}

// ---- akun Super Admin pertama ----
if ($a === 'setup') {
  if ((int)q('SELECT COUNT(*) FROM psi_users')->fetchColumn() > 0) out(['ok' => false, 'err' => 'sudah']);
  if ($KEY === '' || $KEY === 'GANTI-DENGAN-KUNCI-ANDA' || !hash_equals($KEY, (string)($in['key'] ?? ''))) { usleep(700000); out(['ok' => false, 'err' => 'kunci']); }
  $u = strtolower(trim((string)($in['u'] ?? ''))); $n = mb_substr(trim((string)($in['n'] ?? '')), 0, 60); $p = (string)($in['p'] ?? '');
  if (!preg_match('/^[a-z0-9_.-]{3,30}$/', $u)) out(['ok' => false, 'err' => 'data']);
  if (strlen($p) < 6) out(['ok' => false, 'err' => 'pw']);
  q("INSERT INTO psi_users (username, name, role, pass_hash, active, pending, created_at) VALUES (?, ?, 'super', ?, 1, 0, ?)", [$u, $n !== '' ? $n : $u, password_hash($p, PASSWORD_DEFAULT), now()]);
  logit($u . ' (Super Admin)', 'Akun Super Admin pertama dibuat');
  out(['ok' => true]);
}

// ---- masuk: peran + nama pengguna + password ----
if ($a === 'login') {
  $role = (string)($in['role'] ?? ''); $u = strtolower(trim((string)($in['u'] ?? ''))); $p = (string)($in['p'] ?? '');
  $f = q('SELECT * FROM psi_users WHERE username = ?', [$u])->fetch();
  $gagal = function() use ($u, $role, $RN) { usleep(700000); logit(($u !== '' ? $u : '-') . ' (' . ($RN[$role] ?? '-') . ')', 'Gagal masuk'); out(['ok' => false, 'err' => 'login']); };
  if (!$f || !password_verify($p, $f['pass_hash'])) $gagal();
  if ((int)$f['pending'] === 1) out(['ok' => false, 'err' => 'pending']);
  if ((int)$f['active'] !== 1 || $f['role'] !== $role) $gagal();
  q('DELETE FROM psi_sessions WHERE expires < ?', [time()]);
  $tk = bin2hex(random_bytes(24));
  q('INSERT INTO psi_sessions (token, username, expires) VALUES (?, ?, ?)', [$tk, $f['username'], time() + 8 * 3600]);
  logit($f['username'] . ' (' . $RN[$f['role']] . ')', 'Masuk');
  out(['ok' => true, 'token' => $tk, 'user' => ['u' => $f['username'], 'n' => $f['name'], 'r' => $f['role']]]);
}

// ---- keluar ----
if ($a === 'logout') {
  $tk = $in['token'] ?? '';
  if (is_string($tk) && preg_match('/^[a-f0-9]{48}$/', $tk)) {
    $s = q('SELECT username FROM psi_sessions WHERE token = ?', [$tk])->fetchColumn();
    if ($s) { logit($s, 'Keluar'); q('DELETE FROM psi_sessions WHERE token = ?', [$tk]); }
  }
  out(['ok' => true]);
}

// ---- butuh login ----
if ($a === 'me') { out(['ok' => true, 'user' => me($in, 1)]); }

if ($a === 'list') {
  me($in, 1); $rows = [];
  foreach (q('SELECT * FROM psi_results WHERE archived = 0 ORDER BY id ASC')->fetchAll() as $r) {
    $rec = ['test' => $r['test'], 'name' => $r['name'], 'age' => $r['age'], 'sex' => $r['sex'], 'date' => $r['test_date'],
      'minutes' => $r['minutes'] === null ? null : (float)$r['minutes'], 'auto' => (int)$r['auto_end'] === 1, 't' => iso($r['created_at'])];
    $pl = json_decode($r['payload'], true);
    if (is_array($pl)) $rec = array_merge($rec, $pl);
    $rows[] = $rec;
  }
  out(['ok' => true, 'entries' => $rows]);
}

if ($a === 'act') { $m = me($in, 1); logit(whoMe($m), (string)($in['m'] ?? '')); out(['ok' => true]); }

if ($a === 'savecfg') {
  me($in, 2); $c = $in['cfg'] ?? null;
  if (!is_array($c)) out(['ok' => false]);
  $k = is_array($c['krp'] ?? null) ? $c['krp'] : [];
  $cfg = ['disc' => clampInt($c['disc'] ?? 10, 1, 180, 10), 'papi' => clampInt($c['papi'] ?? 30, 1, 180, 30),
    'krp' => ['kolom' => clampInt($k['kolom'] ?? 40, 1, 100, 40), 'angka' => clampInt($k['angka'] ?? 60, 10, 120, 60), 'detik' => clampInt($k['detik'] ?? 15, 5, 120, 15)]];
  q("INSERT INTO psi_settings (k, v) VALUES ('cfg', ?) ON DUPLICATE KEY UPDATE v = VALUES(v)", [json_encode($cfg)]);
  out(['ok' => true]);
}

if ($a === 'clear') {
  $m = me($in, 3);
  q('UPDATE psi_results SET archived = 1 WHERE archived = 0');
  logit(whoMe($m), 'Mengarsipkan semua hasil');
  out(['ok' => true]);
}

if ($a === 'users') {
  me($in, 3); $rows = [];
  foreach (q('SELECT username, name, role, active, pending FROM psi_users ORDER BY id ASC')->fetchAll() as $v)
    $rows[] = ['u' => $v['username'], 'n' => $v['name'], 'r' => $v['role'], 'on' => (int)$v['active'] === 1, 'pend' => (int)$v['pending'] === 1];
  out(['ok' => true, 'users' => $rows]);
}

if ($a === 'saveuser') {
  $m = me($in, 3);
  $u = strtolower(trim((string)($in['u'] ?? ''))); $n = mb_substr(trim((string)($in['n'] ?? '')), 0, 60);
  $r = (string)($in['r'] ?? ''); $p = (string)($in['p'] ?? ''); $on = !empty($in['on']) ? 1 : 0;
  if (!preg_match('/^[a-z0-9_.-]{3,30}$/', $u) || !isset($RANK[$r])) out(['ok' => false, 'err' => 'data']);
  $was = q('SELECT * FROM psi_users WHERE username = ?', [$u])->fetch();
  if (!$was) {
    if (strlen($p) < 6) out(['ok' => false, 'err' => 'pw']);
    q('INSERT INTO psi_users (username, name, role, pass_hash, active, pending, created_at) VALUES (?, ?, ?, ?, ?, 0, ?)', [$u, $n !== '' ? $n : $u, $r, password_hash($p, PASSWORD_DEFAULT), $on, now()]);
    logit(whoMe($m), "Mendaftarkan pengguna $u ({$RN[$r]})");
  } else {
    if ($p !== '' && strlen($p) < 6) out(['ok' => false, 'err' => 'pw']);
    if ($was['role'] === 'super' && (int)$was['active'] === 1 && ($r !== 'super' || !$on) && supers() <= 1) out(['ok' => false, 'err' => 'last']);
    if ($p !== '') q('UPDATE psi_users SET name = ?, role = ?, active = ?, pending = 0, pass_hash = ? WHERE username = ?', [$n !== '' ? $n : $u, $r, $on, password_hash($p, PASSWORD_DEFAULT), $u]);
    else q('UPDATE psi_users SET name = ?, role = ?, active = ?, pending = 0 WHERE username = ?', [$n !== '' ? $n : $u, $r, $on, $u]);
    if (!$on) q('DELETE FROM psi_sessions WHERE username = ?', [$u]);
    logit(whoMe($m), "Mengubah pengguna $u ({$RN[$r]}" . ($on ? '' : ', nonaktif') . ($p !== '' ? ', password diubah' : '') . ')');
  }
  out(['ok' => true]);
}

if ($a === 'deluser') {
  $m = me($in, 3); $u = strtolower(trim((string)($in['u'] ?? '')));
  if ($u === $m['u']) out(['ok' => false, 'err' => 'self']);
  $w = q('SELECT * FROM psi_users WHERE username = ?', [$u])->fetch();
  if (!$w) out(['ok' => false, 'err' => 'data']);
  if ($w['role'] === 'super' && (int)$w['active'] === 1 && supers() <= 1) out(['ok' => false, 'err' => 'last']);
  q('DELETE FROM psi_users WHERE username = ?', [$u]);
  q('DELETE FROM psi_sessions WHERE username = ?', [$u]);
  logit(whoMe($m), "Menghapus pengguna $u");
  out(['ok' => true]);
}

if ($a === 'log') {
  me($in, 3); $rows = [];
  foreach (q('SELECT created_at, who, msg FROM psi_logs ORDER BY id DESC LIMIT 300')->fetchAll() as $v)
    $rows[] = ['t' => iso($v['created_at']), 'u' => $v['who'], 'm' => $v['msg']];
  out(['ok' => true, 'log' => $rows]);
}

out(['ok' => false]);
