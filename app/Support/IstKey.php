<?php

namespace App\Support;

/**
 * Kunci jawaban dan norma IST (Amthauer). Berada di server agar tidak terbaca dari kode halaman.
 * Data (kunci, daftar kata GE, norma usia dan pendidikan) ada di ist_data.json.
 */
class IstKey
{
    const SUB = ['SE', 'WA', 'AN', 'GE', 'ME', 'RA', 'ZR', 'FA', 'WU'];
    const MC = ['SE', 'WA', 'AN', 'FA', 'WU', 'ME'];
    const EDU = ['SLTP (SD/SMP)', 'SLTA (SMA/SMK)', 'D1–D3', 'S1/D4', 'S2', 'S3'];

    private static $d = null;

    private static function data()
    {
        if (self::$d === null) {
            self::$d = json_decode(file_get_contents(__DIR__ . '/ist_data.json'), true);
        }
        return self::$d;
    }

    /** Kelompok norma pendidikan: SLTP, SLTA, atau SARJ (selain keduanya). */
    public static function eduGroup($edu)
    {
        $e = strtoupper(trim((string) $edu));
        if (strpos($e, 'SLTP') === 0) {
            return 'SLTP';
        }
        if (strpos($e, 'SLTA') === 0) {
            return 'SLTA';
        }
        return 'SARJ';
    }

    public static function catSw($v)
    {
        if ($v <= 80) {
            return 'Sangat Rendah';
        }
        if ($v <= 94) {
            return 'Rendah';
        }
        if ($v <= 99) {
            return 'Sedang';
        }
        if ($v <= 104) {
            return 'Cukup';
        }
        return $v <= 118 ? 'Tinggi' : 'Sangat Tinggi';
    }

    public static function catIq($v)
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

    private static function norm($s)
    {
        return trim(preg_replace('/\s+/', ' ', mb_strtolower((string) $s)));
    }

    /** Angka jawaban hitungan: hanya digit; nol di depan dibuang. Selain itu null. */
    private static function digits($s)
    {
        $s = trim((string) $s);
        if ($s === '' || !preg_match('/^\d+$/', $s)) {
            return null;
        }
        return (string) ((int) $s);
    }

    /** Skor mentah (RW) tiap subtes. */
    public static function raw(array $A)
    {
        $K = self::data()['key'];
        $ge = self::data()['ge'];
        $r = [];
        foreach (self::MC as $k) {
            $n = 0;
            for ($i = 0; $i < 20; $i++) {
                $v = $A[$k][$i] ?? -1;
                if (is_int($v) && $v >= 0 && $v <= 4 && 'abcde'[$v] === $K[$k][$i]) {
                    $n++;
                }
            }
            $r[$k] = $n;
        }
        foreach (['RA', 'ZR'] as $k) {
            $n = 0;
            for ($i = 0; $i < 20; $i++) {
                $x = self::digits($A[$k][$i] ?? '');
                if ($x !== null && in_array($x, $K[$k][$i], true)) {
                    $n++;
                }
            }
            $r[$k] = $n;
        }
        $g = 0;
        for ($i = 0; $i < 16; $i++) {
            $w = self::norm($A['GE'][$i] ?? '');
            foreach ($ge[$i] as $p) {
                if ($p[0] === $w) {
                    $g += (int) $p[1];
                    break;
                }
            }
        }
        $r['GE'] = $g;
        return $r;
    }

    public static function score(array $A, $age, $edu)
    {
        $N = self::data()['norm'];
        $R = self::raw($A);
        $tot = 0;
        foreach (self::SUB as $k) {
            $tot += $R[$k];
        }
        // kelompok usia: batas atas pertama yang >= usia, selain itu kelompok terakhir
        $g = end($N['bounds']);
        foreach ($N['bounds'] as $b) {
            if ((int) $age <= $b) {
                $g = $b;
                break;
            }
        }
        $sw = [];
        $cat = [];
        foreach (self::SUB as $k) {
            $v = $N['age'][$g][$k][$R[$k]] ?? null;
            $sw[$k] = $v;
            $cat[$k] = $v === null ? null : self::catSw($v);
        }
        $gs = $N['gs'][$g][min($tot, 180)] ?? null;
        $iq = $N['iq'][$gs] ?? null;
        $eg = self::eduGroup($edu);
        $esw = [];
        foreach (self::SUB as $k) {
            $esw[$k] = $N['edu'][$k][$eg][$R[$k]] ?? null;
        }
        $iqe = null;
        foreach ($N['iqe'][$eg === 'SLTP' ? 'SLTP' : 'LAIN'] as $p) {
            if ($p[0] <= $tot) {
                $iqe = $p[1];
            }
        }
        $x1 = ($sw['GE'] ?? 0) + ($sw['RA'] ?? 0);
        $x2 = ($sw['AN'] ?? 0) + ($sw['ZR'] ?? 0);
        $dom = abs($x1 - $x2) <= 10 ? 'Seimbang' : ($x1 > $x2 ? 'Eksak' : 'Non Eksak');
        return [
            'rw' => $R, 'tot' => $tot, 'band' => $g, 'dom' => $dom,
            'age' => ['sw' => $sw, 'cat' => $cat, 'gs' => $gs, 'iq' => $iq, 'iqc' => $iq === null ? null : self::catIq($iq)],
            'edu' => ['grp' => $eg, 'sw' => $esw, 'iq' => $iqe, 'iqc' => $iqe === null ? null : self::catIq($iqe)],
        ];
    }
}
