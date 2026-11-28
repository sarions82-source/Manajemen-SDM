<?php

$config = [
    // Kunci pemasangan (opsional). Jika diisi, wajib dimasukkan saat mendaftarkan Super Admin pertama.
    'setup_key' => env('SETUP_KEY', ''),

    // Mode ketat peran: Super Admin hanya 26 akun awal (database/staf-awal.json); akun baru hanya bisa Admin.
    'strict_roles' => filter_var(env('PERAN_KETAT', true), FILTER_VALIDATE_BOOLEAN),

    // Wajib ganti password pada login pertama untuk akun Super Admin awal. Bawaan: mati (WAJIB_GANTI_SANDI=true untuk menyalakan).
    'force_change' => filter_var(env('WAJIB_GANTI_SANDI', false), FILTER_VALIDATE_BOOLEAN),

    // Daftar nama pengguna staf (Super Admin/Admin/Employee). BAWAAN: tidak ada pembatasan, Super Admin bebas
    // membuat akun dengan username apa saja. Pembatasan hanya aktif bila STAFF_ALLOW_ONLY=true; saat itu hanya
    // nama di bawah (ditambah env STAFF_ALLOW) yang bisa dibuat dan login sebagai staf.
    'staff_allow' => [
        'edwin.budi',
        'achmad.fanani',
        'achmad.hamam',
        'a.romadhan',
        'agus.mustofa',
        'ahmad.yusuf',
        'bambang.irawan',
        'deny.gita',
        'endang.silatur',
        'fajar.ramadhan',
        'imelda.inka',
        'irwan.candra',
        'iwan.agus',
        'kadarisman',
        'martha.cahya',
        'mochamad.sugeng',
        'mohammad.feryzal',
        'mohammad.subhan',
        'muhammad.fuad',
        'petrus.damianus',
        'puguh.kurniawan',
        'ririn.kurniawati',
        'williyanto.sakunab',
        'yaladriya.raras',
        'yofi.yuliyanto',
        'yoyanda.krisma',
    ],
];

// Tambahan nama staf lewat env STAFF_ALLOW (pisahkan dengan koma), atau STAFF_ALLOW=* untuk mencabut pembatasan.
$extra = trim((string) env('STAFF_ALLOW', ''));
if ($extra === '*') {
    $config['staff_allow'] = [];
} elseif ($extra !== '') {
    $config['staff_allow'] = array_values(array_unique(array_merge(
        $config['staff_allow'],
        array_filter(array_map(fn ($x) => strtolower(trim($x)), explode(',', $extra)))
    )));
}
if (!filter_var(env('STAFF_ALLOW_ONLY', false), FILTER_VALIDATE_BOOLEAN)) {
    $config['staff_allow'] = [];
}
return $config;
