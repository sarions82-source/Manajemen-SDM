<?php

namespace App\Support;

/**
 * Kunci jawaban dan tabel IQ WPT (Wonderlic Personnel Test, Form A).
 * Skor dihitung di server agar kunci tidak terbaca dari kode halaman.
 * Jenis kunci: txt = teks persis (tanpa beda huruf besar/kecil), num = angka (boleh pecahan a/b),
 * set = beberapa angka dalam urutan bebas.
 */
class WptKey
{
    const KEY = [
        ['t' => 'txt', 'v' => ['4']],
        ['t' => 'txt', 'v' => ['2']],
        ['t' => 'txt', 'v' => ['3']],
        ['t' => 'txt', 'v' => ['t', 'n', 'tidak', 'no']],
        ['t' => 'txt', 'v' => ['3']],
        ['t' => 'txt', 'v' => ['1']],
        ['t' => 'txt', 'v' => ['3']],
        ['t' => 'num', 'v' => [0.125], 'tol' => 1e-09],
        ['t' => 'txt', 'v' => ['1']],
        ['t' => 'txt', 'v' => ['4']],
        ['t' => 'txt', 'v' => ['3']],
        ['t' => 'num', 'v' => [6000], 'tol' => 1e-09],
        ['t' => 'txt', 'v' => ['1', 'b', 'benar']],
        ['t' => 'txt', 'v' => ['2']],
        ['t' => 'num', 'v' => [20], 'tol' => 1e-09],
        ['t' => 'num', 'v' => [2], 'tol' => 1e-09],
        ['t' => 'txt', 'v' => ['a']],
        ['t' => 'num', 'v' => [13], 'tol' => 1e-09],
        ['t' => 'txt', 'v' => ['3']],
        ['t' => 'txt', 'v' => ['1', 'b', 'benar']],
        ['t' => 'num', 'v' => [20], 'tol' => 1e-09],
        ['t' => 'txt', 'v' => ['s', 'salah']],
        ['t' => 'set', 'v' => [2, 5]],
        ['t' => 'num', 'v' => [2], 'tol' => 1e-09],
        ['t' => 'txt', 'v' => ['3']],
        ['t' => 'txt', 'v' => ['1', 'b', 'benar']],
        ['t' => 'num', 'v' => [0.03333333333333333], 'tol' => 0.0005],
        ['t' => 'txt', 'v' => ['3']],
        ['t' => 'num', 'v' => [6], 'tol' => 1e-09],
        ['t' => 'num', 'v' => [10], 'tol' => 1e-09],
        ['t' => 'num', 'v' => [0.1111111111111111], 'tol' => 0.0005],
        ['t' => 'txt', 'v' => ['ya', 'y']],
        ['t' => 'txt', 'v' => ['3']],
        ['t' => 'num', 'v' => [20], 'tol' => 1e-09],
        ['t' => 'num', 'v' => [0.25], 'tol' => 1e-09],
        ['t' => 'num', 'v' => [24], 'tol' => 1e-09],
        ['t' => 'num', 'v' => [0.0625], 'tol' => 1e-09],
        ['t' => 'set', 'v' => [6, 9]],
        ['t' => 'txt', 'v' => ['2']],
        ['t' => 'num', 'v' => [1], 'tol' => 1e-09],
        ['t' => 'set', 'v' => [1, 4]],
        ['t' => 'set', 'v' => [3, 22]],
        ['t' => 'num', 'v' => [0.33], 'tol' => 1e-09],
        ['t' => 'txt', 'v' => ['2']],
        ['t' => 'num', 'v' => [24, 0.24], 'tol' => 1e-09],
        ['t' => 'txt', 'v' => ['2']],
        ['t' => 'txt', 'v' => ['3']],
        ['t' => 'num', 'v' => [175], 'tol' => 1e-09],
        ['t' => 'set', 'v' => [1, 2, 4, 5]],
        ['t' => 'num', 'v' => [12], 'tol' => 1e-09],
    ];

    const IQ = [59, 59, 61, 64, 67, 69, 71, 73, 75, 78, 80, 81, 83, 86, 88, 90, 93, 95, 97, 98, 100, 102, 104, 106, 108, 111, 113, 114, 116, 118, 120, 121, 123, 125, 126, 128, 130, 132, 134, 136, 138, 140, 142, 143, 146, 146, 146, 146, 146, 146, 146];

    public static function cat($v)
    {
        if ($v <= 65) {
            return 'Mentally Defective';
        }
        if ($v <= 79) {
            return 'Borderline Defective';
        }
        if ($v <= 90) {
            return 'Low Average';
        }
        if ($v <= 110) {
            return 'Average';
        }
        if ($v <= 119) {
            return 'High Average';
        }
        if ($v <= 127) {
            return 'Superior';
        }
        return $v <= 139 ? 'Very Superior' : 'Genius';
    }

    private static function parseNum($s)
    {
        $s = str_replace(' ', '', mb_strtolower(trim((string) $s)));
        if ($s === '') {
            return null;
        }
        if (preg_match('/^(\d+)\/(\d+)$/', $s, $m)) {
            return (int) $m[2] === 0 ? null : ((int) $m[1]) / ((int) $m[2]);
        }
        if (preg_match('/^[1-9]\d{0,2}([.,]\d{3})+$/', $s)) {
            return (float) preg_replace('/[.,]/', '', $s);
        }
        if (preg_match('/^\d*[.,]\d+$/', $s)) {
            return (float) str_replace(',', '.', $s);
        }
        if (preg_match('/^\d+$/', $s)) {
            return (float) $s;
        }
        return null;
    }

    private static function setOk($ans, array $want)
    {
        $ans = trim((string) $ans);
        preg_match_all('/\d+/', $ans, $m);
        $t = array_map('intval', $m[0]);
        sort($want);
        if (count($t) === 1 && count($want) > 1 && max($want) < 10 && preg_match('/^\d+$/', $ans) && strlen($ans) === count($want)) {
            $t = array_map('intval', str_split($ans));
        }
        sort($t);
        return $t === $want;
    }

    /** True bila jawaban benar menurut aturan kunci butir. */
    public static function ok(array $k, $ans)
    {
        $a = trim((string) $ans);
        if ($a === '') {
            return false;
        }
        if ($k['t'] === 'txt') {
            return in_array(preg_replace('/\s+/', ' ', mb_strtolower($a)), $k['v'], true);
        }
        if ($k['t'] === 'num') {
            $x = self::parseNum($a);
            if ($x === null) {
                return false;
            }
            foreach ($k['v'] as $v) {
                if (abs($x - $v) <= $k['tol']) {
                    return true;
                }
            }
            return false;
        }
        return self::setOk($a, $k['v']);
    }

    public static function score(array $A)
    {
        $ok = [];
        $rs = 0;
        $last = 0;
        for ($i = 0; $i < 50; $i++) {
            $a = (string) ($A[$i] ?? '');
            if (trim($a) !== '') {
                $last = $i + 1;
            }
            $o = self::ok(self::KEY[$i], $a);
            $ok[] = $o ? 1 : 0;
            if ($o) {
                $rs++;
            }
        }
        $iq = self::IQ[min($rs, 50)];
        return ['rs' => $rs, 'iq' => $iq, 'iqc' => self::cat($iq), 'last' => $last, 'wrong' => 50 - $rs, 'ok' => $ok];
    }
}
