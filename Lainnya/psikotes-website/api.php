<?php
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
require __DIR__ . '/config.php';
function out($d){ echo json_encode($d, JSON_UNESCAPED_UNICODE); exit; }
$dir = __DIR__ . '/data';
if (!is_dir($dir)) { @mkdir($dir, 0755, true); }
$file = $dir . '/results.jsonl';
$a = $_GET['a'] ?? '';
$in = json_decode(file_get_contents('php://input', false, null, 0, 65536), true);
if (!is_array($in)) out(['ok' => false]);
function auth($in) {
  global $ADMIN_PASSWORD;
  $ok = $ADMIN_PASSWORD !== 'GANTI-DENGAN-PASSWORD-ANDA' && isset($in['password']) && is_string($in['password']) && hash_equals($ADMIN_PASSWORD, $in['password']);
  if (!$ok) { usleep(700000); out(['ok' => false]); }
}
if ($a === 'save') {
  $valid = function($x, $n, $max) {
    if (!is_array($x) || count($x) !== $n) return false;
    foreach ($x as $v) if (!is_int($v) || $v < -1 || $v > $max) return false;
    return true;
  };
  $test = in_array(($in['test'] ?? 'disc'), ['papi', 'kraepelin'], true) ? $in['test'] : 'disc';
  if (!is_string($in['name'] ?? null) || trim($in['name']) === '') out(['ok' => false]);
  $rec = [
    'test' => $test,
    'name' => mb_substr(trim($in['name']), 0, 100), 'age' => mb_substr((string)($in['age'] ?? ''), 0, 3),
    'sex' => mb_substr((string)($in['sex'] ?? ''), 0, 20), 'date' => mb_substr((string)($in['date'] ?? ''), 0, 10),
    'minutes' => is_numeric($in['minutes'] ?? null) ? (float)$in['minutes'] : null,
    'auto' => !empty($in['auto']), 't' => gmdate('c'),
  ];
  if ($test === 'kraepelin') {
    $C = $in['C'] ?? null;
    if (!is_array($C) || count($C) < 1 || count($C) > 100) out(['ok' => false]);
    $cols = [];
    foreach ($C as $c) {
      if (!is_array($c) || !is_string($c['d'] ?? null) || !is_string($c['a'] ?? null)) out(['ok' => false]);
      if (!preg_match('/^[0-9]{2,120}$/', $c['d']) || !preg_match('/^[0-9]{0,120}$/', $c['a']) || strlen($c['a']) > strlen($c['d']) - 1) out(['ok' => false]);
      $cols[] = ['d' => $c['d'], 'a' => $c['a']];
    }
    $rec['C'] = $cols;
  } elseif ($test === 'papi') {
    $A = $in['A'] ?? null;
    if (!$valid($A, 90, 1)) out(['ok' => false]);
    $rec['A'] = array_values($A);
  } else {
    $P = $in['P'] ?? null; $K = $in['K'] ?? null;
    if (!$valid($P, 24, 3) || !$valid($K, 24, 3)) out(['ok' => false]);
    $rec['P'] = array_values($P); $rec['K'] = array_values($K);
  }
  $ok = file_put_contents($file, json_encode($rec, JSON_UNESCAPED_UNICODE) . "\n", FILE_APPEND | LOCK_EX) !== false;
  out(['ok' => $ok]);
}
if ($a === 'list') {
  auth($in); $rows = [];
  if (is_file($file)) foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $l) { $r = json_decode($l, true); if ($r) $rows[] = $r; }
  out(['ok' => true, 'entries' => $rows]);
}
if ($a === 'clear') {
  auth($in);
  if (is_file($file)) rename($file, $dir . '/arsip-' . date('Ymd-His') . '.jsonl');
  out(['ok' => true]);
}
out(['ok' => false]);
