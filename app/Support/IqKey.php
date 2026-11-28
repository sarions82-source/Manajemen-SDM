<?php

namespace App\Support;

/** Kunci jawaban Tes Penalaran. Berada di server agar tidak terbaca dari kode halaman. */
class IqKey
{
    const KEY = [2, 0, 3, 1, 4, 2, 0, 3, 1, 4, 2, 0, 3, 1, 4, 2, 0, 3, 1, 4, 2, 0, 3, 1, 4, 2, 0, 3, 1, 4, 2, 0, 3, 1, 4, 2, 0, 3, 1, 4];
    const SEC = [['Deret angka', 0, 10], ['Analogi verbal', 10, 18], ['Penalaran logis', 18, 26], ['Hitungan cerita', 26, 33], ['Pola dan pembeda', 33, 40]];
    const EDU = ['SMA/sederajat', 'D1–D3', 'S1/D4', 'S2', 'S3'];
    const FIELD = ['Bisnis dan Manajemen', 'Teknik dan Teknologi', 'Matematika dan Ilmu Komputer', 'Ilmu Sosial', 'Hukum', 'Pendidikan', 'Kedokteran dan Kesehatan', 'Ilmu Alam', 'Agrikultur', 'Arsitektur dan Desain', 'Bahasa dan Sastra', 'Komunikasi dan Informasi', 'Lainnya'];

    public static function score(array $A)
    {
        $s = 0;
        $sec = [];
        foreach (self::SEC as [$n, $a, $b]) {
            $v = 0;
            for ($i = $a; $i < $b; $i++) {
                if ((int) ($A[$i] ?? -1) === self::KEY[$i]) {
                    $v++;
                }
            }
            $s += $v;
            $sec[] = ['n' => $n, 'v' => $v, 't' => $b - $a];
        }
        $c = $s <= 12 ? 'Rendah' : ($s <= 20 ? 'Di bawah rata-rata' : ($s <= 30 ? 'Rata-rata' : ($s <= 36 ? 'Di atas rata-rata' : 'Tinggi')));
        return ['s' => $s, 'p' => (int) round($s / 40 * 100), 'c' => $c, 'sec' => $sec];
    }

    public static function ageGroup($age)
    {
        if (!is_numeric($age)) {
            return '';
        }
        $a = (int) $age;
        return $a < 18 ? '< 18' : ($a < 30 ? '18-29' : ($a < 40 ? '30-39' : ($a < 50 ? '40-49' : ($a < 60 ? '50-59' : ($a < 70 ? '60-69' : '70+')))));
    }
}
