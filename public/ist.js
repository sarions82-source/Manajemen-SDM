/* IST (Intelligenz-Struktur-Test, Amthauer): 9 subtes, 176 soal.
   Kunci jawaban dan norma TIDAK ada di berkas ini: skor dihitung di server (app/Support/IstKey.php). */
(function () {
"use strict";
const e = s => String(s ?? "").replace(/[&<>"]/g, c => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;" }[c]));
const L5 = "abcde";

const EDU = ["SLTP (SD/SMP)", "SLTA (SMA/SMK)", "D1–D3", "S1/D4", "S2", "S3"];

/* Nama subtes, nomor soal, dan waktu (menit). Waktu mengikuti pelaksanaan baku IST; ubah di sini bila perlu. */
const SUBS = {
  SE: { n: "SE", t: "Melengkapi kalimat", d: "Pengetahuan umum dan pertimbangan", from: 1, to: 20, min: 6 },
  WA: { n: "WA", t: "Pemilihan kata", d: "Pemahaman bahasa dan pembentukan konsep", from: 21, to: 40, min: 6 },
  AN: { n: "AN", t: "Analogi", d: "Kemampuan menghubungkan dan fleksibilitas berpikir", from: 41, to: 60, min: 7 },
  GE: { n: "GE", t: "Persamaan kata", d: "Abstraksi verbal", from: 61, to: 76, min: 8 },
  RA: { n: "RA", t: "Hitungan", d: "Berhitung praktis", from: 77, to: 96, min: 10 },
  ZR: { n: "ZR", t: "Deret angka", d: "Berpikir induktif numerik", from: 97, to: 116, min: 10 },
  FA: { n: "FA", t: "Memilih bentuk", d: "Daya bayang ruang dan kombinasi bentuk", from: 117, to: 136, min: 7 },
  WU: { n: "WU", t: "Kubus", d: "Daya bayang ruang tiga dimensi", from: 137, to: 156, min: 9 },
  ME: { n: "ME", t: "Mengingat kata", d: "Daya ingat", from: 157, to: 176, min: 6 }
};
const ORDER = ["SE", "WA", "AN", "GE", "RA", "ZR", "FA", "WU", "ME"];
const MEM_MIN = 3;   // menghafal kata ME, diberikan setelah GE

const INS = {
  SE: "Pada setiap kalimat satu kata hilang dan disediakan 5 kata pilihan sebagai penggantinya. Pilih kata yang paling tepat untuk menyempurnakan kalimat.<br><b>Contoh:</b> Seekor kuda mempunyai kesamaan terbanyak dengan seekor ….. a) kucing b) bajing c) keledai d) lembu e) anjing. Jawabannya c) keledai.<br><b>Contoh:</b> Lawannya “harapan” ialah ….. a) duka b) putus asa c) sengsara d) cinta e) benci. Jawabannya b) putus asa.",
  WA: "Ditentukan 5 kata. Pada 4 dari 5 kata itu terdapat suatu kesamaan. Carilah kata yang kelima yang tidak memiliki kesamaan dengan keempat kata itu.<br><b>Contoh:</b> a) meja b) kursi c) burung d) lemari e) tempat tidur. Jawabannya c) burung, karena yang lain perabot rumah.<br><b>Contoh:</b> a) duduk b) berbaring c) berdiri d) berjalan e) berjongkok. Jawabannya d) berjalan, karena yang lain tidak bergerak.",
  AN: "Ditentukan 3 kata. Antara kata pertama dan kedua terdapat hubungan tertentu. Antara kata ketiga dan salah satu dari lima kata pilihan harus pula terdapat hubungan yang sama. Carilah kata itu.<br><b>Contoh:</b> Hutan : pohon = tembok : ? a) batu bata b) rumah c) semen d) putih e) dinding. Jawabannya a) batu bata.<br><b>Contoh:</b> Gelap : terang = basah : ? a) hujan b) hari c) lembab d) angin e) kering. Jawabannya e) kering.",
  GE: "Ditentukan dua kata. Carilah satu perkataan yang meliputi pengertian kedua kata tadi, lalu tulis pada kotak jawaban.<br><b>Contoh:</b> Ayam – itik. Jawabannya “burung”.<br><b>Contoh:</b> Gaun – celana. Jawabannya “pakaian”.<br>Carilah selalu perkataan yang tepat yang dapat meliputi pengertian kedua kata itu.",
  RA: "Soal-soal hitungan. Tulis jawaban berupa angka pada kotak jawaban.<br><b>Contoh:</b> Sebatang pensil harganya 25 rupiah. Berapakah harga 3 batang? Jawabannya 75.<br><b>Contoh:</b> Dengan sepeda Husin dapat mencapai 15 km dalam waktu 1 jam. Berapa km yang dapat ia capai dalam 4 jam? Jawabannya 60.",
  ZR: "Pada soal berikut diberikan deret angka. Setiap deret tersusun menurut aturan tertentu dan dapat dilanjutkan menurut aturan itu. Carilah angka berikutnya dan tulis pada kotak jawaban.<br><b>Contoh:</b> 2 4 6 8 10 12 14 ? Jawabannya 16 (angka di depan selalu ditambah 2).<br><b>Contoh:</b> 9 7 10 8 11 9 12 ? Jawabannya 10 (berganti-ganti dikurangi 2 dan ditambah 3). Kadang-kadang harus pula dikalikan atau dibagi.",
  FA: "Setiap soal memperlihatkan suatu bentuk yang terpotong menjadi beberapa bagian. Carilah di antara bentuk-bentuk yang ditentukan (a, b, c, d, e) bentuk yang dibangun dengan menyusun potongan-potongan itu sedemikian rupa sehingga tidak ada kelebihan sudut atau ruang di antaranya. Pilih huruf bentuk tersebut.",
  WU: "Ditentukan 5 buah kubus a, b, c, d, e. Pada tiap kubus terdapat enam tanda yang berlainan pada setiap sisinya, dan tiga di antaranya dapat dilihat. Kubus-kubus itu berbeda satu sama lain: dapat mempunyai tanda yang sama, tetapi susunannya berlainan. Setiap soal memperlihatkan salah satu kubus dalam kedudukan yang berbeda. Carilah kubus yang dimaksud. Kubus dapat diputar atau digulingkan dalam pikiran Anda, sehingga mungkin terlihat tanda yang baru.",
  ME: "Pada bagian ini terdapat pertanyaan mengenai kata-kata yang telah Anda hafalkan tadi. Pilih jenis kata yang sesuai.<br><b>Contoh:</b> Kata yang mempunyai huruf permulaan – Q – adalah suatu ….. Jawabannya d) kesenian (Quintet).<br><b>Contoh:</b> Kata yang mempunyai huruf permulaan – Z – adalah suatu ….. Jawabannya e) binatang (Zebra)."
};

/* ===================== SOAL ===================== */
const SE = [
  ["Pengaruh seseorang terhadap orang lain seharusnya bergantung pada …..", "kekuasaan", "bujukan", "kekayaan", "keberanian", "kewibawaan"],
  ["Lawannya “hemat” ialah …..", "murah", "kikir", "boros", "bernilai", "kaya"],
  ["….. tidak termasuk cuaca", "angin puyuh", "halilintar", "salju", "gempa bumi", "kabut"],
  ["Lawannya “setia” ialah …..", "cinta", "benci", "persahabatan", "khianat", "permusuhan"],
  ["Seekor kuda selalu mempunyai …..", "kandang", "ladam", "pelana", "kuku", "surai"],
  ["Seorang paman ….. lebih tua dari kemenakannya.", "jarang", "biasanya", "selalu", "tidak pernah", "kadang-kadang"],
  ["Pada jumlah yang sama, nilai kalori yang tertinggi terdapat pada …..", "ikan", "daging", "lemak", "tahu", "sayuran"],
  ["Pada suatu pertandingan selalu terdapat …..", "lawan", "wasit", "penonton", "sorak", "kemenangan"],
  ["Suatu pernyataan yang belum dipastikan dikatakan sebagai pernyataan yang …..", "paradoks", "tergesa-gesa", "mempunyai arti rangkap", "menyesatkan", "hipotesis"],
  ["Pada sepatu selalu terdapat …..", "kulit", "sol", "tali sepatu", "gesper", "lidah"],
  ["Suatu ….. tidak menyangkut persoalan pencegahan kecelakaan.", "lampu lalu lintas", "kacamata pelindung", "kotak PPPK", "tanda peringatan", "palang kereta api"],
  ["Mata uang logam Rp 50,- tahun 1991, garis tengahnya ialah ….. mm.", "17", "29", "25", "20", "15"],
  ["Seseorang yang bersikap menyangsikan setiap kemajuan ialah seorang yang …..", "demokratis", "radikal", "liberal", "konservatif", "anarkis"],
  ["Lawannya “tidak pernah” ialah …..", "sering", "kadang-kadang", "jarang", "kerap kali", "selalu"],
  ["Jarak antara Jakarta – Surabaya kira-kira ….. Km", "650", "1000", "800", "600", "950"],
  ["Untuk dapat membuat nada yang rendah dan mendalam, kita memerlukan banyak ….", "kekuatan", "peranan", "ayunan", "berat", "suara"],
  ["Ayah ….. lebih berpengalaman dari pada anaknya", "selalu", "biasanya", "jauh", "jarang", "pada dasarnya"],
  ["Diantara kota-kota berikut ini, maka kota ….. letaknya paling selatan.", "Jakarta", "Bandung", "Cirebon", "Semarang", "Surabaya"],
  ["Jika kita mengetahui jumlah presentase nomor-nomor lotere yang tidak menang, maka kita dapat menghitung …..", "jumlah nomor yang menang", "pajak lotere", "kemungkinan menang", "jumlah pengikut", "tinggi keuntungan"],
  ["Seorang anak yang berumur 10 tahun tingginya rata-rata ….. cm", "150", "130", "110", "105", "115"]
];
const WA = [
  ["lingkungan", "panah", "elips", "busur", "lengkungan"], ["mengetuk", "memaki", "menjahit", "menggergaji", "memukul"],
  ["lebar", "keliling", "luas", "isi", "panjang"], ["mengikat", "menyatukan", "melepaskan", "mengaitkan", "melekatkan"],
  ["arah", "timur", "perjalanan", "tujuan", "selatan"], ["jarak", "perpisahan", "tugas", "batas", "perceraian"],
  ["saringan", "kelambu", "payung", "tapisan", "jala"], ["putih", "pucat", "buram", "kasar", "berkilauan"],
  ["otobis", "pesawat terbang", "sepeda motor", "sepeda", "kapal api"], ["biola", "seruling", "klarinet", "terompet", "saxophon"],
  ["bergelombang", "kasar", "berduri", "licin", "lurus"], ["jam", "kompas", "penunjuk jalan", "bintang pari", "arah"],
  ["kebijaksanaan", "pendidikan", "perencanaan", "penempatan", "pengerahan"], ["bermotor", "berjalan", "berlayar", "bersepeda", "berkuda"],
  ["gambar", "lukisan", "potret", "patung", "ukiran"], ["panjang", "lonjong", "runcing", "bulat", "bersudut"],
  ["kunci", "palang pintu", "gerendel", "gunting", "obeng"], ["jembatan", "batas", "perkawinan", "pagar", "masyarakat"],
  ["mengetam", "menasehati", "mengasah", "melicinkan", "menggosok"], ["batu", "baja", "bulu", "karet", "kayu"]
];
const AN = [
  ["Menemukan : menghilangkan = Mengingat : ?", "menghapal", "mengenai", "melupakan", "berpikir", "memimpikan"],
  ["Bunga : jambangan = Burung : ?", "sarang", "langit", "pagar", "pohon", "sangkar"],
  ["Kereta api : rel = Otobis : ?", "roda", "poros", "ban", "jalan raya", "kecepatan"],
  ["Perak : emas = Cincin : ?", "arloji", "berlian", "permata", "gelang", "platina"],
  ["Lingkaran : bola = Bujur sangkar : ?", "bentuk", "gambar", "segi empat", "kubus", "piramida"],
  ["Saran : kepustakaan = Merundingkan : ?", "menawarkan", "menentukan", "menilai", "menimbang", "merenungkan"],
  ["Lidah : asam = Hidung : ?", "mencium", "bernapas", "mengecap", "tengik", "asin"],
  ["Darah : pembuluh = Air : ?", "pintu air", "sungai", "talang", "hujan", "ember"],
  ["Saraf : penyalur = Pupil : ?", "penyinaran", "mata", "melihat", "cahaya", "pelindung"],
  ["Pengantar surat : pengantar telegram = Pandai besi : ?", "palu godam", "pedagang besi", "api", "tukang emas", "besi tempa"],
  ["Buta : warna = Tuli : ?", "pendengaran", "mendengar", "nada", "kata", "telinga"],
  ["Makanan : bumbu = Ceramah : ?", "penghinaan", "pidato", "kelakar", "kesan", "ayat"],
  ["Marah : emosi = Duka cita : ?", "suka cita", "sakit hati", "suasana hati", "sedih", "rindu"],
  ["Mantel : jubah = wool : ?", "bahan sandang", "domba", "sutra", "jas", "tekstil"],
  ["Ketinggian puncak : tekanan udara = ketinggian nada : ?", "garpu tala", "sopran", "nyanyian", "panjang senar", "suara"],
  ["Negara : revolusi = Hidup : ?", "biologi", "keturunan", "mutasi", "seleksi", "ilmu hewan"],
  ["Kekurangan : penemuan = Panas : ?", "haus", "khatulistiwa", "es", "matahari", "dingin"],
  ["Kayu : diketam = Besi : ?", "dipalu", "digergaji", "dituang", "dikikir", "ditempa"],
  ["Olahragawan : lembing = Cendekiawan : ?", "perpustakaan", "penelitian", "karya", "studi", "mikroskop"],
  ["Keledai : kuda pacuan = Pembakaran : ?", "pemadam api", "obor", "letupan", "korek api", "lautan api"]
];
const GE = ["mawar – melati", "mata – telinga", "gula – intan", "hujan – salju", "pengantar surat – telepon", "kamera – kacamata", "lambung – usus", "banyak – sedikit", "telur – benih", "bendera – lencana", "rumput – gajah", "ember – kantong", "awal – akhir", "kikir – boros", "penawaran – permintaan", "atas – bawah"];
const RA = [
  "Jika seorang anak memiliki 50 rupiah dan memberikan 15 rupiah kepada orang lain, berapa rupiahkah yang masih tinggal padanya?",
  "Berapa km-kah yang dapat ditempuh oleh kereta api dalam waktu 7 jam, jika kecepatannya 40 km/jam?",
  "15 peti buah-buahan beratnya 250 kg dan setiap peti kosong beratnya 3 kg, berapakah berat buah-buahan itu?",
  "Seseorang mempunyai persediaan rumput yang cukup untuk 7 ekor kuda selama 78 hari. Berapa harikah persediaan itu cukup untuk 21 ekor kuda?",
  "3 batang coklat harganya Rp 5,- Berapa batangkah yang dapat kita beli dengan Rp 50,-?",
  "Seseorang dapat berjalan 1,75 m dalam waktu ¼ detik. Berapakah meterkah yang dapat ia tempuh dalam waktu 10 detik?",
  "Jika sebuah batu terletak 15 m di sebelah selatan dari sebatang pohon dan pohon itu berada 30 m di sebelah selatan dari sebuah rumah, berapa meterkah jarak antara batu dan rumah itu?",
  "Jika 4 ½ m bahan sandang harganya Rp 90,- berapakah rupiahkah harganya 2 ½ m?",
  "7 orang dapat menyelesaikan sesuatu pekerjaan dalam 6 hari. Berapa orangkah yang diperlukan untuk menyelesaikan pekerjaan itu dalam setengah hari?",
  "Karena dipanaskan, kawat yang panjangnya 48 cm akan mengembang menjadi 52 cm. Setelah pemanasan, berapakah panjangnya kawat yang berukuran 72 cm?",
  "Suatu pabrik dapat menghasilkan 304 batang pensil dalam waktu 8 jam. Berapa batangkah dihasilkan dalam waktu setengah jam?",
  "Untuk suatu campuran diperlukan 2 bagian perak dan 3 bagian timah. Berapa gramkah perak yang diperlukan untuk mendapatkan campuran itu yang beratnya 15 gram?",
  "Untuk setiap Rp 3,- yang dimiliki Sidin, Hamid memiliki Rp 5,- Jika mereka bersama mempunyai Rp 120,- berapa rupiahkah yang dimiliki Hamid?",
  "Mesin A menenun 60 m kain, sedangkan mesin B menenun 40 m. Berapa meterkah yang ditenun mesin A, jika mesin B menenun 60 m?",
  "Seseorang membelikan 1/10 dari uangnya untuk perangko dan 4 kali jumlah itu untuk alat tulis. Sisa uangnya masih Rp 60,- Berapa rupiahkah uang semula?",
  "Di dalam dua peti terdapat 43 piring. Di dalam peti yang satu terdapat 9 piring lebih banyak dari pada di dalam peti yang lain. Berapa buah piring terdapat di dalam peti yang lebih kecil?",
  "Suatu lembaran kain yang panjangnya 60 cm harus dibagikan sedemikian rupa sehingga panjangnya satu bagian ialah 2/3 dari bagian yang lain. Berapa panjangnya bagian yang terpendek?",
  "Suatu perusahaan mengekspor ¾ dari hasil produksinya dan menjual 4/5 dari sisa itu dalam negeri. Berapa % kah hasil produksi yang masih tinggal?",
  "Jika suatu botol berisi anggur hanya 7/8 bagian dan harganya ialah Rp 84,- berapakah harga anggur itu jika botol itu hanya terisi ½ penuh?",
  "Di dalam suatu keluarga setiap anak perempuan mempunyai jumlah saudara laki-laki yang sama dengan jumlah saudara perempuan dan setiap anak laki-laki mempunyai dua kali lebih banyak saudara perempuan dari pada saudara laki-laki. Berapa anak laki-lakikah yang terdapat di dalam keluarga tersebut?"
];
const ZR = [
  "6 9 12 15 18 21 24", "15 16 18 19 21 22 24", "19 18 22 21 25 24 28", "16 12 17 13 18 14 19", "2 4 8 10 20 22 44",
  "15 13 16 12 17 11 18", "25 22 11 33 30 15 45", "49 51 54 27 9 11 14", "2 3 1 3 4 2 4", "19 17 20 16 21 15 22",
  "94 92 46 44 22 20 10", "5 8 9 8 11 12 11", "12 15 19 23 28 33 39", "7 5 10 7 21 17 68", "11 15 18 9 13 16 8",
  "3 8 15 24 35 48 63", "4 5 7 4 8 13 7", "8 5 15 18 6 3 9", "15 6 18 10 30 23 69", "5 35 28 4 11 77 70"
];
const ME_CAT = ["bunga", "perkakas", "burung", "kesenian", "binatang"];
const ME_LET = "ABCDEFGHIJKLMNOPRSTU".split("");
const ME_WORDS = [
  ["BUNGA", "SOKA, LARAT, FLAMBOYAN, YASMIN, DAHLIA"], ["PERKAKAS", "WAJAN, JARUM, KIKIR, CANGKUL, PALU"],
  ["BURUNG", "ITIK, ELANG, WALET, TEKUKUR, NURI"], ["KESENIAN", "QUINTET, ARCA, OPERA, UKIRAN, GAMELAN"],
  ["BINATANG", "RUSA, MUSANG, BERUANG, HARIMAU, ZEBRA"]
];

/* ===================== STATE & ALUR ===================== */
let st = null, steps = [], si = 0, tleft = 0;

function fresh() {
  const mc = () => Array(20).fill(-1);
  return { edu: "", A: { SE: mc(), WA: mc(), AN: mc(), FA: mc(), WU: mc(), ME: mc(), GE: Array(16).fill(""), RA: Array(20).fill(""), ZR: Array(20).fill("") } };
}
function buildSteps() {
  const s = [];
  ["SE", "WA", "AN", "GE"].forEach(k => { s.push({ t: "ins", k }); s.push({ t: "test", k }); });
  s.push({ t: "memins" }); s.push({ t: "mem" });
  ["RA", "ZR", "FA", "WU"].forEach(k => { s.push({ t: "ins", k }); s.push({ t: "test", k }); });
  s.push({ t: "ins", k: "ME" }); s.push({ t: "test", k: "ME" });
  return s;
}
const stopTimer = () => { clearInterval(tk); };
function startTimer(min) {
  stopTimer();
  tleft = min * 60;
  tk = setInterval(() => {
    tleft--;
    const el = document.getElementById("istm");
    if (el) el.textContent = fmt(Math.max(tleft, 0));
    if (tleft <= 0) { stopTimer(); next(); }
  }, 1000);
}
function next() {
  stopTimer();
  si++;
  if (si >= steps.length) return finish(false);
  draw();
}
function answered(k) {
  return st.A[k].filter(v => (typeof v === "number" ? v >= 0 : String(v).trim() !== "")).length;
}

/* ===================== TAMPILAN ===================== */
const head = (title, sub) => `<div class="ln" style="border:0;padding:0"><span><b>${e(title)}</b><br><span class="m">${e(sub || "")}</span></span></div>`;

function eduForm() {
  V.innerHTML = `<div class="c"><h3>IST: data tambahan</h3><p class="m">Pendidikan terakhir dipakai untuk memilih tabel norma. Wajib diisi.</p><label>Pendidikan terakhir</label><select id="ist_e"><option value="">(pilih)</option>${EDU.map(x => `<option>${e(x)}</option>`).join("")}</select><p class="m" id="ist_w" style="color:var(--k)"></p><div class="row"><span></span><button id="ist_g">Lanjut</button></div></div>`;
  document.getElementById("ist_g").onclick = () => {
    const v = document.getElementById("ist_e").value;
    if (!v) { document.getElementById("ist_w").textContent = "Pilih pendidikan terakhir terlebih dahulu."; return; }
    st.edu = v; draw();
  };
}

function insPage(k) {
  const s = SUBS[k];
  const img = k === "FA" ? `<img src="/ist/contoh_fa.jpg" alt="Contoh soal FA" style="max-width:100%;height:auto;margin:10px 0">` : k === "WU" ? `<img src="/ist/contoh_wu.jpg" alt="Contoh soal WU" style="max-width:100%;height:auto;margin:10px 0">` : "";
  V.innerHTML = `<div class="c"><div class="row m"><span>Subtes ${ORDER.indexOf(k) + 1} dari 9</span><span>Soal ${s.from} – ${s.to}</span></div><h3>${s.n}: ${e(s.t)}</h3><p>${INS[k]}</p>${img}<p class="m">Waktu: ${s.min} menit. Waktu berjalan setelah tombol Mulai ditekan dan tidak bisa dijeda. Bagian yang sudah selesai tidak dapat dibuka kembali.</p><div class="row"><span></span><button id="ist_go">Mulai subtes ${s.n}</button></div></div>`;
  document.getElementById("ist_go").onclick = () => { si++; draw(); };
}

function memInsPage() {
  V.innerHTML = `<div class="c"><h3>ME: menghafal kata</h3><p>Sebentar lagi akan ditampilkan lima kelompok kata. Hafalkan kata-kata tersebut selama ${MEM_MIN} menit. Pertanyaan tentang kata-kata ini akan diberikan di akhir tes, jadi tidak perlu mencatat.</p><p class="m">Setelah ${MEM_MIN} menit, layar berpindah otomatis ke subtes berikutnya.</p><div class="row"><span></span><button id="ist_go">Tampilkan kata</button></div></div>`;
  document.getElementById("ist_go").onclick = () => { si++; draw(); };
}
function memPage() {
  V.innerHTML = `<div class="c"><div class="row m"><span>Hafalkan kata-kata berikut</span><span class="tt" id="istm">${fmt(MEM_MIN * 60)}</span></div>${ME_WORDS.map(w => `<p style="margin:10px 0"><b>${w[0]}</b> : ${w[1]}</p>`).join("")}<div class="row"><span></span><button class="s" id="ist_sk">Saya sudah hafal</button></div></div>`;
  document.getElementById("ist_sk").onclick = next;
  startTimer(MEM_MIN);
}

function mcItem(k, i, body, opts, imgHtml) {
  const v = st.A[k][i], no = SUBS[k].from + i;
  const ob = opts.map((o, j) => `<button class="o ${v === j ? "on" : ""}" data-k="${k}" data-i="${i}" data-v="${j}"><b>${L5[j]})</b> ${e(o)}</button>`).join("");
  return `<div class="pr"><b style="display:inline">${no}.</b> ${body ? e(body) : ""}${imgHtml || ""}<div>${ob}</div></div>`;
}
function letterItem(k, i, img) {
  const v = st.A[k][i], no = SUBS[k].from + i;
  const bt = [0, 1, 2, 3, 4].map(j => `<button class="s ra ${v === j ? "on" : ""}" data-k="${k}" data-i="${i}" data-v="${j}" style="padding:10px 18px;margin:2px">${L5[j]}</button>`).join("");
  return `<div class="pr"><b style="display:inline">${no}.</b><div><img src="${img}" alt="Soal ${no}" style="max-width:100%;height:auto;display:block;margin:6px 0"></div><div>${bt}</div></div>`;
}
function textItem(k, i, label, numeric) {
  const no = SUBS[k].from + i;
  return `<div class="pr"><b style="display:inline">${no}.</b> ${e(label)}<div><input class="ist-in" data-t="${k}" data-i="${i}" ${numeric ? 'inputmode="numeric" maxlength="6" placeholder="Angka jawaban"' : 'maxlength="40" placeholder="Tulis jawaban"'} value="${e(st.A[k][i])}" autocomplete="off" style="margin-top:6px"></div></div>`;
}

function testPage(k) {
  const s = SUBS[k]; let b = "";
  if (k === "SE") b = SE.map((q, i) => mcItem(k, i, q[0], q.slice(1))).join("");
  else if (k === "WA") b = WA.map((q, i) => mcItem(k, i, "", q)).join("");
  else if (k === "AN") b = AN.map((q, i) => mcItem(k, i, q[0], q.slice(1))).join("");
  else if (k === "GE") b = GE.map((q, i) => textItem(k, i, q, false)).join("");
  else if (k === "RA") b = RA.map((q, i) => textItem(k, i, q, true)).join("");
  else if (k === "ZR") b = ZR.map((q, i) => textItem(k, i, q + "  ?", true)).join("");
  else if (k === "ME") b = ME_LET.map((l, i) => mcItem(k, i, `Kata yang mempunyai huruf permulaan – ${l} – adalah …..`, ME_CAT)).join("");
  else if (k === "FA") {
    b = `<div><img src="/ist/fa_ref1.png" alt="Pilihan bentuk a-e untuk soal 117-128" style="max-width:100%;height:auto"></div>`;
    for (let i = 0; i < 20; i++) {
      if (i === 12) b += `<hr><div><img src="/ist/fa_ref2.png" alt="Pilihan bentuk a-e untuk soal 129-136" style="max-width:100%;height:auto"></div>`;
      b += letterItem(k, i, `/ist/fa_${117 + i}.png`);
    }
  } else if (k === "WU") {
    b = `<div class="row" style="justify-content:flex-start;gap:6px;flex-wrap:wrap">${["a", "b", "c", "d", "e"].map(l => `<span style="text-align:center"><img src="/ist/wu_${l}.jpg" alt="Kubus ${l}" style="width:110px;height:auto;display:block"><b>${l}</b></span>`).join("")}</div><hr>`;
    for (let i = 0; i < 20; i++) b += letterItem(k, i, `/ist/wu_${137 + i}.jpg`);
  }
  V.innerHTML = `<div class="c"><div class="row m" style="position:sticky;top:0;background:var(--card);padding:6px 0;z-index:5"><span><b>${s.n}</b> · ${e(s.t)}</span><span class="tt" id="istm">${fmt(s.min * 60)}</span><span id="istc">${answered(k)} / ${s.to - s.from + 1}</span></div>${b}<div class="row" style="margin-top:14px"><span class="m" id="istw"></span><button id="ist_fin">Selesai subtes ${s.n}</button></div></div>`;
  V.querySelectorAll("[data-v]").forEach(x => x.onclick = () => {
    const kk = x.dataset.k, i = +x.dataset.i, v = +x.dataset.v;
    st.A[kk][i] = v;
    V.querySelectorAll(`[data-k="${kk}"][data-i="${i}"]`).forEach(y => y.classList.toggle("on", +y.dataset.v === v));
    document.getElementById("istc").textContent = answered(kk) + " / " + (SUBS[kk].to - SUBS[kk].from + 1);
  });
  V.querySelectorAll(".ist-in").forEach(x => x.oninput = () => {
    const kk = x.dataset.t, i = +x.dataset.i;
    if (kk === "RA" || kk === "ZR") x.value = x.value.replace(/\D/g, "").slice(0, 6);
    st.A[kk][i] = x.value;
    document.getElementById("istc").textContent = answered(kk) + " / " + (SUBS[kk].to - SUBS[kk].from + 1);
  });
  const fin = document.getElementById("ist_fin");
  fin.onclick = () => {
    const m = (s.to - s.from + 1) - answered(k);
    if (m && !fin.dataset.w) { fin.dataset.w = 1; document.getElementById("istw").textContent = m + " soal belum dijawab. Klik lagi untuk lanjut."; return; }
    next();
  };
  window.scrollTo(0, 0);
  startTimer(s.min);
}

function draw() {
  if (!st.edu) return eduForm();
  const s = steps[si];
  if (s.t === "ins") insPage(s.k);
  else if (s.t === "test") testPage(s.k);
  else if (s.t === "memins") memInsPage();
  else memPage();
  window.scrollTo(0, 0);
}

function begin() {
  st = fresh(); steps = buildSteps(); si = 0;
  t0 = Date.now();
  stopTimer();
  draw();
}
function rec() {
  const s = st || fresh();
  return { A: JSON.parse(JSON.stringify(s.A)), edu: s.edu };
}

/* ===================== HASIL ===================== */
const NM = k => SUBS[k].n + ": " + SUBS[k].t;
const SUBL = ["SE", "WA", "AN", "GE", "ME", "RA", "ZR", "FA", "WU"];
const fx = v => (v === null || v === undefined ? "-" : v);

const SWB = [[60, 80, "#e53935", "Sangat Rendah"], [80, 94, "#fb8c00", "Rendah"], [94, 99, "#fdd835", "Sedang"], [99, 104, "#9ccc65", "Cukup"], [104, 118, "#43a047", "Tinggi"], [118, 140, "#1e63c7", "Sangat Tinggi"]];
const CUSIA = "#1e63c7", CPEND = "#e67e22";
const swColor = v => (v <= 80 ? "#e53935" : v <= 94 ? "#fb8c00" : v <= 99 ? "#fdd835" : v <= 104 ? "#9ccc65" : v <= 118 ? "#43a047" : "#1e63c7");

/* Grafik profil: SW tiap subtes (garis), norma usia dan norma pendidikan, dengan pita kategori */
function profileChart(a, ed) {
  const W = 540, H = 300, x0 = 46, x1 = 528, y0 = 28, y1 = 252, lo = 60, hi = 140;
  const Y = v => y1 - (Math.max(lo, Math.min(hi, v)) - lo) / (hi - lo) * (y1 - y0);
  const X = i => x0 + 22 + i * ((x1 - x0 - 44) / 8);
  let g = `<svg viewBox="0 0 ${W} ${H}" width="100%" role="img" aria-label="Profil skor standar IST per subtes">`;
  SWB.forEach(([f, t, c]) => { g += `<rect x="${x0}" y="${Y(t)}" width="${x1 - x0}" height="${Y(f) - Y(t)}" fill="${c}" fill-opacity=".16"/>`; });
  [60, 80, 100, 120, 140].forEach(v => { g += `<line x1="${x0}" x2="${x1}" y1="${Y(v)}" y2="${Y(v)}" stroke="#9aa4b2" stroke-width="${v === 100 ? 1.4 : .6}" ${v === 100 ? 'stroke-dasharray="5 4"' : ""}/><text x="${x0 - 6}" y="${Y(v) + 4}" text-anchor="end" fill="currentColor" font-size="11">${v}</text>`; });
  const line = (sw, col, dash) => {
    let d = "", pen = false;
    SUBL.forEach((k, i) => { const v = sw[k]; if (v == null) { pen = false; return; } d += (pen ? "L" : "M") + X(i) + " " + Y(v); pen = true; });
    let o = `<path d="${d}" fill="none" stroke="${col}" stroke-width="2.4" ${dash ? 'stroke-dasharray="6 4"' : ""} stroke-linejoin="round"/>`;
    SUBL.forEach((k, i) => { const v = sw[k]; if (v != null) o += `<circle cx="${X(i)}" cy="${Y(v)}" r="${dash ? 3.2 : 4.2}" fill="${dash ? "#fff" : col}" stroke="${col}" stroke-width="2"/>`; });
    return o;
  };
  g += line(ed.sw, CPEND, true) + line(a.sw, CUSIA, false);
  SUBL.forEach((k, i) => { const v = a.sw[k]; if (v != null) g += `<text x="${X(i)}" y="${Y(v) - 9}" text-anchor="middle" fill="currentColor" font-size="11" font-weight="700">${v}</text>`; g += `<text x="${X(i)}" y="${y1 + 18}" text-anchor="middle" fill="currentColor" font-size="12" font-weight="700">${k}</text>`; });
  g += `<line x1="${x0 + 4}" x2="${x0 + 26}" y1="12" y2="12" stroke="${CUSIA}" stroke-width="2.4"/><circle cx="${x0 + 15}" cy="12" r="4" fill="${CUSIA}"/><text x="${x0 + 32}" y="16" fill="currentColor" font-size="11">Norma usia</text><line x1="${x0 + 118}" x2="${x0 + 140}" y1="12" y2="12" stroke="${CPEND}" stroke-width="2.4" stroke-dasharray="6 4"/><circle cx="${x0 + 129}" cy="12" r="3.2" fill="#fff" stroke="${CPEND}" stroke-width="2"/><text x="${x0 + 146}" y="16" fill="currentColor" font-size="11">Norma pendidikan</text>`;
  g += `<text x="${x1}" y="${H - 6}" text-anchor="end" fill="currentColor" font-size="10" opacity=".75">Pita warna: kategori SW (merah rendah ke biru sangat tinggi) · garis putus SW 100</text></svg>`;
  return g;
}

/* Grafik rata-rata SW per kelompok kemampuan (norma usia) */
const GRP = [["Verbal", ["SE", "WA", "AN", "GE"]], ["Numerik", ["RA", "ZR"]], ["Figural", ["FA", "WU"]], ["Daya ingat", ["ME"]]];
function groupChart(sw) {
  const lab = 90, bw = 340, H = 30, lo = 60, hi = 140;
  let g = `<svg viewBox="0 0 520 ${GRP.length * H + 24}" width="100%" role="img" aria-label="Rata-rata SW per kelompok kemampuan">`;
  GRP.forEach(([n, ks], i) => {
    const vs = ks.map(k => sw[k]).filter(v => v != null), y = i * H + 4;
    if (!vs.length) { g += `<text x="0" y="${y + 17}" fill="currentColor" font-size="12">${n}</text>`; return; }
    const m = vs.reduce((p, c) => p + c, 0) / vs.length, w = (Math.max(lo, Math.min(hi, m)) - lo) / (hi - lo) * bw;
    g += `<text x="0" y="${y + 17}" fill="currentColor" font-size="12" font-weight="700">${n}</text><rect x="${lab}" y="${y + 3}" width="${bw}" height="18" rx="4" fill="#9aa4b2" fill-opacity=".25"/><rect x="${lab}" y="${y + 3}" width="${w}" height="18" rx="4" fill="${swColor(m)}"/><text x="${lab + bw + 8}" y="${y + 17}" fill="currentColor" font-size="12" font-weight="700">${Math.round(m * 10) / 10}</text><text x="${lab + 6}" y="${y + 17}" fill="#fff" font-size="10" style="paint-order:stroke" stroke="#00000066" stroke-width="2">${ks.join(" ")}</text>`;
  });
  const x100 = lab + (100 - lo) / (hi - lo) * bw;
  g += `<line x1="${x100}" x2="${x100}" y1="0" y2="${GRP.length * H + 4}" stroke="currentColor" stroke-dasharray="4 4" opacity=".6"/><text x="${x100}" y="${GRP.length * H + 18}" text-anchor="middle" fill="currentColor" font-size="10">SW 100</text></svg>`;
  return g;
}

/* Skala posisi IQ: segmen kategori, penanda norma usia (atas) dan pendidikan (bawah) */
const IQB = [[40, 65, "#e53935", "Defective"], [65, 79, "#fb8c00", "Borderline"], [79, 90, "#fdd835", "Low Avg"], [90, 110, "#9ccc65", "Average"], [110, 119, "#66bb6a", "Hi Avg"], [119, 127, "#43a047", "Sup."], [127, 139, "#1e88e5", "V. Sup."], [139, 160, "#5e35b1", "Genius"]];
function iqChart(iqA, iqE) {
  const x0 = 16, x1 = 504, yb = 40, h = 26, lo = 40, hi = 160, X = v => x0 + (Math.max(lo, Math.min(hi, v)) - lo) / (hi - lo) * (x1 - x0);
  let g = `<svg viewBox="0 0 520 132" width="100%" role="img" aria-label="Posisi IQ pada skala kategori">`;
  IQB.forEach(([f, t, c, n]) => { g += `<rect x="${X(f)}" y="${yb}" width="${X(t) - X(f)}" height="${h}" fill="${c}"/><text x="${(X(f) + X(t)) / 2}" y="${yb + h + 13}" text-anchor="middle" fill="currentColor" font-size="9">${n}</text>`; });
  [40, 65, 80, 90, 110, 120, 130, 140, 160].forEach(v => { g += `<text x="${X(v)}" y="${yb - 4}" text-anchor="middle" fill="currentColor" font-size="9" opacity=".7" ${[80, 130].includes(v) ? 'display="none"' : ""}>${v}</text>`; });
  const mk = (v, col, up, label) => v == null ? "" : `<path d="${up ? `M${X(v)} ${yb - 12}l-7 -12h14z` : `M${X(v)} ${yb + h + 22}l-7 12h14z`}" fill="${col}"/><text x="${Math.min(Math.max(X(v), 60), 460)}" y="${up ? yb - 28 : yb + h + 50}" text-anchor="middle" fill="currentColor" font-size="11" font-weight="700">${label}: ${v}</text>`;
  g += mk(iqA, CUSIA, true, "IQ usia") + mk(iqE, CPEND, false, "IQ pendidikan") + `</svg>`;
  return g;
}

function body(rec) {
  const r = rec.ist;
  if (!r) return `<div class="c"><h2>Hasil IST</h2><p class="m">Skor IST dihitung di server dan belum tersedia untuk catatan ini. Muat ulang daftar hasil atau pastikan hasil tersimpan.</p></div>`;
  const a = r.age, ed = r.edu;
  const ok = SUBL.filter(k => a.sw[k] != null);
  const hi = ok.slice().sort((x, y) => a.sw[y] - a.sw[x]).slice(0, 2), lo = ok.slice().sort((x, y) => a.sw[x] - a.sw[y]).slice(0, 2);
  const card = (t, b, c) => `<div><p class="m" style="margin:0">${t}</p><p class="tt" style="font-size:22px;margin:0">${b}</p><p class="m" style="margin:0">${c}</p></div>`;
  let h = `<div class="c"><h2>Hasil IST</h2><div class="g">${card("IQ (norma usia)", fx(a.iq), e(a.iqc || "di bawah rentang norma"))}${card("IQ (norma pendidikan)", fx(ed.iq), e(ed.iqc || "di bawah rentang norma"))}${card("Dominasi", e(r.dom), "GE+RA vs AN+ZR")}${card("Skor mentah total", r.tot, "dari 9 subtes")}</div><p class="m">Norma usia: kelompok ≤ ${e(r.band)} tahun. Norma pendidikan: ${e(ed.grp)}${rec.edu ? " (" + e(rec.edu) + ")" : ""}. Gesamt SW (usia): ${fx(a.gs)}.</p></div>`;
  h += `<div class="c"><h2>Profil skor per subtes</h2>${profileChart(a, ed)}<p class="m">Titik penuh = norma usia, titik putih putus-putus = norma pendidikan. Pita warna menunjukkan kategori SW.</p></div>`;
  h += `<div class="c"><h2>Posisi IQ</h2>${iqChart(a.iq, ed.iq)}<p class="m">Kategori IQ: ${e(a.iqc || "di bawah rentang norma")} (usia), ${e(ed.iqc || "di bawah rentang norma")} (pendidikan).</p></div>`;
  h += `<div class="c"><h2>Kelompok kemampuan</h2>${groupChart(a.sw)}<p class="m">Rata-rata SW norma usia: Verbal (SE, WA, AN, GE), Numerik (RA, ZR), Figural (FA, WU), Daya ingat (ME).</p></div>`;
  h += `<div class="c"><h2>Tabel skor per subtes</h2><div class="tw"><table><tr><th>Subtes</th><th>RW</th><th>SW usia</th><th>Kategori</th><th>SW pend.</th></tr>${SUBL.map(k => `<tr><td>${e(NM(k))}</td><td>${r.rw[k]}</td><td>${fx(a.sw[k])}</td><td>${e(a.cat[k] || "-")}</td><td>${fx(ed.sw[k])}</td></tr>`).join("")}</table></div><p class="m">RW = skor mentah, SW = skor standar. Skor mentah di atas batas subtes (20, GE 32) tidak dapat dinormakan dan ditampilkan “-”.</p></div>`;
  h += `<div class="c"><h2>Kesimpulan otomatis</h2><p>Kemampuan umum (norma usia): IQ <b>${fx(a.iq)}</b>${a.iqc ? " (" + e(a.iqc) + ")" : ""}; menurut norma pendidikan IQ <b>${fx(ed.iq)}</b>${ed.iqc ? " (" + e(ed.iqc) + ")" : ""}.</p>${hi.length ? `<p>Subtes tertinggi: ${hi.map(k => `<b>${e(NM(k))}</b> (SW ${a.sw[k]}, ${e(SUBS[k].d)})`).join("; ")}.</p><p>Subtes terendah: ${lo.map(k => `<b>${e(NM(k))}</b> (SW ${a.sw[k]}, ${e(SUBS[k].d)})`).join("; ")}.</p>` : ""}<p>Pola dominasi: <b>${e(r.dom)}</b> (jumlah SW GE+RA dibanding AN+ZR; selisih ≤ 10 dianggap seimbang).</p><p class="m">Kesimpulan dibuat otomatis dari skor dan perlu dilengkapi interpretasi psikolog.</p></div>`;
  return h;
}
const sum = rec => rec.ist ? `<h3>IST</h3><ul><li>IQ (norma usia): <b>${fx(rec.ist.age.iq)}</b> ${e(rec.ist.age.iqc || "")}</li><li>IQ (norma pendidikan): <b>${fx(rec.ist.edu.iq)}</b> ${e(rec.ist.edu.iqc || "")}</li><li>Dominasi: <b>${e(rec.ist.dom)}</b></li></ul>` : "";
const ring = rec => rec.ist ? "IQ " + fx(rec.ist.age.iq) + " / " + (rec.ist.age.iqc || "-") : "-";
const csv = rec => rec.ist ? [rec.ist.age.iq ?? "", rec.ist.edu.iq ?? "", rec.ist.tot, rec.ist.dom] : ["", "", "", ""];

window.IST = { begin, rec, body, sum, ring, csv, EDU, _t: { SE, WA, AN, GE, RA, ZR, SUBS } };
})();
