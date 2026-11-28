/* Asesmen gabungan: MBTI + LI (Leadership Inventory) + OCHAI + Self Assessment
   Satu tes, empat bagian berurutan. Hasil, grafik, dan kesimpulan dihitung otomatis.
   Kunci MBTI dan LI diambil dari berkas Skoring_MBTI_LI_DISC_1.xlsx. */
(function () {
"use strict";
const D = {"mbti":{"items":[["Spontan, Fleksibel, tidak diikat waktu","Terencana dan memiliki deadline jelas"],["Lebih memilih berkomunikasi dengan menulis","Lebih memilih berkomunikasi dengan bicara"],["Tidak menyukai hal-hal yang bersifat mendadak dan di luar perencanaan","Perubahan mendadak tidak jadi masalah"],["Obyektif","Subyektif"],["Menemukan dan mengembangkan ide dengan mendiskusikannya","Menemukan dan mengembangkan ide dengan merenungkan"],["Bergerak dari gambaran umum baru ke detail","Bergerak dari detail ke gambaran umum sebagai kesimpulan akhir"],["Berorientasi pada dunia eksternal (kegiatan, orang)","Berorientasi pada dunia internal (memori, pemikiran, ide)"],["Berbicara mengenai masalah yang dihadapi hari ini dan langkah-langkah praktis mengatasinya","Berbicara mengenai visi masa depan dan konsep-konsep mengenai visi tersebut"],["Diyakinkan dengan penjelasan yang menyentuh perasaan","Diyakinkan dengan penjelasan yang masuk akal"],["Fokus pada sedikit hobi namun mendalam","Fokus pada banyak hobi secara luas dan umum"],["Tertutup dan mandiri","Sosial dan ekspresif"],["Aturan, jadwal dan target sangat mengikat dan membebani","Aturan, jadwal dan target akan sangat membantu dan memperjelas tindakan"],["Menggunakan pengalaman sebagai pedoman","Menggunakan imajinasi dan perenungan sebagai pedoman"],["Berorientasi tugas dan job description","Berorientasi pada manusia dan hubungan"],["Pertemuan dengan orang lain dan aktivitas sosial melelahkan","Bertemu orang dan aktivitas sosial membuat bersemangat"],["SOP sangat membantu","SOP sangat membosankan"],["Mengambil keputusan berdasar logika dan aturan main","Mengambil keputusan berdasar perasaan pribadi dan kondisi orang lain"],["Bebas dan dinamis","Prosedural dan tradisional"],["Berorientasi pada hasil","Berorientasi pada proses"],["Beraktifitas sendirian di rumah menyenangkan","Beraktifitas sendirian di rumah membosankan"],["Membiarkan orang lain bertindak bebas asalkan tujuan tercapai","Mengatur orang lain dengan tata tertib agar tujuan tercapai"],["Memilih ide inspiratif lebih penting daripada fakta","Memilih fakta lebih penting daripada ide inspiratif"],["Mengemukakan tujuan dan sasaran lebih dahulu","Mengemukakan kesepakatan terlebih dahulu"],["Fokus pada target dan mengabaikan hal-hal baru","Memperhatikan hal-hal baru dan siap menyesuaikan diri serta mengubah target"],["Kontinuitas dan stabilitas lebih diutamakan","Perubahan dan variasi lebih diutamakan"],["Pendirian masih bisa berubah tergantung situasi nantinya","Berpegang teguh pada pendirian"],["Bertindak step by step dengan timeframe yang jelas","Bertindak dengan semangat tanpa menggunakan timeframe"],["Berinisiatif tinggi hampir dalam berbagai hal meskipun tidak berhubungan dengan dirinya","Berinisiatif bila situasi memaksa atau berhubungan dengan kepentingan sendiri"],["Lebih memilih tempat yang tenang dan pribadi untuk berkonsentrasi","Lebih memilih tempat yang ramai dan banyak interaksi / aktifitas"],["Menganalisa","Berempati"],["Berpikir secara matang sebelum bertindak","Berani bertindak tanpa terlalu lama berfikir"],["Menghargai seseorang karena sifat dan perilakunya","Menghargai seseorang karena skill dan faktor teknis"],["Merasa nyaman bila situasi tetap terbuka terhadap pilihan-pilihan lain","Merasa tenang bila semua sudah diputuskan"],["Menarik kesimpulan dengan lama dan hati-hati","menarik kesimpulan dengan cepat sesuai naluri"],["Mengekspresikan semangat","Menyimpan semangat dalam hati"],["Mengklarifikasi ide dan teori sebelum dipraktekkan","Memahami ide dan teori saat mempraktekkannya langsung"],["Melibatkan perasaan itu tidak profesional","Terlalu kaku pada peraturan dan pekerjaan itu kejam"],["Mencari kesempatan untuk berkomunikasi secara perorangan","Memilih berkomunikasi pada sekelompok orang"],["Yang penting situasi harmonis terjaga","Yang penting tujuan tercapai"],["Ketidakpastian itu seru, menegangkan dan membuat hati lebih senang","Ketidakpastian membuat bingung dan meresahkan"],["Berfokus pada masa kini (apa yang bisa diperbaiki sekarang)","Berfokus pada masa depan (apa yang mungkin dicapai di masa depan)"],["Mempertanyakan","Mengakomodasi"],["Secara konsisten mengamati dan mengingat detail","Mengamati dan mengingat detail hanya bila berhubungan dengan pola"],["Situasi last minute membuat bersemangat dan memunculkan potensi","Situasi last minute sangat menyiksa, membuat stress dan merupakan kesalahan"],["Lebih suka komunikasi tidak langsung (telp, surat, e-mail)","Lebih suka komunikasi langsung (tatap muka)"],["Praktis","Konseptual"],["Perubahan adalah musuh","Perubahan adalah semangat hidup"],["Sering dianggap keras kepala","Sering dianggap terlalu memihak"],["Bersemangat saat menolong orang keluar dari kesalahan dan meluruskan","Bersemangat saat mengkritik dan menemukan kesalahan"],["Bertindak sesuai situasi dan kondisi yang terjadi saat itu","Bertindak sesuai apa yang sudah direncanakan"],["Menggunakan keterampilan yang sudah dikuasai","Menyukai tantangan untuk menguasai keterampilan baru"],["Membangun ide pada saat berbicara","Membangun ide dengan matang baru membicarakannya"],["Memilih cara yang sudah ada dan sudah terbukti","Memilih cara yang unik dan belum dipraktekkan orang lain"],["Hidup harus sudah diatur dari awal","Hidup seharusnya mengalir sesuai kondisi"],["Standar harus ditegakkan di atas segalanya (itu menunjukkan kehormatan dan harga diri)","Perasaan manusia lebih penting dari sekadar standar (yang adalah benda mati)"],["Daftar dan checklist adalah panduan penting","Daftar dan checklist adalah tugas dan beban"],["Menuntut perlakuan yang adil dan sama pada semua orang","Menuntut perlakuan khusus sesuai karakteristik masing-masing orang"],["Mementingkan sebab-akibat","Mementingkan nilai-nilai personal"],["Puas ketika mampu beradaptasi dengan momentum yang terjadi","Puas ketika mampu menjalankan semuanya sesuai rencana"],["Spontan, Easy Going, fleksibel","Berhati-hati, penuh pertimbangan, kaku"]],"key":[[[60,"B"],[52,"B"],[45,"A"],[38,"A"],[35,"B"],[31,"A"],[29,"A"],[28,"B"],[20,"A"],[15,"A"],[11,"A"],[10,"A"],[7,"B"],[5,"B"],[2,"A"]],[[53,"A"],[51,"A"],[46,"A"],[43,"A"],[41,"A"],[36,"A"],[34,"A"],[27,"A"],[25,"A"],[22,"B"],[18,"B"],[16,"A"],[13,"A"],[8,"A"],[6,"B"]],[[58,"A"],[57,"A"],[55,"A"],[49,"B"],[48,"A"],[42,"A"],[39,"B"],[37,"A"],[23,"A"],[32,"B"],[30,"A"],[17,"A"],[9,"B"],[4,"A"],[14,"A"]],[[59,"B"],[56,"A"],[54,"A"],[50,"B"],[47,"A"],[44,"B"],[40,"B"],[33,"B"],[26,"B"],[24,"A"],[21,"B"],[19,"A"],[12,"B"],[3,"A"],[1,"B"]]],"types":{"ISTJ":["Bertanggungjawab","Serius, tenang, stabil & damai. Senang pada fakta, logis, obyektif, praktis & realistis. Task oriented, tekun, teratur, menepati janji, dapat diandalkan & bertanggung jawab. Pendengar yang baik, setia, hanya mau berbagi dengan orang dekat. Memegang aturan, standar & prosedur dengan teguh.","Belajarlah memahami perasaan & kebutuhan orang lain. Kurangi keinginan untuk mengontrol orang lain atau memerintah mereka untuk menegakkan aturan. Lihatlah lebih banyak sisi positif pada orang lain atau hal lainnya. Terbukalah terhadap perubahan.","Bidang Manajemen, Polisi, Intelijen, Hakim, Pengacara, Dokter, Akuntan (Staf Keuangan), Programmer atau yang berhubungan dengan IT, System Analys, Pemimpin Militer","ESFP atau ESTP"],"ISFJ":["Setia","Penuh pertimbangan, hati-hati, teliti dan akurat. Serius, tenang, stabil namun sensitif. Ramah, perhatian pada perasaan & kebutuhan orang lain, setia, kooperatif, pendengar yang baik. Punya kemampuan mengorganisasi, detail, teliti, sangat bertanggungjawab & bisa diandalkan.","Lihat lebih dalam, lebih antusias, & lebih semangat. Belajarlah mengatakan ”tidak”. Jangan menyenangkan semua orang atau Anda dianggap plin plan. Jangan terjebak zona nyaman dan rutinitas. Cobalah hal baru. Ada banyak hal menyenangkan yang mungkin belum pernah Anda coba.","Architect, Interior Designer, Perawat, Administratif, Designer, Child Care, Konselor, Back Office Manager, Penjaga Toko / Perpustakaan, Dunia Perhotelan.","ESFP atau ESTP"],"ISTP":["Pragmatis","Tenang, pendiam, cenderung kaku, dingin, hati-hati, penuh pertimbangan. Logis, rasional, kritis, obyektif, mampu mengesampingkan perasaan. Mampu menghadapi perubahan mendadak dengan cepat dan tenang. Percaya diri, tegas dan mampu menghadapi perbedaan maupun kritik. Mampu menganalisa, mengorganisir, & mendelegasikan. Problem solver yang baik terutama untuk masalah teknis & keadaan mendadak.","Observasilah kehidupan sosial, apa yang membuat orang marah, cinta, senang, termotivasi & terapkan pada hubungan Anda. Belajarlah untuk mengenali perasaan Anda dan mengekspresikannya. Jadilah orang yang lebih terbuka, keluar dari zona nyaman, eksplorasi ide baru, dan berdiskusi dengan orang lain.  Jangan mencari-cari kesalahan orang hanya untuk menyelesaikan masalahnya. Jangan menyimpan informasi yang harusnya dibagi dan belajarlah mempercayakan tanggungjawab pada orang lain.","Polisi, Ahli Forensik, Programmer, Ahli Komputer, System Analyst, Teknisi, Insinyur, Mekanik, Pilot, Atlit, Entrepreneur","ESTJ atau ENTJ"],"ISFP":["Artistik","Berpikiran simpel & praktis, fleksibel, sensitif, ramah, tidak menonjolkan diri, rendah hati pada kemampuannya. Menghindari konflik, tidak memaksakan pendapat atau nilai-nilainya pada orang lain. Biasanya tidak mau memimpin tetapi menjadi pengikut dan pelaksana yang setia. Seringkali santai menyelesaikan sesuatu, karena sangat menikmati apa yang terjadi saat ini. Menunjukkan perhatian lebih banyak melalui tindakan dibandingkan kata-kata.","Jangan takut pada penolakan dan konflik. Anda tidak perlu menyenangkan semua orang. Cobalah untuk mulai memikirkan dampak jangka panjang dari keputusan-keputusan kecil di hari ini. Asah dan kembangkan sisi kreatifitas dan seni dalam diri Anda sebagai modal bagus dalam diri Anda. Cobalah untuk lebih terbuka dan mengekspresikan perasaan Anda.","Seniman, Designer, Pekerja Sosial, Konselor, Psikolog, Guru, Aktor, Bidang Hospitality","ESFJ atau ENFJ"],"INFJ":["Reflektif","Perhatian, empati, sensitif & berkomitmen terhadap sebuah hubungan. Sukses karena ketekunan, originalitas dan keinginan kuat untuk melakukan apa saja yang diperlukan termasuk memberikan yg terbaik dalam pekerjaan. Idealis, perfeksionis, memegang teguh prinsip. Visioner, penuh ide, kreatif, suka merenung dan inspiring. Biasanya diikuti dan dihormati karena kejelasan visi serta dedikasi pada hal-hal baik.","Seimbangkan cara pandang Anda. Jangan hanya melihat sisi negatif & resiko. Namun, lihatlah sisi positif dan peluangnya. Bersabarlah, jangan mudah marah dan menyalahkan orang lain atau situasi. Rileks dan jangan terus menerus berfikir atau menyelesaikan tanggungjawab.","Pengajar, Psikolog, Dokter, Konselor, Pekerja Sosial, Fotografer, Seniman, Designer, Child Care.","ESFP atau ESTP"],"INTJ":["Independen","Visioner, punya perencanaan praktis, & biasanya memiliki ide-ide original serta dorongan kuat untuk mencapainya. Mandiri dan percaya diri. Punya kemampuan analisa yang bagus serta menyederhanakan sesuatu yang rumit dan abstrak menjadi sesuatu yang praktis, mudah difahami & dipraktekkan. Skeptis, kritis, logis, menentukan (determinatif) dan kadang keras kepala. Punya keinginan untuk berkembang serta selalu ingin lebih maju dari orang lain. Kritik & konflik tidak menjadi masalah berarti.","Belajarlah mengungkapkan emosi & perasaan Anda. Cobalah untuk lebih terbuka pada dunia luar, banyak bergaul, banyak belajar, banyak membaca, mengunjungi banyak tempat, eksplorasi hal baru, & memperluas wawasan. Hindari perdebatan tidak penting. Belajarlah untuk berempati, memberi perhatian dan lebih peka terhadap orang lain.","Peneliti, Ilmuwan, Insinyur, Teknisi, Pengajar, Profesor, Dokter, Research & Development, Business Analyst, System Analyst, Pengacara, Hakim, Programmers, Posisi Strategis dalam organisasi.","ENFP atau ENTP"],"INFP":["Idealis","Sangat perhatian dan peka dengan perasaan orang lain. Penuh dengan antusiasme dan kesetiaan, tapi biasanya hanya untuk orang dekat. Peduli pada banyak hal. Cenderung mengambil terlalu banyak dan menyelesaikan sebagian. Peduli pada banyak hal. Cenderung mengambil terlalu banyak dan menyelesaikan sebagian. Cenderung idealis dan perfeksionis. Berpikir win-win solution, mempercayai dan mengoptimalkan orang lain.","Belajarlah menghadapi kritik. Jika baik maka kritik itu bisa membangun Anda, namun jika tidak abaikan saja. Jangan ragu pula untuk bertanya dan minta saran. Belajarlah untuk bersikap tegas. Jangan selalu berperasaan dan menyenangkan orang dengan tindakan baik. Bertindak baik itu berbeda dengan bertindak benar. Jangan terlalu menyalahkan diri dan bersikap terlalu keras pada diri sendiri. Kegagalan adalah hal biasa dan semua orang pernah mengalaminya. Jangan terlalu baik pada orang lain tapi melupakan diri sendiri. Anda juga punya tanggungjawab untuk berbuat baik pada diri sendiri.","Penulis, Sastrawan, Konselor, Psikolog, Pengajar, Seniman, Rohaniawan, Bidang Hospitality","ENFJ atau ESFJ"],"INTP":["Konseptual","Sangat menghargai intelektualitas dan pengetahuan. Menikmati hal-hal teoritis dan ilmiah. Senang memecahkan masalah dengan logika dan analisa. Diam dan menahan diri. Lebih suka bekerja sendiri. Cenderung kritis, skeptis, mudah curiga dan pesimis. Tidak suka memimpin dan bisa menjadi pengikut yang tidak banyak menuntut. Cenderung memiliki minat yang jelas. Membutuhkan karir dimana minatnya bisa berkembang dan bermanfaat. Jika menemukan sesuatu yang menarik minatnya, ia akan sangat serius dan antusias menekuninya.","Belajarlah membangun hubungan dengan orang lain. Belajar berempati, mendengar aktif, memberi perhatian dan bertukar pendapat. Relaks. Jangan terlalu banyak berfikir. Nikmati hidup Anda tanpa harus bertanya mengapa dan bagaimana. Cobalah menemukan satu ide, merencanakan dan mewujudkannya. Jangan terlalu sering berganti-ganti ide tetapi tidak satupun yang terwujud.","Ilmuwan, Fotografer, Programmer, Ahli komputer, System Analyst, Penulis Buku Teknis, Ahli Forensik, Jaksa, Pengacara, Teknisi","ENTJ atau ESTJ"],"ESTP":["Spontan","Spontan, Aktif, Enerjik, Cekatan, Cepat, Sigap, Antusias, Fun dan penuh variasi. Komunikator, asertif, to the point, ceplas-ceplos, berkarisma, punya interpersonal skill yang baik. Baik dalam pemecahan masalah langsung di tempat. Mampu menghadapi masalah, konflik dan kritik. Tidak khawatir, menikmati apapun yang terjadi.Cenderung untuk menyukai sesuatu yang mekanistis, kegiatan bersama dan olahraga. Mudah beradaptasi, toleran, pada umumnya konservatif tentang nilai-nilai. Tidak suka penjelasan terlalu panjang. Paling baik dalam hal-hal nyata yang dapat dilakukan.","Saran Pengembangan: Belajarlah memahami perasaan dan pemikiran orang lain terutama saat bicara dengan mereka. Belajarlah untuk sabar, menikmati proses, tidak semua hal bisa dicapai dengan cepat. Sesekali luangkan waktu untuk merenung dan merencanakan masa depan Anda. Cobalah untuk mencatat pengamatan-pengamatan Anda termasuk detailnya.","Marketing, Sales, Polisi, Entrepreneur, Pialang Saham, Technical Support","ISFJ atau ISTJ"],"ESFP":["Murah Hati","Outgoing, easygoing, mudah berteman, bersahabat, sangat sosial, ramah, hangat, & menyenangkan. Optimis, ceria, antusias, fun, menghibur, suka menjadi perhatian. Punya interpersonal skill yang baik, murah hati, mudah simpatik dan mengenali perasaan orang lain. Menghindari konflik dan menjaga keharmonisan suatu hubungan. Menghindari konflik dan menjaga keharmonisan suatu hubungan. Mengetahui apa yang terjadi di sekelilingnya dan ikut serta dalam kegiatan tersebut. Sangat baik dalam keadaan yang membutuhkan common sense, tindakan cepat dan ketrampilan praktis.","Jangan terburu-buru dalam mengambil keputusan. Belajarlah untuk fokus dan tidak mudah berubah-ubah terutama untuk hal yang penting. Jangan menyenangkan semua orang. Begitu pula sebaliknya, tidak semua orang bisa menyenangkan Anda. Belajarlah menghadapi kritik dan konflik. Jangan lari. Anda punya kecenderungan meterialistis. Hati-hati, tidak semua hal bisa diukur dengan materi ataupun uang.","Entertainer, Seniman, Marketing, Konselor, Designer, Tour Guide, Bidang Anak-anak, Bidang Hospitality","ISTJ atau ISFJ"],"ENFP":["Optimis","Ramah, hangat, enerjik, optimis, antusias, semangat tinggi, fun. Imaginatif, penuh ide, kreatif, inovatif. Mampu beradaptasi dengan beragam situasi dan perubahan. Pandai berkomunikasi, senang bersosialisasi & membawa suasana positif. Mudah membaca perasaan dan kebutuhan orang lain.","Belajarlah untuk fokus, disiplin, tegas dan konsisten Belajarlah untuk menghadapi konflik dan kritik. Pikirkan kebutuhan diri sendiri. Jangan melupakannya karena terlalu peduli pada kebutuhan orang lain. Jangan terlalu boros. Belajarlah untuk mengelola keuangan sedikit demi sedikit.","Konselor, Psikolog, Entertainer, Pengajar, Motivator, Presenter, Reporter, MC, Seniman, Hospitality","INTJ atau INFJ"],"ENTP":["Inovatif – Kreatif","Gesit, kreatif, inovatif, cerdik, logis, baik dalam banyak hal. Banyak bicara dan punya kemampuan debat yang baik. Bisa berargumentasi untuk senang-senang saja tanpa merasa bersalah. Fleksibel. Punya banyak cara untuk memecahkan masalah dan tantangan. Fleksibel. Kurang konsisten. Cenderung untuk melakukan hal baru yang menarik hati setelah melakukan sesuatu yang lain. Punya keinginan kuat untuk mengembangkan diri.","Cobalah untuk win-win solution. Jangan ingin menang sendiri. Belajarlah untuk disiplin dan konsisten. Hindari perdebatan tidak penting. Hindari perdebatan tidak penting. Belajarlah untuk sedikit waspada. Seimbangkan cara pandang Anda agar tidak terlalu optimis dan mengambil resiko yang tidak realistis. Belajarlah untuk memberi perhatian pada perasaan orang lain.","Pengacara, Psikolog, Konsultan, Ilmuwan, Aktor,Marketing, Programmer, Fotografer","INFJ atau INTJ"],"ESTJ":["Konservatif – Disiplin","Praktis, realistis, berpegang pada fakta, dengan dorongan alamiah untuk bisnis dan mekanistis. Sangat sistematis, procedural dan terencana. Disiplin, on time dan pekerja keras. Konservatif dan cenderung kaku. Tidak tertarik pada subject yang tidak berguna baginya, tapi dapat menyesuaikan diri jika diperlukan. Senang mengorganisir sesuatu. Bisa menjadi administrator yang baik jika mereka ingat untuk memperhatikan perasaan dan perspektif orang lain.","Kurangi keinginan untuk mengontrol dan memaksa orang lain. Belajarlah untuk mengontrol emosi dan amarah Anda. Cobalah untuk introspeksi diri dan meluangkan waktu sejenak untuk merenung. Belajarlah untuk lebih sabar dan low profile Belajarlah untuk memahami orang lain.","Militer, Manajer, Polisi, Hakim, Pengacara, Guru, Sales, Auditor, Akuntan, System Analyst","ISTP atau INTP"],"ESFJ":["Harmonis","Hangat, banyak bicara, populer, dilahirkan untuk bekerjasama, suportif dan anggota kelompok yang aktif. Membutuhkan keseimbangan dan baik dalam menciptakan harmoni. Selalu melakukan sesuatu yang manis bagi orang lain. Kerja dengan baik dalam situasi yang mendukung dan memujinya. Santai, easy going, sederhana, tidak berfikir panjang. Teliti dan rajin merawat apa yang ia miliki.","Jangan mengorbankan diri hanya untuk menyenangkan orang lain. Jangan mengukur harga diri Anda dari perlakuan, penghargaan dan pujian orang lain. Mintalah pertimbangan orang lain dalam mengambil keputusan. Belajarlah untuk lebih tegas. Terima tanggungjawab hidup dan belajarlah untuk lebih dewasa. Jangan mengasihani diri sendiri. Hadapi kritik dan konflik, jangan lari.","Perencana Keuangan, Perawat, Guru, Bidang anak-anak, Konselor, Administratif, Hospitality","ISFP atau INFP"],"ENFJ":["Meyakinkan","Kreatif, imajinatif, peka, sensitive, loyal. Pada umumnya peduli pada apa kata orang atau apa yang orang lain inginkan dan cenderung melakukan sesuatu dengan memperhatikan perasaan orang lain. Pandai bergaul, meyakinkan, ramah, fun, populer, simpatik. Responsif pada kritik dan pujian. Menyukai variasi dan tantangan baru. Butuh apresiasi dan penerimaan.","Jangan mengorbankan diri hanya untuk menyenangkan orang lain. Jangan mengukur harga diri Anda dari perlakuan orang lain. Jangan mudah kecewa jika mereka tidak seperti yang Anda inginkan. Belajarlah untuk tegas dan mengambil keputusan. Menghadapi kritik dan konflik. Jangan terlalu bersikap keras terhadap diri sendiri.","Konsultan, Psikolog, Konselor, Pengajar, Marketing, HRD, Event Coordinator, Entertainer, Penulis, Motivator","INFP atau ISFP"],"ENTJ":["Pemimpin Alami","Tegas, asertif, to the point, jujur terus terang, obyektif, kritis, & punya standard tinggi. Dominan, kuat kemauannya, perfeksionis dan kompetitif. Tangguh, disiplin, dan sangat menghargai komitmen. Cenderung menutupi perasaan dan menyembunyikan kelemahan. Berkarisma, komunikasi baik, mampu menggerakkan orang. Berbakat pemimpin.","Belajarlah untuk relaks. Tidak perlu perfeksionis dan selalu kompetitif dengan semua orang. Ungkapkan perasaan Anda. Menyatakan perasaan bukanlah kelemahan. Belajarlah mengelola emosi Anda. Jangan mudah marah. Belajarlah untuk menghargai dan mengapresiasi orang lain. Jangan terlalu arogan dan menganggap remeh orang lain. Lihat sisi positifnya. Jangan hanya melihat benar dan salah saja.","Entrepreneur, Pengacara, Hakim, Konsultan, Pemimpin Organisasi, Business analyst, Bidang Finansial","INTP atau ISTP"]}},"li":{"items":[["Saya dengan cepat melihat dan mengambil keuntungan/manfaat dari peluang-peluang yang baru.","Saya dapat bekerjasama dengan baik dengan berbagai karakter dan tipe orang.","Memberikan ide-ide adalah salah satu kemampuan alamiah saya.","Saya senang melibatkan seseorang setelah saya tahu bahwa orang tersebut memiliki nilai lebih bagi organisasi.","Saya selalu konsisten dalam menunjukkan efektivitas diri dalam bekerja.","Saya siap menghadapi hal-hal yang dianggap tidak populer jika hal itu memang akan memberikan hasil yang sepadan.","Saya biasanya dapat mengenali apakah sesuatu hal itu realistis atau tidak (akan berjalan baik atau tidak).","Saya dapat memberikan pertimbangan rasional atas setiap pilihan tindakan yang diambil tanpa menimbulkan bias atau prasangka."],["Saya tidak suka mengikuti suatu pertemuan, kecuali agendanya jelas, terstruktur, terkendali dan secara umum dilaksanakan dengan tertib.","Saya cenderung menjadi murah hati terhadap orang lain yang memiliki pandangan yang bagus tetapi belum pernah diberi kesempatan yang sepantasnya.","Saya cenderung menjadi banyak bicara saat tim membicarakan ide-ide baru.","Bila tujuan ke depan kami berbeda, saya menjadi kurang bersemangat untuk bekerjasama dengan para rekan kerja saya.","Saya kadang-kadang kelihatan seperti suka memaksakan kehendak, jika saya melihat sesuatu memang harus/perlu dilakukan.","Saya merasa sulit untuk tampil memimpin suatu kegiatan.","Kadang-kadang saya terburu-buru menyampaikan suatu ide namun belakangan justru saya menjadi kehilangan arah mengenai perkembangan ide tersebut.","Rekan kerja biasa melihat saya sebagai orang yang terlalu khawatir dengan hal-hal yang detil dan sesuatu yang buruk yang mungkin dapat terjadi."],["Saya memiliki kemampuan alamiah mempengaruhi orang lain tanpa mereka merasa dipaksa.","Kehati-hatian saya mencegah tim melakukan kesalahan dan kealpaan.","Saya siap memberikan desakan untuk segera mengambil tindakan agar tim tidak membuang-buang waktu.","Saya dapat diandalkan untuk memberikan pemikiran yang orisinil.","Saya selalu siap mendengarkan saran-saran yang baik demi kepentingan bersama.","Saya antusias untuk melihat ide-ide baru dan perkembangan mutakhir.","Saya yakin kapasitas saya dalam memberikan pertimbangan dapat membantu tim mengambil keputusan yang tepat.","Saya dapat diandalkan untuk memastikan seluruh pekerjaan penting telah diatur atau ditata dengan baik."],["Saya memiliki keinginan yang kuat untuk mengenal rekan kerja lebih baik/dekat.","Saya tidak segan-segan untuk menantang pendapat orang lain atau mengemukakan pendapat saya yang tidak populer.","Saya dapat memberikan suatu argumentasi yang tepat untuk menantang usulan-usulan yang lemah.","Saya yakin saya punya kemampuan yang cukup baik untuk menjalankan suatu rencana.","Saya cenderung menghindari hal yang biasa-biasa saja tetapi sebaliknya saya suka membuat kejutan.","Saya cenderung perfeksionis (ingin sempurna) dalam melaksanakan setiap pekerjaan.","Saya siap menggunakan akses dengan pihak-pihak diluar tim.","Ketika saya mempertimbangkan semua sudut pandang, saya tidak lagi ragu-ragu untuk mengambil keputusan yang harus diambil."],["Saya menikmati pekerjaan menganalisa situasi dan menimbang-nimbang semua kemungkinan.","Saya senang mencari solusi praktis suatu persoalan.","Saya senang merasa menjadi orang yang selalu mendorong terjadinya hubungan kerja yang baik.","Saya dapat memberi pengaruh yang kuat dalam pengambilan keputusan.","Saya dapat bertemu dengan orang-orang yang memiliki sesuatu yang baru untuk ditawarkan.","Saya dapat meyakinkan orang lain untuk setuju mengambil tindakan yang perlu dilakukan.","Saya dapat memberikan perhatian penuh pada tugas yang saya laksanakan.","Saya suka pada bidang pekerjaan yang dapat memperluas kreativitas saya."],["Saya menarik diri sejenak untuk mencari jalan keluar.","Saya siap bekerja dengan orang yang menunjukkan sikap positif.","Saya mencari cara untuk mengefisienkan pekerjaan dengan memanfaatkan apa yang terbaik yang dapat dikontribusikan oleh setiap orang dalam tim.","‘Sense of Urgency’ (perasaan genting) yang saya miliki dapat membantu untuk memastikan pekerjaan tim tepat waktu.","Saya yakin dapat mempertahankan ketenangan dan menjaga kemampuan saya untuk berpikir jernih.","Saya tetap fokus pada tujuan saat menghadapi tekanan.","Saya siap mengambil peran sebagai pemimpin, jika saya melihat tim tidak menunjukkan kemajuan.","Saya membuka diskusi untuk mendorong pemikiran-pemikiran baru  dan mendapatkan sesuatu untuk bergerak maju."],["Saya biasanya menunjukkan kekurangsabaran saya terhadap mereka yang menghalangi kemajuan.","Orang lain mengkritik saya sebagai orang yang terlalu analitis dan kurang intuitif.","Keinginan saya untuk memastikan segala sesuatu berjalan baik kadang-kadang dapat menghambat kemajuan.","Saya mudah merasa bosan, dan cenderung bergantung pada satu atau dua orang yang berpengaruh untuk memotivasi saya.","Saya agak susah untuk memulai sesuatu kecuali jika ada tujuan yang sangat jelas.","Saya terkadang tidak dapat menjelaskan dengan baik dan mengklarifikasi hal-hal rumit yang saya hadapi.","Saya suka menuntut orang lain untuk melakukan sesuatu yang tidak bisa saya kerjakan sendiri.","Saya menjadi ragu untuk menyampaikan pendapat saya ketika berhadapan dengan pihak-pihak yang berseberangan /bertentangan dengan saya."]],"key":[["d","f","e","g","h","c","a","b"],["b","e","h","a","d","g","c","f"],["a","c","b","h","g","d","f","e"],["h","b","f","d","c","e","g","a"],["f","d","g","b","a","h","e","c"],["c","g","d","f","e","a","h","b"],["g","a","c","e","b","f","d","h"]],"roles":["COORDINATOR","DRIVER","FINISHER","IMPLEMENTOR","MONITOR/EVALUATOR","ORIGINATOR","RESOURCE INVESTIGATOR","SUPPORTER"],"info":{"COORDINATOR":["tenang, percaya diri, terkendali","kapasitas untuk memperlakukan dan menyambut naik semua kontributor potensial tanpa prasangka; objektifitas yang kuat","tidak suka membuat keputusan atau memberikan saran"],"DRIVER":["Bersemangat tinggi, mudah bergaul, dinamis","memicu dan siap menghadapi tantangan, ketidak efektifan dan mampu menekan keinginan pribadi","langsung memprovokasi orang lain, mudah tersinggung dan tidak sabar"],"FINISHER":["sangat hati-hati, teratur, waspada, gelisah","kapasitas untuk menjadi panutan, perdeksionis","cenderung mengkhawatikan hal-hal kecil; enggan mendelegasi"],"IMPLEMENTOR":["Tradisional, dutiful, mudah ditebak, handal, setia","kemampuan mengorganisasi, pragmatis, pekerja keras, disiplin tinggi","tidak luwes, tidak memberikan tanggapan atas ide yang belum terbukti berhasil"],"MONITOR/EVALUATOR":["serius, tidak emosional, penuh perhitungan","pertimbangan yang kuat, taktis, berpendirian teguh","kurang inspiratis dan kurang dapat memoticasi orang lain"],"ORIGINATOR":["Individualis, serius, tidak kolot","Inovatif, intelektual, kaya akan pengetahuan","mengabaikan hal-hal yang rinci, tidak membumi"],"RESOURCE INVESTIGATOR":["Ekstrovert, antusias, ingin tahu, komunikatif","kemampuan tinggi dalam berinteraksi denagn orang lain dan suka mendalamni hal-hal yang baru; siap menghadapi tantangan","Mudah kehilangan semangat/antusiasme bila semua aktifitas selesai dijalankan"],"SUPPORTER":["tenang, sensitif, pengamat, diplomatis","sigap menganggapi situasi dan membantu oirang lain dan mendukung semangat tim","Ragu-ragu bertindak bila dalam keadaan krisis"]}}};

const e = s => String(s ?? "").replace(/[&<>"]/g, c => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;" }[c]));
const rd = (n, k = 1) => Math.round(n * Math.pow(10, k)) / Math.pow(10, k);
const num = v => (typeof v === "number" && isFinite(v) ? v : 0);
const tc = s => String(s).toLowerCase().replace(/(^|[\s/])([a-z])/g, (m, a, b) => a + b.toUpperCase());

const DIM = [
  ["I", "E", "Introvert", "Ekstrovert"],
  ["S", "N", "Sensing", "Intuition"],
  ["T", "F", "Thinking", "Feeling"],
  ["J", "P", "Judging", "Perceiving"]
];
const SEC = ["MBTI", "LI (Leadership Inventory)", "OCHAI (budaya organisasi)", "Self Assessment"];
const INTRO = [
  "Untuk setiap nomor, pilih SATU pernyataan (A atau B) yang paling sesuai dengan diri Anda. Semua nomor harus diisi.",
  "Bagikan TEPAT 10 poin pada setiap bagian ke pernyataan yang paling menggambarkan diri Anda. Poin boleh diberikan semuanya ke satu pernyataan atau dibagi ke beberapa pernyataan. Pernyataan yang tidak sesuai dibiarkan 0.",
  "Untuk setiap pertanyaan, bagikan TEPAT 100 poin ke empat pernyataan (A sampai D), dua kali: untuk kondisi organisasi SAAT INI dan kondisi yang ANDA HARAPKAN. Makin sesuai, makin besar poinnya.",
  "Beri nilai 1 (sangat rendah) sampai 5 (sangat tinggi) untuk setiap kompetensi sesuai penilaian Anda terhadap diri sendiri. Tanda bersifat opsional: ＋ untuk kekuatan utama, － untuk area yang perlu dikembangkan."
];

/* ---- OCHAI: 6 dimensi x 4 pernyataan (A Clan, B Adhocracy, C Market, D Hierarchy) ---- */
const OT = [
  { k: "A", n: "Clan", s: "Kekeluargaan" },
  { k: "B", n: "Adhocracy", s: "Inovatif" },
  { k: "C", n: "Market", s: "Berorientasi hasil" },
  { k: "D", n: "Hierarchy", s: "Terstruktur" }
];
const OQ = [
  { t: "Karakteristik utama organisasi", o: [
    "Organisasi terasa seperti keluarga besar. Orang-orangnya terbuka dan banyak berbagi tentang dirinya.",
    "Organisasi dinamis dan berjiwa wirausaha. Orang berani mengambil risiko dan mencoba hal baru.",
    "Organisasi berorientasi pada hasil. Yang utama adalah menyelesaikan pekerjaan dan unggul dari pesaing.",
    "Organisasi terkendali dan terstruktur. Prosedur formal menentukan apa yang dikerjakan orang."] },
  { t: "Kepemimpinan di organisasi", o: [
    "Pemimpin umumnya dipandang sebagai pembimbing, fasilitator, atau pengayom.",
    "Pemimpin umumnya dipandang sebagai pencetus gagasan, inovator, dan pengambil risiko.",
    "Pemimpin umumnya dipandang sebagai pengejar target yang tegas dan menuntut hasil.",
    "Pemimpin umumnya dipandang sebagai pengatur dan koordinator yang menjaga kelancaran dan efisiensi."] },
  { t: "Cara organisasi mengelola karyawan", o: [
    "Ditandai kerja sama tim, kesepakatan bersama, dan keterlibatan semua orang.",
    "Ditandai kebebasan individu, keberanian mencoba, dan penghargaan pada keunikan.",
    "Ditandai persaingan yang ketat, target tinggi, dan tuntutan pencapaian.",
    "Ditandai rasa aman dalam bekerja, kepastian, dan hubungan kerja yang stabil."] },
  { t: "Perekat organisasi", o: [
    "Kesetiaan dan rasa saling percaya. Komitmen terhadap organisasi sangat tinggi.",
    "Komitmen pada inovasi dan pengembangan hal-hal baru. Menjadi yang terdepan itu penting.",
    "Penekanan pada pencapaian tujuan dan prestasi. Keberhasilan adalah pengikat utama.",
    "Aturan dan kebijakan formal. Menjaga organisasi tetap berjalan lancar itu penting."] },
  { t: "Penekanan strategis", o: [
    "Pengembangan manusia. Keterbukaan, kepercayaan, dan partisipasi terus didorong.",
    "Mencari sumber daya dan peluang baru. Mencoba hal-hal baru dihargai.",
    "Persaingan dan pencapaian. Target yang ambisius dan kemenangan di pasar ditekankan.",
    "Kesinambungan dan stabilitas. Efisiensi, kendali, dan kelancaran operasi ditekankan."] },
  { t: "Kriteria keberhasilan", o: [
    "Keberhasilan diukur dari pengembangan SDM, kerja tim, komitmen karyawan, dan kepedulian.",
    "Keberhasilan diukur dari produk atau layanan yang unik dan baru, serta menjadi pelopor.",
    "Keberhasilan diukur dari kemenangan di pasar dan keunggulan atas pesaing.",
    "Keberhasilan diukur dari efisiensi: pekerjaan tepat waktu, jadwal lancar, dan biaya rendah."] }
];
const OCHQ = OQ;

/* ---- Self Assessment: 20 kompetensi ---- */
const SA = [
  ["Integrity (Integritas)", "Bertindak jujur dan konsisten dengan nilai yang dianut, juga saat tidak diawasi."],
  ["Innovative (Inovatif)", "Mencetuskan dan menerapkan gagasan atau cara kerja yang baru."],
  ["People Development (Pengembangan SDM)", "Mendorong pembelajaran dan pertumbuhan karier orang lain dalam jangka panjang."],
  ["Directiveness (Mengarahkan)", "Memberi arahan yang jelas dan memastikan arahan itu dijalankan."],
  ["Entrepreneurship", "Melihat peluang dan berani mengambil risiko terukur demi hasil."],
  ["Expertise", "Menguasai pengetahuan dan keterampilan teknis di bidangnya dan terus memperbaruinya."],
  ["Impact & Influence", "Mempengaruhi pendapat dan tindakan orang lain secara efektif."],
  ["Relation Building", "Membangun dan menjaga hubungan kerja serta jaringan yang baik."],
  ["Team Leadership", "Memimpin, menyatukan, dan mengarahkan anggota tim ke tujuan bersama."],
  ["Conceptual Thinking", "Mengenali pola dan menghubungkan hal-hal terpisah menjadi gambaran besar."],
  ["Customer Service Orientation", "Memahami dan memenuhi kebutuhan pelanggan, internal maupun eksternal."],
  ["Strategic", "Berpikir jangka panjang dan menyusun langkah yang selaras dengan arah organisasi."],
  ["Organizational Awareness", "Memahami struktur, dinamika, dan budaya organisasi."],
  ["Self Confidence (Kepercayaan Diri)", "Yakin pada kemampuan sendiri dan berani mengambil keputusan."],
  ["Communication Skill", "Menyampaikan gagasan dengan jelas dan mendengarkan dengan baik."],
  ["Achievement Orientation", "Menetapkan target tinggi dan terdorong untuk mencapainya."],
  ["Analytical Thinking", "Menguraikan masalah dan menilai sebab-akibat secara logis."],
  ["Concern for Order", "Peduli pada ketepatan, kualitas, aturan, dan kerapian kerja."],
  ["Leading Result", "Mengarahkan tim dan proses agar target tercapai."],
  ["Developing Others", "Memberi umpan balik dan bimbingan yang spesifik untuk memperbaiki kinerja orang lain."]
];

/* ================= SKORING ================= */
function mbti(M) {
  M = Array.isArray(M) ? M : [];
  const L = [0, 0, 0, 0], R = [0, 0, 0, 0];
  let ans = 0;
  M.forEach(v => { if (v === 0 || v === 1) ans++; });
  D.mbti.key.forEach((k, d) => k.forEach(([n, c]) => {
    const v = M[n - 1];
    if (v !== 0 && v !== 1) return;
    if (v === (c === "A" ? 0 : 1)) L[d]++; else R[d]++;
  }));
  const pl = L.map(x => x / 15), pr = R.map(x => x / 15);
  const type = DIM.map((m, d) => (pl[d] > pr[d] ? m[0] : m[1])).join("");
  return { L, R, pl, pr, type, ans, info: D.mbti.types[type] || null };
}

function li(Lr) {
  Lr = Array.isArray(Lr) ? Lr : [];
  const tot = Array(8).fill(0), sec = [];
  D.li.key.forEach((letters, s) => {
    const a = Array.isArray(Lr[s]) ? Lr[s] : [];
    sec.push(a.reduce((x, y) => x + num(y), 0));
    letters.forEach((l, j) => { tot[j] += num(a[l.charCodeAt(0) - 97]); });
  });
  const rank = tot.map(v => 1 + tot.filter(x => x > v).length);
  const sum = tot.reduce((a, b) => a + b, 0);
  const okSec = sec.filter(x => x === 10).length;
  return { roles: D.li.roles, tot, rank, sum, sec, okSec };
}
function liTop(l) {
  const rows = l.roles.map((r, j) => ({ r, v: l.tot[j], k: l.rank[j] }));
  const top = rows.filter(x => x.k === 1);
  const nk = Math.min(...rows.filter(x => x.k > 1).map(x => x.k), 99);
  const nxt = rows.filter(x => x.k === nk);
  return { rows, top: l.sum ? top : [], nxt: l.sum ? nxt : [] };
}

function och(O) {
  const n = O && Array.isArray(O.n) ? O.n : [], x = O && Array.isArray(O.e) ? O.e : [];
  const avg = a => [0, 1, 2, 3].map(k => {
    let s = 0;
    for (let q = 0; q < 6; q++) s += num(a[q] && a[q][k]);
    return s / 6;
  });
  const sum = a => { let ok = 0; for (let q = 0; q < 6; q++) if (a[q] && a[q].reduce((p, c) => p + num(c), 0) === 100) ok++; return ok; };
  const now = avg(n), exp = avg(x);
  return { now, exp, okN: sum(n), okE: sum(x), has: now.some(v => v > 0) || exp.some(v => v > 0) };
}

function sa(S) {
  S = Array.isArray(S) ? S : [];
  const rows = SA.map((c, i) => {
    const s = S[i] || {};
    return { n: c[0], r: num(s.r), m: s.m === "+" || s.m === "-" ? s.m : "" };
  });
  const rated = rows.filter(x => x.r >= 1 && x.r <= 5);
  return { rows, rated: rated.length, avg: rated.length ? rated.reduce((a, b) => a + b.r, 0) / rated.length : 0 };
}

function inc(rec) {
  const m = mbti(rec.M), l = li(rec.L), o = och(rec.O), s = sa(rec.S);
  return (60 - m.ans) + (7 - l.okSec) + (6 - o.okN) + (6 - o.okE) + (20 - s.rated);
}

/* ================= KESIMPULAN OTOMATIS ================= */
function concl(rec) {
  const m = mbti(rec.M), l = li(rec.L), o = och(rec.O), s = sa(rec.S), out = [];
  if (m.ans === 60) {
    const best = DIM.map((d, k) => (m.pl[k] > m.pr[k] ? [d[2], m.pl[k]] : [d[3], m.pr[k]])).sort((a, b) => b[1] - a[1])[0];
    out.push(`<b>Kepribadian (MBTI):</b> tipe <b>${e(m.type)}</b>${m.info ? " (" + e(m.info[0]) + ")" : ""}. Kecenderungan paling kuat ada pada dimensi ${e(best[0])} (${Math.round(best[1] * 100)}%).`);
  }
  const t = liTop(l);
  if (t.top.length) {
    const info = D.li.info[t.top[0].r];
    out.push(`<b>Peran dalam tim (LI):</b> peran dominan <b>${t.top.map(x => e(tc(x.r))).join(" / ")}</b> (skor ${t.top[0].v})${t.nxt.length ? ", peran pendukung " + t.nxt.map(x => e(tc(x.r))).join(" / ") + " (skor " + t.nxt[0].v + ")" : ""}.${info ? " Ciri khas: " + e(info[0]) + ". Hal yang perlu diwaspadai: " + e(info[2]) + "." : ""}`);
  }
  if (o.has) {
    const iN = o.now.indexOf(Math.max(...o.now)), iE = o.exp.indexOf(Math.max(...o.exp));
    const gap = o.exp.map((v, k) => v - o.now[k]);
    const iG = gap.reduce((b, v, k) => (Math.abs(v) > Math.abs(gap[b]) ? k : b), 0);
    out.push(`<b>Budaya organisasi (OCHAI):</b> saat ini paling dominan tipe <b>${OT[iN].n}</b> (${OT[iN].s}, ${rd(o.now[iN])}%), sedangkan yang diharapkan <b>${OT[iE].n}</b> (${OT[iE].s}, ${rd(o.exp[iE])}%). Perubahan terbesar yang diharapkan: ${OT[iG].n} ${gap[iG] >= 0 ? "naik" : "turun"} ${rd(Math.abs(gap[iG]))} poin.`);
  }
  if (s.rated) {
    const hi = s.rows.filter(x => x.r >= 1).sort((a, b) => b.r - a.r).slice(0, 3);
    const lo = s.rows.filter(x => x.r >= 1).sort((a, b) => a.r - b.r).slice(0, 3);
    const plus = s.rows.filter(x => x.m === "+").map(x => x.n), minus = s.rows.filter(x => x.m === "-").map(x => x.n);
    out.push(`<b>Penilaian diri (Self Assessment):</b> rata-rata ${rd(s.avg)} dari 5. Dinilai tertinggi: ${hi.map(x => e(x.n) + " (" + x.r + ")").join(", ")}. Dinilai terendah: ${lo.map(x => e(x.n) + " (" + x.r + ")").join(", ")}.${plus.length ? " Ditandai kekuatan: " + plus.map(e).join(", ") + "." : ""}${minus.length ? " Ditandai perlu dikembangkan: " + minus.map(e).join(", ") + "." : ""}`);
  }
  if (m.ans === 60 && m.info) {
    out.push(`<b>Saran pengembangan:</b> ${e(m.info[2].replace(/^Saran Pengembangan:\s*/i, ""))}`);
    out.push(`<b>Profesi yang sesuai (MBTI):</b> ${e(m.info[3])}.`);
  }
  return out;
}

/* ================= TAMPILAN HASIL ================= */
function hbars(rows, max, valFmt) {
  const H = 22, lab = 170, bw = 230, h = rows.length * H + 6;
  return `<svg viewBox="0 0 460 ${h}" width="100%" role="img">${rows.map((r, i) => {
    const w = max ? Math.max(0, Math.min(1, r.v / max)) * bw : 0, y = i * H + 3;
    const nm = r.l.length > 30 ? r.l.slice(0, 29) + "…" : r.l;
    return `<text x="0" y="${y + 14}">${e(nm)}</text><rect x="${lab}" y="${y + 3}" width="${bw}" height="12" rx="3" fill="var(--bd)"/><rect x="${lab}" y="${y + 3}" width="${w}" height="12" rx="3" fill="${r.c || "var(--ac)"}"/><text x="${lab + bw + 6}" y="${y + 14}">${e(valFmt ? valFmt(r.v) : r.v)}</text>`;
  }).join("")}</svg>`;
}

function mbtiChart(m) {
  return DIM.map((d, k) => {
    const a = Math.round(m.pl[k] * 100), b = Math.round(m.pr[k] * 100);
    return `<div class="sc"><div class="row m" style="font-size:13px"><span style="${m.pl[k] > m.pr[k] ? "font-weight:700;color:var(--tx)" : ""}">${e(d[2])} (${d[0]}) ${a}%</span><span style="${m.pr[k] >= m.pl[k] ? "font-weight:700;color:var(--tx)" : ""}">${b}% ${e(d[3])} (${d[1]})</span></div><div style="display:flex;height:10px;border-radius:5px;overflow:hidden;background:var(--bd)"><i style="display:block;width:${a}%;background:var(--ac)"></i><i style="display:block;width:${b}%;background:var(--p)"></i></div></div>`;
  }).join("");
}

function ochChart(o) {
  const mx = Math.max(40, Math.ceil(Math.max(...o.now, ...o.exp) / 10) * 10), Y = v => 160 - (v / mx) * 130;
  let s = `<svg viewBox="0 0 320 205" width="100%" role="img">`;
  [0, 0.5, 1].forEach(f => { const v = mx * f, y = Y(v); s += `<line x1="26" x2="314" y1="${y}" y2="${y}" stroke="var(--bd)"/><text x="0" y="${y + 4}">${rd(v, 0)}</text>`; });
  OT.forEach((t, g) => {
    const x0 = 40 + g * 70;
    [[o.now[g], "var(--ac)"], [o.exp[g], "var(--p)"]].forEach(([v, c], j) => {
      const x = x0 + j * 26, y = Y(v);
      s += `<rect x="${x}" y="${y}" width="22" height="${160 - y}" rx="2" fill="${c}"/><text x="${x - 1}" y="${y - 4}">${rd(v)}</text>`;
    });
    s += `<text x="${x0}" y="178" class="tt">${t.n}</text>`;
  });
  s += `<rect x="40" y="190" width="10" height="10" fill="var(--ac)"/><text x="54" y="199">Saat ini</text><rect x="120" y="190" width="10" height="10" fill="var(--p)"/><text x="134" y="199">Diharapkan</text></svg>`;
  return s;
}

function body(rec) {
  const m = mbti(rec.M), l = li(rec.L), o = och(rec.O), s = sa(rec.S), tp = liTop(l), cc = concl(rec);
  const ic = inc(rec);
  let h = "";
  /* ringkasan */
  const iN = o.has ? o.now.indexOf(Math.max(...o.now)) : -1, iE = o.has ? o.exp.indexOf(Math.max(...o.exp)) : -1;
  const card = (a, b, c) => `<div><p class="m" style="margin:0">${a}</p><p class="tt" style="font-size:20px;margin:0">${b}</p><p class="m" style="margin:0">${c}</p></div>`;
  h += `<div class="c"><div class="g">${card("MBTI", m.ans === 60 ? e(m.type) : "-", m.info ? e(m.info[0]) : "")}${card("Peran LI", tp.top.length ? tp.top.map(x => e(tc(x.r))).join(" / ") : "-", tp.top.length ? "skor " + tp.top[0].v : "")}${card("Budaya saat ini", iN >= 0 ? OT[iN].n : "-", iN >= 0 ? "diharapkan: " + OT[iE].n : "")}${card("Self Assessment", s.rated ? rd(s.avg) + " / 5" : "-", s.rated + " dari 20 dinilai")}</div></div>`;
  /* kesimpulan */
  h += `<div class="c"><h2>Kesimpulan otomatis</h2>${cc.length ? cc.map(x => `<p>${x}</p>`).join("") : `<p class="m">Belum ada data yang cukup untuk disimpulkan.</p>`}<p class="m">Kesimpulan ini dibuat otomatis dari skor dan perlu dilengkapi interpretasi asesor.</p></div>`;
  /* MBTI */
  h += `<div class="c"><h2>MBTI</h2>${m.ans < 60 ? `<p class="tt" style="color:var(--k)">Terisi ${m.ans} dari 60 nomor. Hasil tidak dapat dianggap valid.</p>` : ""}<div class="g"><div><h3>Persentase dimensi</h3>${mbtiChart(m)}</div><div><h3>Tipe kepribadian</h3><p class="tt" style="font-size:28px;margin:0">${m.ans ? e(m.type) : "-"}</p>${m.info ? `<p class="tt">${e(m.info[0])}</p>` : ""}</div></div>${m.info ? `<h3>Karakteristik</h3><p>${e(m.info[1])}</p><h3>Saran pengembangan</h3><p>${e(m.info[2].replace(/^Saran Pengembangan:\s*/i, ""))}</p><h3>Profesi yang disarankan</h3><p>${e(m.info[3])}</p><h3>Pasangan atau partner alami</h3><p>${e(m.info[4])}</p>` : ""}<p class="m">Tipe ditentukan dari kecenderungan yang lebih besar pada tiap pasangan dimensi (I/E, S/N, T/F, J/P).</p></div>`;
  /* LI */
  const lrows = tp.rows.slice().sort((a, b) => b.v - a.v).map(r => ({ l: tc(r.r), v: r.v, c: r.k === 1 && l.sum ? "var(--p)" : "var(--ac)" }));
  h += `<div class="c"><h2>LI (Leadership Inventory)</h2>${l.okSec < 7 ? `<p class="tt" style="color:var(--k)">Hanya ${l.okSec} dari 7 bagian yang berjumlah tepat 10 poin. Hasil tidak dapat dianggap valid.</p>` : ""}<h3>Skor 8 peran (maksimal 70)</h3>${hbars(lrows, Math.max(20, ...l.tot), v => v)}<div class="tw"><table><tr><th>Peringkat</th><th>Peran</th><th>Skor</th></tr>${tp.rows.slice().sort((a, b) => a.k - b.k || b.v - a.v).map(r => `<tr><td>${l.sum ? r.k : "-"}</td><td>${e(tc(r.r))}</td><td>${r.v}</td></tr>`).join("")}</table></div>${[...tp.top, ...tp.nxt].slice(0, 2).map((x, ix) => { const f = D.li.info[x.r]; return f ? `<h3>${ix === 0 && tp.top.length ? "Peran dominan" : "Peran pendukung"}: ${e(tc(x.r))}</h3><div class="tw"><table><tr><th>Karakter khas</th><td style="white-space:normal">${e(f[0])}</td></tr><tr><th>Aspek positif</th><td style="white-space:normal">${e(f[1])}</td></tr><tr><th>Aspek negatif</th><td style="white-space:normal">${e(f[2])}</td></tr></table></div>` : ""; }).join("")}</div>`;
  /* OCHAI */
  h += `<div class="c"><h2>OCHAI (budaya organisasi)</h2>${o.okN < 6 || o.okE < 6 ? `<p class="tt" style="color:var(--k)">Kolom berjumlah 100: saat ini ${o.okN} dari 6, diharapkan ${o.okE} dari 6. Hasil tidak dapat dianggap valid.</p>` : ""}<div class="g"><div>${o.has ? ochChart(o) : `<p class="m">Belum diisi.</p>`}</div><div class="tw"><table><tr><th>Tipe</th><th>Saat ini</th><th>Diharapkan</th><th>Selisih</th></tr>${OT.map((t, k) => `<tr><td>${t.k}. ${t.n} <span class="m">(${t.s})</span></td><td>${rd(o.now[k])}</td><td>${rd(o.exp[k])}</td><td>${o.exp[k] - o.now[k] >= 0 ? "+" : ""}${rd(o.exp[k] - o.now[k])}</td></tr>`).join("")}</table></div></div><p class="m">Nilai adalah rata-rata dari 6 pertanyaan. Selisih positif berarti budaya tersebut diharapkan lebih kuat.</p></div>`;
  /* Self Assessment */
  const srows = s.rows.map(r => ({ l: (r.m === "+" ? "＋ " : r.m === "-" ? "－ " : "") + r.n, v: r.r, c: r.m === "-" ? "var(--k)" : r.m === "+" ? "var(--p)" : "var(--ac)" }));
  h += `<div class="c"><h2>Self Assessment</h2>${s.rated < 20 ? `<p class="tt" style="color:var(--k)">Terisi ${s.rated} dari 20 kompetensi. Hasil tidak dapat dianggap valid.</p>` : ""}${hbars(srows, 5, v => (v ? v : "-"))}<p class="m">＋ kekuatan utama (hijau) · － perlu dikembangkan (merah). Rata-rata ${s.rated ? rd(s.avg) : "-"} dari 5.</p></div>`;
  return h;
}

function sum(rec) {
  const m = mbti(rec.M), l = li(rec.L), o = och(rec.O), s = sa(rec.S), tp = liTop(l);
  const iN = o.has ? o.now.indexOf(Math.max(...o.now)) : -1, iE = o.has ? o.exp.indexOf(Math.max(...o.exp)) : -1;
  return `<h3>Asesmen kepribadian dan kepemimpinan</h3><ul><li>MBTI: <b>${m.ans === 60 ? e(m.type) : "-"}</b>${m.info ? " · " + e(m.info[0]) : ""}</li><li>Peran LI: <b>${tp.top.length ? tp.top.map(x => e(tc(x.r))).join(" / ") : "-"}</b>${tp.nxt.length ? " · pendukung: " + tp.nxt.map(x => e(tc(x.r))).join(" / ") : ""}</li><li>OCHAI: saat ini <b>${iN >= 0 ? OT[iN].n : "-"}</b>, diharapkan <b>${iE >= 0 ? OT[iE].n : "-"}</b></li><li>Self Assessment: rata-rata <b>${s.rated ? rd(s.avg) : "-"}</b> dari 5</li></ul>`;
}

function ring(rec) {
  const m = mbti(rec.M), tp = liTop(li(rec.L)), o = och(rec.O);
  const iN = o.has ? o.now.indexOf(Math.max(...o.now)) : -1;
  return (m.ans === 60 ? m.type : "-") + " / " + (tp.top.length ? tp.top.map(x => tc(x.r)).join("+") : "-") + " / " + (iN >= 0 ? OT[iN].n : "-");
}

function csv(rec) {
  const m = mbti(rec.M), tp = liTop(li(rec.L)), o = och(rec.O), s = sa(rec.S);
  return [
    m.ans === 60 ? m.type : "",
    tp.top.map(x => tc(x.r)).join("+"),
    o.has ? OT.map((t, k) => t.k + ":" + rd(o.now[k])).join(" ") : "",
    o.has ? OT.map((t, k) => t.k + ":" + rd(o.exp[k])).join(" ") : "",
    s.rated ? rd(s.avg, 2) : ""
  ];
}

/* ================= ALUR PENGERJAAN ================= */
let st = null, pos = 0;
const PAGES = [];
for (let p = 0; p < 6; p++) PAGES.push({ s: 0, p });
for (let p = 0; p < 7; p++) PAGES.push({ s: 1, p });
for (let p = 0; p < 6; p++) PAGES.push({ s: 2, p });
for (let p = 0; p < 2; p++) PAGES.push({ s: 3, p });

function fresh() {
  return {
    M: Array(60).fill(-1),
    L: Array.from({ length: 7 }, () => Array(8).fill(0)),
    O: { n: Array.from({ length: 6 }, () => [0, 0, 0, 0]), e: Array.from({ length: 6 }, () => [0, 0, 0, 0]) },
    S: Array.from({ length: 20 }, () => ({ r: 0, m: "" }))
  };
}
const clamp = (v, hi) => { v = Math.round(+v); return v >= 0 ? Math.min(v, hi) : 0; };
const tot = a => a.reduce((x, y) => x + y, 0);

function pageOk(pg) {
  if (pg.s === 0) { for (let n = pg.p * 10; n < pg.p * 10 + 10; n++) if (st.M[n] < 0) return false; return true; }
  if (pg.s === 1) return tot(st.L[pg.p]) === 10;
  if (pg.s === 2) return tot(st.O.n[pg.p]) === 100 && tot(st.O.e[pg.p]) === 100;
  for (let n = pg.p * 10; n < pg.p * 10 + 10; n++) if (!(st.S[n].r >= 1)) return false;
  return true;
}

function sync() {
  const pg = PAGES[pos];
  if (pg.s === 1) {
    const t = tot(st.L[pg.p]), el = document.getElementById("lt");
    if (el) { el.textContent = "Terpakai " + t + " dari 10 poin" + (t < 10 ? " · sisa " + (10 - t) : t > 10 ? " · kelebihan " + (t - 10) : " ✓"); el.style.color = t > 10 ? "var(--k)" : t === 10 ? "var(--p)" : ""; }
  } else if (pg.s === 2) {
    [["n", "otn"], ["e", "ote"]].forEach(([k, id]) => {
      const t = tot(st.O[k][pg.p]), el = document.getElementById(id);
      if (el) { el.textContent = t + " / 100"; el.style.color = t > 100 ? "var(--k)" : t === 100 ? "var(--p)" : ""; }
    });
  }
  const nx = document.getElementById("nx");
  if (nx) nx.disabled = !pageOk(pg);
}

function draw() {
  const pg = PAGES[pos], N = PAGES.length;
  let b = "";
  if (pg.s === 0) {
    for (let n = pg.p * 10; n < pg.p * 10 + 10; n++) {
      const it = D.mbti.items[n];
      b += `<div class="pr"><b>${n + 1}.</b><button class="o ${st.M[n] === 0 ? "on" : ""}" data-m="${n}" data-v="0"><b>A.</b> ${e(it[0])}</button><button class="o ${st.M[n] === 1 ? "on" : ""}" data-m="${n}" data-v="1"><b>B.</b> ${e(it[1])}</button></div>`;
    }
  } else if (pg.s === 1) {
    b += `<h3>Bagian ${pg.p + 1} dari 7</h3>` + D.li.items[pg.p].map((t, k) => `<div class="ln"><span><b>${"ABCDEFGH"[k]}.</b> ${e(t)}</span><input class="ai" type="number" inputmode="numeric" min="0" max="10" step="1" data-l="${k}" value="${st.L[pg.p][k] || ""}" placeholder="0" aria-label="Poin ${"ABCDEFGH"[k]}"></div>`).join("") + `<p class="tt" id="lt" style="margin-top:10px"></p>`;
  } else if (pg.s === 2) {
    const q = OCHQ[pg.p];
    b += `<h3>Pertanyaan ${pg.p + 1} dari 6: ${e(q.t)}</h3><div class="ln m" style="border-top:0"><span></span><span style="flex:none;width:72px;text-align:center">Saat ini</span><span style="flex:none;width:72px;text-align:center">Diharapkan</span></div>` + q.o.map((t, k) => `<div class="ln"><span><b>${OT[k].k}.</b> ${e(t)}</span><input class="ai" type="number" inputmode="numeric" min="0" max="100" step="1" data-o="n" data-k="${k}" value="${st.O.n[pg.p][k] || ""}" placeholder="0" aria-label="Saat ini ${OT[k].k}"><input class="ai" type="number" inputmode="numeric" min="0" max="100" step="1" data-o="e" data-k="${k}" value="${st.O.e[pg.p][k] || ""}" placeholder="0" aria-label="Diharapkan ${OT[k].k}"></div>`).join("") + `<div class="ln tt" style="border-top:2px solid var(--bd)"><span>Jumlah (harus 100)</span><span id="otn" style="flex:none;width:72px;text-align:center"></span><span id="ote" style="flex:none;width:72px;text-align:center"></span></div>`;
  } else {
    for (let n = pg.p * 10; n < pg.p * 10 + 10; n++) {
      const c = SA[n], s = st.S[n];
      b += `<div class="pr"><b>${n + 1}. ${e(c[0])}</b><span class="m" style="display:block;margin-bottom:6px">${e(c[1])}</span><div class="row" style="justify-content:flex-start;gap:6px"><span class="m">Nilai:</span>${[1, 2, 3, 4, 5].map(r => `<button class="s ra ${s.r === r ? "on" : ""}" data-sr="${n}" data-r="${r}" style="padding:8px 14px">${r}</button>`).join("")}<span class="m" style="margin-left:10px">Tanda:</span><button class="s mk pl ${s.m === "+" ? "on" : ""}" data-sm="${n}" data-mk="+" title="Kekuatan utama" style="padding:8px 12px">＋</button><button class="s mk mn ${s.m === "-" ? "on" : ""}" data-sm="${n}" data-mk="-" title="Perlu dikembangkan" style="padding:8px 12px">－</button></div></div>`;
    }
  }
  V.innerHTML = `<div class="c ases"><div class="row m"><span>Bagian ${pg.s + 1} dari 4 · ${e(SEC[pg.s])}</span><span class="tt" id="tm">${fmt(Math.max(left, 0))}</span><span>Halaman ${pos + 1} dari ${N}</span></div><div class="bar"><i style="width:${(pos + 1) / N * 100}%"></i></div><p class="m">${e(INTRO[pg.s])}</p>${b}<div class="row" style="margin-top:14px"><button class="s" id="pv" ${pos ? "" : "disabled"}>Sebelumnya</button><button id="nx" disabled>${pos < N - 1 ? "Berikutnya" : "Selesai"}</button></div></div>`;

  V.querySelectorAll("[data-m]").forEach(x => x.onclick = () => {
    const n = +x.dataset.m, v = +x.dataset.v;
    st.M[n] = v;
    V.querySelectorAll('[data-m="' + n + '"]').forEach(y => y.classList.toggle("on", +y.dataset.v === v));
    sync();
  });
  V.querySelectorAll("[data-l]").forEach(x => {
    x.oninput = () => { st.L[pg.p][+x.dataset.l] = clamp(x.value, 10); sync(); };
    x.onchange = () => { x.value = st.L[pg.p][+x.dataset.l] || ""; };
  });
  V.querySelectorAll("[data-o]").forEach(x => {
    x.oninput = () => { st.O[x.dataset.o][pg.p][+x.dataset.k] = clamp(x.value, 100); sync(); };
    x.onchange = () => { x.value = st.O[x.dataset.o][pg.p][+x.dataset.k] || ""; };
  });
  V.querySelectorAll("[data-sr]").forEach(x => x.onclick = () => {
    const n = +x.dataset.sr, r = +x.dataset.r;
    st.S[n].r = r;
    V.querySelectorAll('[data-sr="' + n + '"]').forEach(y => y.classList.toggle("on", +y.dataset.r === r));
    sync();
  });
  V.querySelectorAll("[data-sm]").forEach(x => x.onclick = () => {
    const n = +x.dataset.sm, k = x.dataset.mk;
    st.S[n].m = st.S[n].m === k ? "" : k;
    V.querySelectorAll('[data-sm="' + n + '"]').forEach(y => y.classList.toggle("on", y.dataset.mk === st.S[n].m));
  });
  document.getElementById("pv").onclick = () => { pos--; draw(); };
  document.getElementById("nx").onclick = () => { if (pos < N - 1) { pos++; draw(); } else finish(); };
  sync();
  window.scrollTo(0, 0);
}

function begin() {
  st = fresh();
  pos = 0;
  t0 = Date.now();
  left = T.menit * 60;
  clearInterval(tk);
  tk = setInterval(tick, 1000);
  draw();
}

function rec() {
  const s = st || fresh();
  return { M: s.M.slice(), L: s.L.map(a => a.slice()), O: { n: s.O.n.map(a => a.slice()), e: s.O.e.map(a => a.slice()) }, S: s.S.map(x => ({ r: x.r, m: x.m })) };
}

window.ASES = { begin, rec, body, sum, ring, csv, inc, concl, _t: { mbti, li, liTop, och, sa, DIM, OT, SA } };
})();
