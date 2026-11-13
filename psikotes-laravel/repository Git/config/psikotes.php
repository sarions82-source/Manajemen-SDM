<?php

return [
    // Kunci pemasangan (opsional). Jika diisi, wajib dimasukkan saat mendaftarkan Super Admin pertama.
    'setup_key' => env('SETUP_KEY', ''),

    // Daftar nama pengguna staf (Super Admin/Admin/Employee) yang diizinkan memakai aplikasi ini.
    // Selain nama di bawah, tidak ada yang bisa mendaftar, dibuat, atau login sebagai staf.
    // Kosongkan daftar ([]) untuk mematikan pembatasan. Ubah hanya oleh pemilik aplikasi.
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
        'yofi.yulianto',
        'yoyanda.krisma',
    ],
];
