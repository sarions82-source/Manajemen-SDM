/* WPT (Wonderlic Personnel Test, Form A): 50 soal, 12 menit.
   Kunci jawaban TIDAK ada di berkas ini: skor dihitung di server (app/Support/WptKey.php). */
(function () {
"use strict";
const e = s => String(s ?? "").replace(/[&<>"]/g, c => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;" }[c]));

const SAME = ["memiliki arti yang sama", "memiliki arti yang berlawanan", "tidak memiliki arti sama atau berlawanan"];
const TF = ["benar", "salah", "tidak tahu"];
/* kind: mc (pilihan angka), yn (YA/TIDAK), bs (B/S), num (ketik angka), txt (ketik huruf), set (ketik beberapa angka) */
const mc = (q, o, x) => Object.assign({ k: "mc", q, o }, x || {});
const Q = [
  mc("Bulan lalu pada awal tahun ini adalah", ["Januari", "Maret", "Juli", "Desember", "Oktober"]),
  mc("MENANGKAP adalah lawan kata dari", ["meletakkan", "membebaskan", "beresiko", "berusaha", "turun tingkat"]),
  mc("Sebagian besar hal di bawah ini serupa satu sama lain. Manakah salah satu di antaranya yang kurang serupa dengan yang lain?", ["Januari", "Agustus", "Rabu", "Oktober", "Desember"]),
  { k: "yn", q: "Jawablah dengan YA atau TIDAK. Apakah RSVP berarti ‘jawablah yang tidak perlu’?" },
  mc("Dalam kelompok kata berikut, manakah kata yang berbeda dari kata yang lain?", ["pasukan", "liga", "berpartisipasi", "pak", "kelompok"]),
  mc("BIASA adalah lawan kata dari", ["jarang", "terbiasa", "tetap", "berhenti", "selalu"]),
  mc("Gambar manakah yang terbuat dari dua gambar di dalam tanda kurung?", ["1", "2", "3", "4", "5"], { img: "wpt_07.png", short: 1 }),
  { k: "num", q: "Perhatikan urutan angka berikut. Angka berapa yang selanjutnya muncul?  8  4  2  1  ½  ¼  ?" },
  mc("Klien dan Pelanggan. Apakah kata-kata ini:", SAME),
  mc("Manakah kata berikut ini yang berhubungan dengan aroma saat gigi mengunyah?", ["manis", "bau tak sedap", "bau wangi", "hidung", "bersih"]),
  mc("MUSIM GUGUR adalah lawan dari:", ["liburan", "musim panas", "musim semi", "musim dingin", "musim gugur"]),
  { k: "num", q: "Sebuah pesawat terbang 300 kaki dalam ½ detik. Pada kecepatan yang sama berapa kaki ia terbang dalam 10 detik?" },
  mc("Anggaplah dua pernyataan pertama adalah benar. Apakah yang terakhir: benar, salah, atau tidak tahu?<br><i>Anak-anak lelaki ini adalah anak yang normal. Semua anak normal sifatnya aktif. Anak-anak lelaki ini aktif.</i>", TF, { tf: 1 }),
  mc("JAUH adalah lawan kata dari", ["terpencil", "dekat", "jauh", "terburu-buru", "pasti"]),
  { k: "num", q: "3 permen lemon seharga 10 rupiah. Berapa harga ½ lusin?" },
  { k: "num", q: "Berapa banyak duplikasi yang sama dari lima pasangan angka di bawah ini?<br><span class=\"tt\">84721 &nbsp; 84721<br>9210651 &nbsp; 9210561<br>14201201 &nbsp; 14210210<br>96101101 &nbsp; 96101161<br>88884444 &nbsp; 88884444</span>" },
  { k: "txt", q: "Misalkan Anda menyusun kata-kata berikut sehingga menjadi pernyataan yang benar. Lalu tuliskan <b>huruf terakhir</b> dari kata terakhir sebagai jawaban.<br><i>Selalu sebuah kata kerja kalimat suatu memiliki</i>", hint: "Satu huruf" },
  { k: "num", q: "Anak lelaki berumur 5 tahun dan saudara perempuannya dua kali lebih tua. Ketika anak lelaki itu berumur 8 tahun, berapa umur saudara perempuannya?" },
  mc("IT'S &nbsp; ITS. Apakah kata ini:", SAME),
  mc("Anggaplah dua pernyataan pertama adalah benar. Apakah pernyataan terakhir: benar, salah, atau tidak tahu?<br><i>John seusia dengan Sally. Sally lebih muda dari Bill. John lebih muda dari Bill.</i>", TF, { tf: 1 }),
  { k: "num", q: "Seorang dealer membeli beberapa barrel seharga 4.000 rupiah. Ia menjual dengan harga 5.000 rupiah, mendapat untung 50 rupiah setiap barrel. Berapa banyak barel yang dijual?" },
  { k: "bs", q: "Misalkan Anda menyusun kata-kata berikut sehingga menjadi kalimat lengkap. Jika kalimat itu benar, pilih B. Jika salah, pilih S.<br><i>telur menghasilkan semua ayam</i>" },
  { k: "set", n: 2, q: "Dua dari peribahasa berikut ini memiliki arti sama. Manakah itu?", o: ["Semakin banyak memiliki sapi, akan memiliki satu anak sapi yang buruk.", "Anak seperti Ayahnya.", "Bila tertinggal sama jauhnya dengan satu mil", "Seorang dikenal dari persahabatan yang dijalin", "Mereka adalah benih dari mangkuk yang sama."] },
  { k: "num", q: "Sebuah jam terlambat 1 menit 18 detik dalam 39 hari. Berapa detik ia terlambat dalam sehari?" },
  mc("CANVASS &nbsp; CANVAS. Apakah kata-kata ini:", SAME),
  mc("Anggaplah dua pernyataan pertama adalah benar. Pernyataan terakhir: benar, salah, atau tidak tahu?<br><i>Semua siswa mengikuti ujian. Beberapa orang di ruangan ini adalah siswa. Beberapa orang di ruangan ini mengikuti ujian.</i>", TF, { tf: 1 }),
  { k: "num", q: "Dalam 30 hari seorang menabung 1 dolar. Berapa rata-rata tabungannya setiap hari?", hint: "Boleh pecahan, mis. 1/4" },
  mc("INGENIOUS &nbsp; INGENUOUS. Apakah kata-kata ini:", SAME),
  { k: "num", q: "Dua orang menangkap 36 ikan. X menangkap 5 kali lebih banyak dari Y. Berapa ikan yang ditangkap Y?" },
  { k: "num", q: "Sebuah kotak segi empat, yang terisi penuh, memuat 800 kubik kaki gandum. Jika satu kotak lebarnya 8 kaki dan panjangnya 10 kaki, berapa kedalaman kotak itu?" },
  { k: "num", q: "Satu angka dari rangkaian berikut tidak cocok dengan pola angka yang lainnya. Angka berapakah itu?<br><span class=\"tt\">½ &nbsp; ¼ &nbsp; 1/6 &nbsp; 1/8 &nbsp; 1/9 &nbsp; 1/12</span>", hint: "Boleh pecahan, mis. 1/4" },
  { k: "yn", q: "Jawablah pertanyaan ini dengan YA atau TIDAK. Apakah P.M. berarti ‘post merediem’?" },
  mc("DAPAT DIPERCAYA &nbsp; GAMPANG PERCAYA. Apakah kata-kata ini:", SAME),
  { k: "num", q: "Sebuah rok membutuhkan 2 ¼ meter kain. Berapa banyak potong yang dihasilkan dari 45 meter kain?" },
  { k: "num", q: "Sebuah jam menunjuk tepat pada pukul 12 siang hari Senin. Pada pukul 2 siang hari Rabu, jam itu terlambat 26 detik. Pada rata-rata yang sama, berapa banyak jam itu terlambat dalam ½ jam?", hint: "Boleh pecahan, mis. 1/4" },
  { k: "num", q: "Tim bisbol kami kalah 9 permainan dalam musim ini. Ini merupakan 3/8 bagian dari semua pertandingan mereka. Berapa banyak pertandingan yang mereka mainkan dalam musim kompetisi saat ini?" },
  { k: "num", q: "Apakah angka selanjutnya dari seri ini?  1  .5  .25  .125  ?" },
  { k: "set", n: 2, img: "wpt_38.png", q: "Bentuk geometris ini dapat dibagi oleh suatu garis lurus menjadi dua bagian yang dapat disatukan dengan suatu cara hingga membentuk bujur sangkar yang sempurna. Garis menghubungkan dua dari angka-angka yang ada. Tuliskan kedua angka tersebut sebagai jawaban." },
  mc("Apakah arti dari kalimat berikut: sama, berlawanan, atau tidak sama atau berlawanan?<br><i>Sebuah sapu yang baru menyapu dengan bersih. Sepatu yang sudah lama sifatnya makin lunak.</i>", ["sama", "berlawanan", "tidak sama atau berlawanan"]),
  { k: "num", q: "Berapa duplikasi dari pasangan kata berikut ini?<br><span class=\"tt\">Rexford, J.D. &nbsp; Rockford, J.D<br>Singleton, M.O. &nbsp; Simbleten, M.O.<br>Richards, W.E. &nbsp; Richad, W.E.<br>Siegel, A.B. &nbsp; Seigel, A.B.<br>Wood, A.O. &nbsp; Wood, A.O.</span>" },
  { k: "set", n: 2, q: "Dua dari peribahasa ini memiliki makna yang serupa. Manakah itu?", o: ["Anda tidak dapat membuat dompet sutra dari kuping babi betina.", "Orang yang mencuri telur akan mencuri sapi.", "Batu yang berguling tidak akan mengumpulkan lumut", "Anda tidak mungkin menghancurkan kapal yang sudah rusak.", "Ini ketidakmungkinan yang terjadi."] },
  { k: "set", n: 2, img: "wpt_42.png", q: "Gambar geometris ini dapat dibagi dengan garis lurus menjadi dua bagian yang dapat disatukan untuk membentuk sebuah bujur sangkar yang sempurna. Garis menghubungkan dua angka. Tuliskan kedua angka itu sebagai jawaban." },
  { k: "num", q: "Dalam kelompok angka berikut ini, manakah angka yang terkecil?  10  1  .999  .33  11" },
  mc("Apakah makna dari kalimat berikut: sama, berlawanan, atau tidak sama atau berlawanan?<br><i>Tidak ada orang jujur meminta maaf atas kejujurannya. Kejujuran dihormati dan lapar pujian.</i>", ["sama", "berlawanan", "tidak sama atau berlawanan"]),
  { k: "num", q: "Dengan harga 1.80 dolar, seorang grosir membeli satu kardus buah yang berisi 12 lusin. Ia tahu dua lusin akan busuk sebelum dia menjualnya. Dengan harga berapa per lusin dia harus menjual jeruk itu untuk mendapat 1/3 dari harga seluruhnya?" },
  mc("Dalam rangkaian kata berikut ini, manakah kata yang berbeda dari yang lainnya?", ["koloni", "perkawanan", "kawanan", "kru", "konstelasi"]),
  mc("Anggaplah dua pernyataan pertama ini benar. Apakah pernyataan terakhir: benar, salah, atau tidak tahu?<br><i>Orang besar dibodohi. Saya dibodohi. Saya adalah orang besar.</i>", TF, { tf: 1 }),
  { k: "num", q: "Tiga orang membentuk kemitraan dan setuju membagi keuntungan secara rata. X menginvestasi 4.500 dolar, Y sebesar 3.500 dolar, dan Z sebesar 2.000 dolar. Jika keuntungan mencapai 1.500 dolar, lebih kurang berapa yang akan diperoleh X dibanding jika keuntungan dibagi berdasarkan besarnya investasi?" },
  { k: "set", n: 4, img: "wpt_49.png", q: "Empat dari 5 bagian ini dapat digabungkan untuk membuat segitiga. Manakah keempat gambar ini?" },
  { k: "num", q: "Untuk mencetak sebuah artikel berisi 30.000 kata, sebuah percetakan memutuskan untuk memakai dua ukuran jenis. Dengan menggunakan tipe yang lebih besar, sebuah halaman tercetak akan memuat 1.200 kata. Dengan tipe yang lebih kecil, sebuah halaman memuat 1.500 kata. Artikel ini masuk dalam 22 halaman di majalah. Berapa banyak halaman yang dibutuhkan untuk tipe yang lebih kecil?" }
];

/* ===================== ALUR ===================== */
let A = [], pg = 0, started = false;
const PER = 10, NP = 5;
let tleft = 0;

function begin() {
  A = Array(50).fill(""); pg = 0; started = false;
  t0 = Date.now(); clearInterval(tk);
  intro();
}
function intro() {
  V.innerHTML = `<div class="c"><h3>WPT: Wonderlic Personnel Test (Form A)</h3><p>Ini adalah tes kemampuan memecahkan masalah. Tes mencakup berbagai jenis pertanyaan yang harus diselesaikan <b>tanpa kalkulator</b> atau alat bantu sejenis.</p><p>Tes berisi <b>50 pertanyaan</b> yang bertahap makin sulit. Anda tidak mungkin menyelesaikan semuanya, tetapi kerjakan semampu Anda. Waktu Anda <b>${T.menit} menit</b> untuk memberi jawaban benar sebanyak mungkin. Kerjakan dengan teliti, namun jangan terlalu lama pada satu pertanyaan; lewati dan lanjutkan bila perlu. Anda boleh kembali ke halaman sebelumnya selama waktu masih ada.</p><h4>Contoh pertanyaan (tidak dinilai)</h4><ul><li>MENUAI adalah lawan kata dari: 1. mendapat 2. bersorak 3. melanjutkan 4. berada 5. menabur. Jawabannya <b>5</b> (menabur).</li><li>Harga setiap kotak ‘paper clip’ adalah 23 rupiah. Berapa harga 4 kotak? Jawabannya <b>92</b>.</li><li>MINER &nbsp; MINOR. Apakah kata-kata ini: 1. memiliki arti sama 2. memiliki arti berlawanan 3. tidak memiliki arti sama atau berlawanan? Jawabannya <b>3</b>.</li></ul><p class="m">Waktu berjalan setelah tombol Mulai ditekan dan tidak dapat dijeda.</p><div class="row"><span></span><button id="wpt_go">Mulai tes</button></div></div>`;
  document.getElementById("wpt_go").onclick = start;
}
function start() {
  started = true; tleft = T.menit * 60; t0 = Date.now();
  clearInterval(tk);
  tk = setInterval(() => {
    tleft--;
    const el = document.getElementById("wptm");
    if (el) el.textContent = fmt(Math.max(tleft, 0));
    if (tleft <= 0) { clearInterval(tk); finish(true); }
  }, 1000);
  draw();
}
const answered = () => A.filter(x => String(x).trim() !== "").length;

function item(i) {
  const q = Q[i], v = A[i], n = i + 1;
  let b = `<div class="pr" id="wq${i}"><b style="display:inline">${n}.</b> ${q.q}`;
  if (q.img) b += `<div><img src="/wpt/${q.img}" alt="Gambar soal ${n}" style="max-width:100%;height:auto;display:block;margin:8px 0"></div>`;
  if (q.k === "mc") {
    if (q.short) b += `<div>${q.o.map((o, j) => `<button class="s ra ${v === String(j + 1) ? "on" : ""}" data-i="${i}" data-v="${j + 1}" style="padding:10px 18px;margin:2px">${e(o)}</button>`).join("")}</div>`;
    else b += `<div>${q.o.map((o, j) => `<button class="o ${v === String(j + 1) ? "on" : ""}" data-i="${i}" data-v="${j + 1}"><b>${j + 1}.</b> ${e(o)}</button>`).join("")}</div>`;
  } else if (q.k === "yn") {
    b += `<div>${[["ya", "YA"], ["tidak", "TIDAK"]].map(([x, l]) => `<button class="o ${v === x ? "on" : ""}" data-i="${i}" data-v="${x}" style="display:inline-block;width:auto;margin-right:6px">${l}</button>`).join("")}</div>`;
  } else if (q.k === "bs") {
    b += `<div>${[["b", "B (benar)"], ["s", "S (salah)"]].map(([x, l]) => `<button class="o ${v === x ? "on" : ""}" data-i="${i}" data-v="${x}" style="display:inline-block;width:auto;margin-right:6px">${l}</button>`).join("")}</div>`;
  } else {
    if (q.k === "set" && q.o) b += `<ol style="margin:6px 0 6px 18px">${q.o.map(o => `<li>${e(o)}</li>`).join("")}</ol>`;
    const hint = q.k === "set" ? `Tulis ${q.n === 4 ? "empat" : "dua"} angka, pisahkan dengan koma` : q.k === "txt" ? q.hint : (q.hint || "Tulis angka jawaban");
    b += `<div><input class="wpt-in" data-i="${i}" maxlength="24" placeholder="${e(hint)}" value="${e(v)}" autocomplete="off" ${q.k === "txt" ? "" : 'inputmode="text"'} style="margin-top:6px;max-width:300px"></div>`;
  }
  return b + "</div>";
}

function draw() {
  const from = pg * PER;
  let b = ""; for (let i = from; i < from + PER; i++) b += item(i);
  V.innerHTML = `<div class="c"><div class="row m" style="position:sticky;top:0;background:var(--card);padding:6px 0;z-index:5"><span>Soal ${from + 1} – ${from + PER} dari 50</span><span class="tt" id="wptm">${fmt(Math.max(tleft, 0))}</span><span id="wptc">${answered()} dijawab</span></div>${b}<div class="row" style="margin-top:14px"><button class="s" id="wpt_pv" ${pg ? "" : "disabled"}>Sebelumnya</button><span class="m" id="wptw"></span><button id="wpt_nx">${pg < NP - 1 ? "Berikutnya" : "Selesai"}</button></div></div>`;
  const upd = () => { document.getElementById("wptc").textContent = answered() + " dijawab"; };
  V.querySelectorAll("[data-v]").forEach(x => x.onclick = () => {
    const i = +x.dataset.i; A[i] = A[i] === x.dataset.v ? "" : x.dataset.v;
    V.querySelectorAll(`[data-i="${i}"][data-v]`).forEach(y => y.classList.toggle("on", y.dataset.v === A[i]));
    upd();
  });
  V.querySelectorAll(".wpt-in").forEach(x => x.oninput = () => { A[+x.dataset.i] = x.value; upd(); });
  document.getElementById("wpt_pv").onclick = () => { pg--; draw(); };
  const nx = document.getElementById("wpt_nx");
  nx.onclick = () => {
    if (pg < NP - 1) { pg++; draw(); return; }
    const m = 50 - answered();
    if (m && !nx.dataset.w) { nx.dataset.w = 1; document.getElementById("wptw").textContent = m + " soal belum dijawab. Klik Selesai lagi untuk mengakhiri."; return; }
    clearInterval(tk); finish(false);
  };
  window.scrollTo(0, 0);
}
function rec() { return { A: A.slice() }; }

/* ===================== HASIL ===================== */
const IQB = [[40, 65, "#e53935", "Defective"], [65, 79, "#fb8c00", "Borderline"], [79, 90, "#fdd835", "Low Avg"], [90, 110, "#9ccc65", "Average"], [110, 119, "#66bb6a", "Hi Avg"], [119, 127, "#43a047", "Sup."], [127, 139, "#1e88e5", "V. Sup."], [139, 160, "#5e35b1", "Genius"]];
function iqChart(iq) {
  const x0 = 16, x1 = 504, yb = 40, h = 26, lo = 40, hi = 160, X = v => x0 + (Math.max(lo, Math.min(hi, v)) - lo) / (hi - lo) * (x1 - x0);
  let g = `<svg viewBox="0 0 520 112" width="100%" role="img" aria-label="Posisi IQ">`;
  IQB.forEach(([f, t, c, n]) => { g += `<rect x="${X(f)}" y="${yb}" width="${X(t) - X(f)}" height="${h}" fill="${c}"/><text x="${(X(f) + X(t)) / 2}" y="${yb + h + 13}" text-anchor="middle" fill="currentColor" font-size="9">${n}</text>`; });
  [40, 65, 90, 110, 120, 140, 160].forEach(v => { g += `<text x="${X(v)}" y="${yb - 4}" text-anchor="middle" fill="currentColor" font-size="9" opacity=".7">${v}</text>`; });
  g += `<path d="M${X(iq)} ${yb - 12}l-7 -12h14z" fill="#1e63c7"/><text x="${Math.min(Math.max(X(iq), 40), 480)}" y="${yb - 28}" text-anchor="middle" fill="currentColor" font-size="12" font-weight="700">IQ ${iq}</text></svg>`;
  return g;
}
const JF = [["Executive", 28], ["Managerial (mid-level)", 26], ["Technical (professional)", 26], ["Supervisor (first line)", 22], ["Technical (mid-level)", 22], ["Sales", 22], ["Office / Clerical", 21], ["Skilled Trades", 18], ["Unskilled Trades", 15]];
function jobChart(rs) {
  const lab = 150, bw = 280, H = 24, mx = 50, X = v => lab + v / mx * bw;
  let g = `<svg viewBox="0 0 520 ${JF.length * H + 28}" width="100%" role="img" aria-label="Skor dibanding skor minimum kelompok jabatan">`;
  JF.forEach(([n, v], i) => {
    const y = i * H + 4, hit = rs >= v;
    g += `<text x="0" y="${y + 15}" fill="currentColor" font-size="11">${n}</text><rect x="${lab}" y="${y + 3}" width="${bw}" height="14" rx="3" fill="#9aa4b2" fill-opacity=".25"/><rect x="${lab}" y="${y + 3}" width="${v / mx * bw}" height="14" rx="3" fill="${hit ? "#43a047" : "#e53935"}"/><text x="${lab + bw + 8}" y="${y + 15}" fill="currentColor" font-size="11" font-weight="700">${v} ${hit ? "✓" : ""}</text>`;
  });
  const xr = X(Math.min(rs, mx));
  g += `<line x1="${xr}" x2="${xr}" y1="0" y2="${JF.length * H + 6}" stroke="#1e63c7" stroke-width="2.4"/><text x="${xr}" y="${JF.length * H + 20}" text-anchor="middle" fill="currentColor" font-size="11" font-weight="700">Skor Anda: ${rs}</text></svg>`;
  return g;
}
function blockChart(ok) {
  const W = 520, bw = 70, H = 150, y1 = 120, mx = 10;
  let g = `<svg viewBox="0 0 ${W} ${H + 20}" width="100%" role="img" aria-label="Jumlah benar per kelompok 10 soal">`;
  for (let b = 0; b < 5; b++) {
    const c = ok.slice(b * 10, b * 10 + 10).reduce((p, q) => p + q, 0), h = c / mx * 90, x = 30 + b * 96;
    g += `<rect x="${x}" y="${y1 - 90}" width="${bw}" height="90" rx="4" fill="#9aa4b2" fill-opacity=".22"/><rect x="${x}" y="${y1 - h}" width="${bw}" height="${h}" rx="4" fill="${c >= 7 ? "#43a047" : c >= 4 ? "#fdd835" : "#e53935"}"/><text x="${x + bw / 2}" y="${y1 - h - 5}" text-anchor="middle" fill="currentColor" font-size="12" font-weight="700">${c}</text><text x="${x + bw / 2}" y="${y1 + 16}" text-anchor="middle" fill="currentColor" font-size="11">${b * 10 + 1}–${b * 10 + 10}</text>`;
  }
  return g + `</svg>`;
}
function mapGrid(ok, last) {
  let g = `<div style="display:grid;grid-template-columns:repeat(10,1fr);gap:4px;max-width:480px">`;
  ok.forEach((v, i) => { const n = i + 1, ans = n <= last; g += `<div title="Soal ${n}" style="text-align:center;font-size:12px;padding:6px 0;border-radius:4px;color:#fff;background:${v ? "#43a047" : ans ? "#e53935" : "#9aa4b2"}">${n}</div>`; });
  return g + `</div><p class="m">Hijau = benar, merah = salah, abu-abu = tidak dijawab (setelah soal terakhir yang dijawab atau dilewati).</p>`;
}

function body(rec) {
  const r = rec.wpt;
  if (!r) return `<div class="c"><h2>Hasil WPT</h2><p class="m">Skor WPT dihitung di server dan belum tersedia untuk catatan ini. Muat ulang daftar hasil atau pastikan hasil tersimpan.</p></div>`;
  const dj = r.ok.slice(0, r.last).reduce((p, q) => p + q, 0), acc = r.last ? Math.round(dj / r.last * 100) : 0;
  const meet = JF.filter(x => r.rs >= x[1]).map(x => x[0]), no = JF.filter(x => r.rs < x[1]).map(x => x[0]);
  const card = (t, b, c) => `<div><p class="m" style="margin:0">${t}</p><p class="tt" style="font-size:22px;margin:0">${b}</p><p class="m" style="margin:0">${c}</p></div>`;
  let h = `<div class="c"><h2>Hasil WPT</h2><div class="g">${card("Skor (jumlah benar)", r.rs + " / 50", "")}${card("IQ", r.iq, e(r.iqc))}${card("Soal terakhir dijawab", r.last, "dari 50")}${card("Salah atau kosong", r.wrong, "")}</div></div>`;
  h += `<div class="c"><h2>Posisi IQ</h2>${iqChart(r.iq)}<p class="m">Kategori: ${e(r.iqc)}. IQ diperoleh dari tabel konversi skor mentah (0–50) pada berkas Excel WPT.</p></div>`;
  h += `<div class="c"><h2>Skor dibanding skor minimum kelompok jabatan</h2>${jobChart(r.rs)}<p class="m">Batang = skor minimum tiap kelompok jabatan (tabel Job Families pada berkas Excel). Hijau = skor Anda mencapai minimum, merah = belum. Garis biru = skor Anda.</p><details><summary class="m">Tabel acuan lengkap dari berkas Excel</summary><img src="/wpt/wpt_ref_median.png" alt="Median skor pelamar per jabatan" style="max-width:100%;height:auto;margin:8px 0"><img src="/wpt/wpt_ref_jobfam.png" alt="Job Families" style="max-width:100%;height:auto;margin:8px 0"></details></div>`;
  h += `<div class="c"><h2>Jawaban benar per kelompok soal</h2>${blockChart(r.ok)}<p class="m">Soal makin sulit dari awal ke akhir, jadi penurunan bertahap itu wajar.</p><h3>Peta jawaban</h3>${mapGrid(r.ok, r.last)}</div>`;
  h += `<div class="c"><h2>Kesimpulan otomatis</h2><p>Skor <b>${r.rs}</b> dari 50 (IQ <b>${r.iq}</b>, ${e(r.iqc)}). Soal terakhir yang dijawab adalah nomor <b>${r.last}</b>; dari soal yang dicoba, ketepatan sekitar <b>${acc}%</b>.</p><p>${meet.length ? "Skor mencapai minimum untuk: " + meet.map(e).join(", ") + "." : "Skor belum mencapai minimum untuk kelompok jabatan mana pun pada tabel acuan."}${no.length && meet.length ? " Belum mencapai: " + no.map(e).join(", ") + "." : ""}</p><p class="m">Kesimpulan dibuat otomatis dari skor dan perlu dilengkapi interpretasi psikolog.</p></div>`;
  return h;
}
const sum = rec => rec.wpt ? `<h3>WPT</h3><ul><li>Skor: <b>${rec.wpt.rs}</b> / 50</li><li>IQ: <b>${rec.wpt.iq}</b> ${e(rec.wpt.iqc)}</li></ul>` : "";
const ring = rec => rec.wpt ? rec.wpt.rs + "/50 · IQ " + rec.wpt.iq : "-";
const csv = rec => rec.wpt ? [rec.wpt.rs, rec.wpt.iq, rec.wpt.iqc, rec.wpt.last] : ["", "", "", ""];

window.WPT = { begin, rec, body, sum, ring, csv, _t: { Q } };
})();
