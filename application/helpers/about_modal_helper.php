<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * About Modal Helper
 * 
 * Provides reusable functions for generating about modal buttons
 * and retrieving default content for common modules.
 */

/**
 * Generate About Modal Button
 * 
 * @param string $moduleId Unique module identifier (e.g., 'kinerja_bas', 'monitoring_kua')
 * @param array $content Modal content (title, description, features, usage)
 * @param string $btnClass Additional button classes
 * @return string HTML button element
 */
function about_modal_button($moduleId, $content = [], $btnClass = '')
{
    // Store content in data attribute (JSON encoded)
    $contentJson = htmlspecialchars(json_encode($content), ENT_QUOTES, 'UTF-8');
    
    return '<button type="button" class="btn btn-sm rounded-circle ms-2 about-modal-btn text-white ' . $btnClass . '" 
            data-module-id="' . $moduleId . '" 
            data-content="' . $contentJson . '"
            title="Tentang Modul">
            <i class="fa fa-info-circle"></i>
        </button>';
}

/**
 * Get default content for common modules
 * 
 * @param string $moduleType Module type identifier
 * @return array|null Module content or null if not found
 */
function get_default_about_content($moduleType)
{
    $defaults = [
        'kinerja_bas' => [
            'title' => 'Tentang Modul Kinerja BAS',
            'description' => 'Modul ini menampilkan statistik dan rekapitulasi kinerja Berita Acara Sidang (BAS) panitera pengganti secara periodik.',
            'features' => [
                'Tampilan data kinerja BAS panitera pengganti',
                'Filter berdasarkan rentang tanggal',
                'Data perbandingan bulan berjalan, bulan sebelumnya, dan tahun berjalan',
                'Statistik jumlah sidang, BAS sudah diunggah, BAS belum diunggah, dan skor persentase',
                'Data dapat diklik untuk melihat detail BAS',
                'Visualisasi grafik untuk setiap periode'
            ],
            'usage' => [
                'Gunakan filter rentang tanggal untuk memilih periode yang ingin ditampilkan',
                'Klik pada angka dalam tabel untuk melihat detail BAS sesuai kategori',
                'Gunakan scroll horizontal untuk melihat kolom-kolom yang tidak muat di layar',
                'Grafik dapat diubah jenisnya (bar, pie, doughnut) untuk visualisasi yang berbeda',
                'Skor persentase ditampilkan dengan warna berbeda berdasarkan capaian'
            ],
            'scoreColors' => [
                ['label' => 'Hijau', 'range' => '100%', 'meaning' => 'sangat baik'],
                ['label' => 'Biru', 'range' => '90-99%', 'meaning' => 'baik'],
                ['label' => 'Cyan', 'range' => '75-89%', 'meaning' => 'cukup baik'],
                ['label' => 'Kuning', 'range' => '50-74%', 'meaning' => 'perlu perbaikan'],
                ['label' => 'Merah', 'range' => '< 50%', 'meaning' => 'kurang']
            ]
        ],
        'kinerja_minutasi' => [
            'title' => 'Tentang Modul Kinerja Setor Panmud',
            'description' => 'Modul ini menampilkan statistik dan rekapitulasi Kinerja Setor Panmud (penyetoran berkas perkara yang telah diputus) panitera pengganti secara periodik.',
            'features' => [
                'Tampilan data Kinerja Setor Panmud panitera pengganti',
                'Filter berdasarkan rentang tanggal',
                'Data perbandingan bulan berjalan, bulan sebelumnya, dan tahun berjalan',
                'Statistik jumlah perkara putus, minutasi sudah setor, minutasi pending, dan skor persentase',
                'Data dapat diklik untuk melihat detail minutasi',
                'Visualisasi grafik untuk setiap periode'
            ],
            'usage' => [
                'Gunakan filter rentang tanggal untuk memilih periode yang ingin ditampilkan',
                'Klik pada angka dalam tabel untuk melihat detail minutasi sesuai kategori',
                'Gunakan scroll horizontal untuk melihat kolom-kolom yang tidak muat di layar',
                'Grafik dapat diubah jenisnya (bar, pie, doughnut) untuk visualisasi yang berbeda',
                'Skor persentase ditampilkan dengan warna berbeda berdasarkan capaian'
            ],
            'scoreColors' => [
                ['label' => 'Hijau', 'range' => '100%', 'meaning' => 'sangat baik'],
                ['label' => 'Biru', 'range' => '90-99%', 'meaning' => 'baik'],
                ['label' => 'Cyan', 'range' => '75-89%', 'meaning' => 'cukup baik'],
                ['label' => 'Kuning', 'range' => '50-74%', 'meaning' => 'perlu perbaikan'],
                ['label' => 'Merah', 'range' => '< 50%', 'meaning' => 'kurang']
            ]
        ],
        'kinerja_relaas' => [
            'title' => 'Tentang Modul Kinerja Relaas',
            'description' => 'Modul ini menampilkan statistik dan rekapitulasi kinerja Relaas per kecamatan secara periodik.',
            'features' => [
                'Tampilan data kinerja relaas per kecamatan',
                'Filter berdasarkan rentang tanggal',
                'Data perbandingan bulan berjalan, bulan sebelumnya, dan tahun berjalan',
                'Statistik jumlah relaas, input/unggah, sisa, dan skor persentase',
                'Data dapat diklik untuk melihat detail relaas',
                'Visualisasi grafik untuk setiap periode'
            ],
            'usage' => [
                'Gunakan filter rentang tanggal untuk memilih periode yang ingin ditampilkan',
                'Klik pada angka dalam tabel untuk melihat detail relaas sesuai kecamatan',
                'Gunakan scroll horizontal untuk melihat kolom-kolom yang tidak muat di layar',
                'Grafik dapat diubah jenisnya (bar, pie, doughnut) untuk visualisasi yang berbeda',
                'Skor persentase ditampilkan dengan warna berbeda berdasarkan capaian'
            ],
            'scoreColors' => [
                ['label' => 'Hijau', 'range' => '100%', 'meaning' => 'sangat baik'],
                ['label' => 'Biru', 'range' => '90-99%', 'meaning' => 'baik'],
                ['label' => 'Cyan', 'range' => '75-89%', 'meaning' => 'cukup baik'],
                ['label' => 'Kuning', 'range' => '50-74%', 'meaning' => 'perlu perbaikan'],
                ['label' => 'Merah', 'range' => '< 50%', 'meaning' => 'kurang']
            ]
        ],
        'kinerja_hakim' => [
            'title' => 'Tentang Modul Keadaan Perkara',
            'description' => 'Modul ini menampilkan statistik dan rekapitulasi keadaan perkara di pengadilan secara periodik (bulanan atau tahunan).',
            'features' => [
                'Tampilan data keadaan perkara secara bulanan atau tahunan',
                'Filter berdasarkan tahun',
                'Filter berdasarkan status e-Court',
                'Filter berdasarkan alur perkara',
                'Filter berdasarkan jenis perkara',
                'Data dapat diklik untuk melihat detail perkara',
                'Statistik persentase penggunaan e-Court'
            ],
            'usage' => [
                'Gunakan filter tahun untuk memilih periode yang ingin ditampilkan',
                'Gunakan filter status e-Court untuk memfilter berdasarkan penggunaan sistem e-Court',
                'Gunakan filter alur perkara untuk memfilter berdasarkan jalannya proses perkara',
                'Gunakan filter jenis perkara untuk memfilter berdasarkan kategori jenis perkara',
                'Klik pada angka dalam tabel untuk melihat daftar perkara yang sesuai dengan data tersebut',
                'Gunakan scroll horizontal untuk melihat kolom-kolom yang tidak muat di layar'
            ]
        ],
        'kinerja_durasiputus' => [
            'title' => 'Tentang Modul Durasi Putus',
            'description' => 'Modul ini menampilkan statistik durasi putusan perkara berdasarkan hakim.',
            'features' => [
                'Tampilan data durasi putusan per hakim',
                'Filter berdasarkan rentang tanggal',
                'Kategorisasi: < 3 bulan, 3-5 bulan, > 5 bulan',
                'Statistik jumlah putusan per kategori',
                'Data dapat diklik untuk melihat detail perkara',
                'Visualisasi grafik untuk setiap kategori'
            ],
            'usage' => [
                'Gunakan filter rentang tanggal untuk memilih periode yang ingin ditampilkan',
                'Klik pada angka dalam tabel untuk melihat detail perkara sesuai durasi',
                'Gunakan scroll horizontal untuk melihat kolom-kolom yang tidak muat di layar',
                'Grafik dapat diubah jenisnya (bar, pie, doughnut) untuk visualisasi yang berbeda'
            ]
        ],
        'kinerja_perlindungan' => [
            'title' => 'Tentang Modul Perlindungan',
            'description' => 'Modul ini menampilkan statistik perlindungan dalam perkara cerai berdasarkan hakim.',
            'features' => [
                'Tampilan data perlindungan per hakim',
                'Filter berdasarkan rentang tanggal',
                'Statistik talak kabul, talak perlindungan, gugat kabul',
                'Persentase perlindungan',
                'Data dapat diklik untuk melihat detail perkara',
                'Visualisasi grafik untuk setiap kategori'
            ],
            'usage' => [
                'Gunakan filter rentang tanggal untuk memilih periode yang ingin ditampilkan',
                'Klik pada angka dalam tabel untuk melihat detail perkara sesuai kategori',
                'Gunakan scroll horizontal untuk melihat kolom-kolom yang tidak muat di layar',
                'Grafik dapat diubah jenisnya (bar, pie, doughnut) untuk visualisasi yang berbeda'
            ]
        ],
        'monitoring_jenisperkara' => [
            'title' => 'Tentang Modul Monitoring Jenis Perkara',
            'description' => 'Modul ini menampilkan statistik dan rekapitulasi keadaan perkara berdasarkan jenis perkara secara periodik (rentang tanggal).',
            'features' => [
                'Tampilan data keadaan perkara berdasarkan jenis perkara',
                'Filter berdasarkan rentang tanggal',
                'Statistik lengkap: sisa sebelumnya, diterima, jumlah, dicabut, putus, dan minutasi',
                'Breakdown putusan: dikabulkan, ditolak, tidak diterima, digugurkan, dicoret, perdamaian',
                'Persentase penggunaan e-Court',
                'Data dapat diklik untuk melihat detail perkara'
            ],
            'usage' => [
                'Gunakan filter rentang tanggal untuk memilih periode yang ingin ditampilkan',
                'Klik pada angka dalam tabel untuk melihat daftar perkara sesuai kategori',
                'Gunakan scroll horizontal untuk melihat kolom-kolom yang tidak muat di layar',
                'Total footer dapat diklik untuk melihat total keseluruhan perkara'
            ]
        ],
        'monitoring_jenisperkarabulanan' => [
            'title' => 'Tentang Modul Jenis Perkara Bulanan',
            'description' => 'Modul ini menampilkan statistik jenis perkara secara bulanan.',
            'features' => [
                'Tampilan data jenis perkara per bulan',
                'Filter berdasarkan tahun',
                'Breakdown lengkap semua jenis perkara',
                'Data dapat diklik untuk melihat detail perkara',
                'Visualisasi grafik untuk setiap bulan'
            ],
            'usage' => [
                'Gunakan filter tahun untuk memilih periode yang ingin ditampilkan',
                'Klik pada angka dalam tabel untuk melihat daftar perkara sesuai jenis',
                'Gunakan scroll horizontal untuk melihat kolom-kolom yang tidak muat di layar'
            ]
        ],
        'monitoring_kua' => [
            'title' => 'Tentang Modul Jumlah Perkara KUA',
            'description' => 'Modul ini menampilkan statistik perkara berdasarkan Kantor Urusan Agama (KUA) tempat pernikahan, termasuk jumlah dan persentase perkara.',
            'features' => [
                'KUA: Nama Kantor Urusan Agama tempat pernikahan',
                'Kode KUA: Kode identifikasi KUA',
                'Jumlah: Jumlah perkara yang tercatat untuk KUA tersebut',
                'Persentase (%): Persentase dari total perkara yang terjadi'
            ],
            'usage' => [
                'Gunakan filter rentang tanggal untuk memilih periode yang ingin ditampilkan',
                'Klik pada angka jumlah untuk melihat daftar perkara sesuai kriteria',
                'Gunakan scroll horizontal untuk melihat kolom-kolom yang tidak muat di layar'
            ]
        ],
        'monitoring_kecamatan' => [
            'title' => 'Tentang Modul Monitoring Kecamatan',
            'description' => 'Modul ini menampilkan statistik perkara berdasarkan Kecamatan.',
            'features' => [
                'Tampilan data perkara per kecamatan',
                'Filter berdasarkan rentang tanggal',
                'Statistik jumlah perkara dan persentase',
                'Data dapat diklik untuk melihat detail perkara'
            ],
            'usage' => [
                'Gunakan filter rentang tanggal untuk memilih periode yang ingin ditampilkan',
                'Klik pada angka dalam tabel untuk melihat daftar perkara sesuai kecamatan',
                'Gunakan scroll horizontal untuk melihat kolom-kolom yang tidak muat di layar'
            ]
        ],
        'monitoring_putusan' => [
            'title' => 'Tentang Modul Monitoring Putusan',
            'description' => 'Modul ini menampilkan statistik putusan perkara.',
            'features' => [
                'Tampilan data putusan perkara',
                'Filter berdasarkan rentang tanggal',
                'Statistik jumlah putusan per kategori',
                'Data dapat diklik untuk melihat detail perkara'
            ],
            'usage' => [
                'Gunakan filter rentang tanggal untuk memilih periode yang ingin ditampilkan',
                'Klik pada angka dalam tabel untuk melihat daftar perkara sesuai kategori',
                'Gunakan scroll horizontal untuk melihat kolom-kolom yang tidak muat di layar'
            ]
        ],
        'monitoring_perkara' => [
            'title' => 'Tentang Modul Data Perkara',
            'description' => 'Modul ini menampilkan data perkara.',
            'features' => [
                'Tampilan data perkara',
                'Filter berdasarkan rentang tanggal',
                'Statistik jumlah perkara per kategori',
                'Data dapat diklik untuk melihat detail pihak'
            ],
            'usage' => [
                'Gunakan filter rentang tanggal untuk memilih periode yang ingin ditampilkan',
                'Klik pada angka dalam tabel untuk melihat daftar perkara sesuai kategori',
                'Gunakan scroll horizontal untuk melihat kolom-kolom yang tidak muat di layar'
            ]
        ],
        'cerai_kecamatan' => [
            'title' => 'Tentang Modul Statistik Cerai per Kecamatan',
            'description' => 'Modul ini menampilkan statistik perkara cerai berdasarkan Kecamatan tempat tinggal pihak yang berperkara, termasuk jumlah perkara, jenis cerai (gugat/talak), dan persentase.',
            'features' => [
                'Kecamatan: Nama Kecamatan tempat tinggal pihak',
                'Jumlah Perkara: Total perkara cerai yang tercatat untuk Kecamatan tersebut',
                'Cerai Gugat: Jumlah perkara cerai yang diajukan oleh istri (jenis_perkara_id = 347)',
                'Cerai Talak: Jumlah perkara cerai yang diajukan oleh suami (jenis_perkara_id = 346)',
                'Persentase (%): Persentase dari total perkara cerai di Kabupaten Sidoarjo'
            ],
            'usage' => [
                'Gunakan filter rentang tanggal untuk memilih periode yang ingin ditampilkan',
                'Klik pada angka jumlah untuk melihat daftar perkara sesuai kriteria',
                'Gunakan scroll horizontal untuk melihat kolom-kolom yang tidak muat di layar'
            ],
            'notes' => [
                'Data hanya mencakup perkara dengan akta cerai yang telah diterbitkan',
                'Data hanya mencakup pihak dengan propinsi 35 (Jawa Timur) dan kabupaten 35.15 (Sidoarjo)',
                'Untuk Cerai Talak (346), kecamatan mengacu pada Tergugat (pihak2)',
                'Untuk Cerai Gugat (347), kecamatan mengacu pada Pemohon (pihak1)'
            ]
        ],
		'whatsapp_log' => [
            'title' => 'Tentang Modul WhatsApp Log',
            'description' => 'Modul ini menampilkan log pengiriman notifikasi WhatsApp.',
            'features' => [
                'Tampilan log pengiriman WhatsApp',
                'Filter berdasarkan rentang tanggal',
                'Statistik jumlah notifikasi terkirim',
                'Detail status pengiriman'
            ],
            'usage' => [
                'Gunakan filter rentang tanggal untuk memilih periode yang ingin ditampilkan',
                'Gunakan scroll horizontal untuk melihat kolom-kolom yang tidak muat di layar'
            ]
        ],
		'notifikasi' => [
            'title' => 'Tentang Modul Notifikasi',
            'description' => 'Modul ini menampilkan berbagai notifikasi terkait perkara di pengadilan.',
            'features' => [
                'Notifikasi Antrian Sidang',
                'Notifikasi Jadwal Sidang',
                'Notifikasi Kalender',
                'Notifikasi Jurnal',
                'Notifikasi Akta Cerai',
                'Filter berdasarkan tipe notifikasi',
                'Data dapat diklik untuk melihat detail'
            ],
            'usage' => [
                'Gunakan dropdown untuk memilih tipe notifikasi yang ingin ditampilkan',
                'Klik pada data untuk melihat detail perkara',
                'Gunakan pagination untuk navigasi data',
                'Notifikasi disposition untuk disposisi surat'
            ],
            'notes' => [
                'Data notifikasi diambil dari sistem SIPP',
                'Terdapat menu untuk menandai notifikasi sudah dibaca',
                'Terdapat menu untuk menandai semua notifikasi sudah dibaca'
            ]
        ],
		'pegawai' => [
            'title' => 'Tentang Modul Pegawai',
            'description' => 'Modul pengelolaan data Pegawai di pengadilan.',
            'features' => [
                'Daftar seluruh pegawai',
                'Data profil pegawai',
                'Riwayat kinerja',
                'Filter dan pencarian',
                'Detail informasi jabatan'
            ],
            'usage' => [
                'Gunakan fitur pencarian untuk mencari pegawai',
                'Klik pada baris untuk melihat detail profil',
                'Lihat riwayat kinerja masing-masing pegawai',
                'Informasi jabatan dan unit kerja'
            ],
            'notes' => [
                'Data diambil dari database internal',
                'Hanya dapat diakses oleh pengguna yang berhak'
            ]
        ],
        'statistik' => [
            'title' => 'Tentang Modul Statistik',
            'description' => 'Modul menampilkan statistik jabatan dan golongan ruang Pegawai di pengadilan.',
            'features' => [
                'Statistik berdasarkan jabatan',
                'Statistik berdasarkan golongan ruang',
                'Statistik berdasarkan jenis kelamin',
                'Statistik pendidikan',
                'Grafik visualisasi data',
                'Tampilan publik dan privat'
            ],
            'usage' => [
                'Pilih menu Statistik untuk melihat data',
                'Gunakan filter untuk melihat data spesifik',
                'Grafik dapat diubah jenisnya untuk visualisasi berbeda',
                'Tampilan publik dapat diakses tanpa login'
            ],
            'notes' => [
                'Data diambil dari database internal',
                'Statistik diperbarui secara real-time'
            ]
        ],
        'plt' => [
            'title' => 'Tentang Modul Pelaksana Tugas (PLT)',
            'description' => 'Modul pengelolaan pejabat sementara yang melaksanakan tugas kedinasan di pengadilan.',
            'features' => [
                'Pengelolaan PLT Ketua',
                'Pengelolaan PLT Wakil Ketua',
                'Pengelolaan PLT Panitera',
                'Pengelolaan PLT Sekretaris',
                'Pengelolaan PLT Kasubag Kepegawaian',
                'Form pemilihan pejabat PLT',
                'Fitur hapus pejabat PLT'
            ],
            'usage' => [
                'Pilih nama pegawai dari dropdown untuk mengangkat sebagai pejabat PLT',
                'Klik tombol hapus untuk mencabut jabatan PLT',
                'Data PLT akan ditampilkan dengan informasi jabatan dan grup'
            ],
            'notes' => [
                'Hanya dapat diakses oleh admin dan bagian kepegawaian',
                'PLT dapat ditambahkan untuk posisi kosong'
            ]
        ],
        'struktur' => [
            'title' => 'Tentang Modul Struktur Organisasi',
            'description' => 'Modul menampilkan struktur organisasi pengadilan dalam bentuk diagram hierarkis.',
            'features' => [
                'Visualisasi struktur organisasi',
                'Tampilan hierarkis pejabat struktural',
                'Informasi staff pada setiap unit',
                'Detail profil pegawai',
                'Link ke profil masing-masing pejabat',
                'Tampilan publik dan privat',
                'Export ke PDF'
            ],
            'usage' => [
                'Navigasi melalui hierarki struktur organisasi',
                'Klik pada nama pejabat untuk melihat profil',
                'Gunakan tombol PDF untuk mengekspor struktur',
                'Tampilan publik dapat diakses tanpa login'
            ],
            'notes' => [
                'Data diambil dari database internal',
                'Struktur organisasi dapat dikonfigurasi melalui modul referensi'
            ]
        ],
        'blangko' => [
            'title' => 'Tentang Modul Blangko',
            'description' => 'Modul sistem manajemen blangko dokumen resmi pengadilan.',
            'features' => [
                'Manajemen file blangko',
                'Upload dan download blangko',
                'Organisasi folder blangko',
                'Integrasi dengan elFinder',
                'Akses ke direktori blangko'
            ],
            'usage' => [
                'Gunakan file manager untuk mengelola blangko',
                'Upload file blangko baru melalui interface',
                'Download blangko yang tersedia',
                'Organisasi file dalam folder'
            ],
            'notes' => [
                'File disimpan di direktori terpisah',
                'Hanya dapat diakses oleh pengguna yang berhak'
            ]
        ],
        'ocr' => [
            'title' => 'Tentang Modul OCR',
            'description' => 'Modul konversi file PDF/Word ke format Word dengan teknologi OCR (Optical Character Recognition).',
            'features' => [
                'Konversi PDF ke Word',
                'Konversi Word ke Word',
                'Dukungan OCR untuk teks gambar',
                'Pengaturan DPI',
                'Pilihan bahasa OCR',
                'Deteksi tabel',
                'Pertahankan tata letak',
                'Peningkatan kualitas gambar',
                'Proses batch upload',
                'Proses batch file'
            ],
            'usage' => [
                'Upload file PDF atau Word',
                'Pilih pengaturan OCR (DPI, bahasa, dll)',
                'Klik tombol Proses untuk memulai konversi',
                'Download hasil konversi setelah selesai',
                'Dapat memproses多个 file sekaligus'
            ],
            'notes' => [
                'File output berformat .docx',
                'Proses dilakukan menggunakan backend Python',
                'File sementara akan dihapus setelah konversi'
            ]
        ],
        'rtf' => [
            'title' => 'Tentang Modul Pembersih RTF & DOC',
            'description' => 'Modul pembersihan file RTF dan DOC dari kode-kode yang tidak diperlukan.',
            'features' => [
                'Pembersihan file RTF',
                'Pembersihan file DOC',
                'Konversi DOC ke RTF',
                'Penghapusan kode-kode tidak diperlukan',
                'Proses batch upload',
                'Proses batch file',
                'Deteksi otomatis tipe file'
            ],
            'usage' => [
                'Upload file RTF, DOC, atau DOCX',
                'Klik tombol Proses untuk memulai pembersihan',
                'Download hasil pembersihan setelah selesai',
                'Dapat memproses多个 file sekaligus'
            ],
            'notes' => [
                'File output berformat .rtf',
                'Proses dilakukan menggunakan backend Python',
                'File sementara akan dihapus setelah proses selesai'
            ]
        ],
        'rtsp' => [
            'title' => 'Tentang Modul Monitor RTSP',
            'description' => 'Modul monitoring streaming RTSP dari kamera CCTV yang terhubung.',
            'features' => [
                'Streaming video RTSP',
                'Monitoring kamera CCTV',
                'Tambah/edit/hapus stream',
                'Informasi lokasi kamera',
                'Integrasi dengan go2rtc server',
                'Tampilan multi-kamera',
                'Notifikasi WhatsApp'
            ],
            'usage' => [
                'Pilih stream dari daftar untuk memulai monitoring',
                'Klik tombol Tambah untuk menambah stream baru',
                'Masukkan nama, URL RTSP, dan lokasi kamera',
                'Klik tombol Edit untuk mengubah data stream',
                'Klik tombol Hapus untuk menghapus stream'
            ],
            'notes' => [
                'Memerlukan koneksi ke server go2rtc',
                'URL RTSP harus valid dan dapat diakses',
                'Hanya dapat diakses oleh pengguna yang berhak'
            ]
        ],
        'surat_masuk' => [
            'title' => 'Tentang Modul Surat Masuk',
            'description' => 'Modul pengelolaan surat masuk.',
            'features' => [
                'Input dan edit surat masuk',
                'Upload file surat',
                'Pencarian surat',
                'Export data surat'
            ],
            'usage' => [
                'Klik tombol Tambah Data untuk menambah surat baru',
                'Klik tombol Edit untuk mengubah data surat',
                'Klik tombol Hapus untuk menghapus surat',
                'Gunakan fitur pencarian untuk mencari surat'
            ]
        ],
        'surat_keluar' => [
            'title' => 'Tentang Modul Surat Keluar',
            'description' => 'Modul pengelolaan surat keluar.',
            'features' => [
                'Input dan edit surat keluar',
                'Upload file surat',
                'Pencarian surat',
                'Export data surat'
            ],
            'usage' => [
                'Klik tombol Tambah Data untuk menambah surat baru',
                'Klik tombol Edit untuk mengubah data surat',
                'Klik tombol Hapus untuk menghapus surat',
                'Gunakan fitur pencarian untuk mencari surat'
            ]
        ],
        'sk' => [
            'title' => 'Tentang Modul Manaejemen SK',
            'description' => 'Modul pengelolaan Surat Keputusan (SK).',
            'features' => [
                'Input dan edit SK',
                'Upload file SK',
                'Pencarian SK',
                'Export data SK'
            ],
            'usage' => [
                'Klik tombol Tambah Data untuk menambah SK baru',
                'Klik tombol Edit untuk mengubah data SK',
                'Klik tombol Hapus untuk menghapus SK',
                'Gunakan fitur pencarian untuk mencari SK'
            ]
        ],
        'document_sop' => [
            'title' => 'Tentang Modul SOP',
            'description' => 'Modul pengelolaan dokumen SOP (Standar Operasional Prosedur) untuk internal dan akses publik.',
            'features' => [
                'Input dan edit dokumen SOP',
                'Upload file SOP dalam format PDF',
                'Pencarian SOP berdasarkan judul atau nomor',
                'Kategorisasi SOP berdasarkan jenis dan unit kerja',
                'Export data SOP',
                'Akses publik untuk viewing dan download SOP'
            ],
            'usage' => [
                'Klik tombol Tambah Data untuk menambah SOP baru',
                'Klik tombol Edit untuk mengubah data SOP',
                'Klik tombol Hapus untuk menghapus SOP',
                'Gunakan fitur pencarian untuk mencari SOP berdasarkan judul atau nomor',
                'Klik tombol Export untuk mengunduh data SOP dalam format Excel',
                'Akses publik dapat melihat dan mengunduh SOP tanpa login'
            ],
            'notes' => [
                'Dokumen SOP harus diunggah dalam format PDF',
                'SOP yang dipublikasikan dapat diakses oleh masyarakat umum',
                'Pastikan SOP telah melalui proses verifikasi sebelum dipublikasikan'
            ]
        ],
        'lhk_lhkpn' => [
            'title' => 'Tentang Modul LHKPN',
            'description' => 'Modul pengelolaan Laporan Harta Kekayaan Penyelenggara Negara (LHKPN) untuk pegawai.',
            'features' => [
                'Input dan edit data LHKPN',
                'Upload file bukti lapor LHKPN',
                'Pencarian LHKPN berdasarkan nama atau NIP',
                'Filter berdasarkan tahun dan status',
                'Export data LHKPN',
                'Notifikasi untuk pegawai yang belum lapor LHKPN'
            ],
            'usage' => [
                'Klik tombol Tambah Data untuk menambah LHKPN baru',
                'Klik tombol Edit untuk mengubah data LHKPN',
                'Klik tombol Hapus untuk menghapus LHKPN',
                'Gunakan fitur pencarian untuk mencari LHKPN berdasarkan nama atau NIP',
                'Gunakan filter tahun untuk menampilkan LHKPN sesuai tahun yang diinginkan',
                'Klik tombol Export untuk mengunduh data LHKPN dalam format Excel'
            ],
            'notes' => [
                'LHKPN wajib dilaporkan oleh setiap Penyelenggara Negara',
                'Bukti lapor LHKPN harus diunggah dalam format PDF',
                'Pegawai akan mendapat notifikasi jika belum melaporkan LHKPN'
            ]
        ],
        'lhk_lhkasn' => [
            'title' => 'Tentang Modul SPT',
            'description' => 'Modul pengelolaan Laporan Pajak Tahunan (SPT) Aparatur Sipil Negara.',
            'features' => [
                'Input dan edit data SPT Tahunan',
                'Upload file bukti lapor SPT',
                'Pencarian SPT berdasarkan nama atau NIP',
                'Filter berdasarkan tahun dan status',
                'Export data SPT',
                'Notifikasi untuk pegawai yang belum lapor SPT'
            ],
            'usage' => [
                'Klik tombol Tambah Data untuk menambah SPT baru',
                'Klik tombol Edit untuk mengubah data SPT',
                'Klik tombol Hapus untuk menghapus SPT',
                'Gunakan fitur pencarian untuk mencari SPT berdasarkan nama atau NIP',
                'Gunakan filter tahun untuk menampilkan SPT sesuai tahun yang diinginkan',
                'Klik tombol Export untuk mengunduh data SPT dalam format Excel'
            ],
            'notes' => [
                'SPT Tahunan wajib dilaporkan oleh setiap ASN',
                'Bukti lapor SPT harus diunggah dalam format PDF',
                'Pegawai akan mendapat notifikasi jika belum melaporkan SPT Tahunan'
            ]
        ],
        'jabatan' => [
            'title' => 'Tentang Modul Jabatan',
            'description' => 'Modul pengelolaan data jabatan.',
            'features' => [
                'Input dan edit jabatan',
                'Pencarian jabatan',
                'Export data jabatan'
            ],
            'usage' => [
                'Klik tombol Tambah Data untuk menambah jabatan baru',
                'Klik tombol Edit untuk mengubah data jabatan',
                'Klik tombol Hapus untuk menghapus jabatan',
                'Gunakan fitur pencarian untuk mencari jabatan'
            ]
        ],
        'settings_menu' => [
            'title' => 'Tentang Modul Settings Menu',
            'description' => 'Modul pengelolaan pengaturan menu aplikasi.',
            'features' => [
                'Input dan edit menu',
                'Pengaturan urutan menu',
                'Pengaturan visibilitas menu',
                'Pengaturan ikon menu'
            ],
            'usage' => [
                'Klik tombol Tambah Data untuk menambah menu baru',
                'Klik tombol Edit untuk mengubah data menu',
                'Klik tombol Hapus untuk menghapus menu',
                'Drag and drop untuk mengubah urutan menu'
            ]
        ],
        'siadpa_register' => [
            'title' => 'Tentang Modul SIADPA Register',
            'description' => 'Modul ini menampilkan data register dari SIADPA.',
            'features' => [
                'Tampilan data register SIADPA',
                'Filter berdasarkan rentang tanggal',
                'Pencarian data',
                'Export data'
            ],
            'usage' => [
                'Gunakan filter rentang tanggal untuk memilih periode yang ingin ditampilkan',
                'Gunakan fitur pencarian untuk mencari data',
                'Klik pada nomor perkara untuk melihat detail'
            ]
        ],
        'siadpa_ac' => [
            'title' => 'Tentang Modul SIADPA AC',
            'description' => 'Modul ini menampilkan data Akta Cerai dari SIADPA.',
            'features' => [
                'Tampilan data Akta Cerai SIADPA',
                'Filter berdasarkan rentang tanggal',
                'Pencarian data',
                'Export data'
            ],
            'usage' => [
                'Gunakan filter rentang tanggal untuk memilih periode yang ingin ditampilkan',
                'Gunakan fitur pencarian untuk mencari data',
                'Klik pada nomor perkara untuk melihat detail'
            ]
        ],
        'rekapitulasi_keadaan_perkara' => [
            'title' => 'Tentang Modul Rekapitulasi Keadaan Perkara',
            'description' => 'Modul ini menampilkan rekapitulasi keadaan perkara.',
            'features' => [
                'Tampilan rekapitulasi keadaan perkara',
                'Filter berdasarkan rentang tanggal',
                'Breakdown lengkap keadaan perkara',
                'Data dapat diklik untuk melihat detail perkara'
            ],
            'usage' => [
                'Gunakan filter rentang tanggal untuk memilih periode yang ingin ditampilkan',
                'Klik pada angka dalam tabel untuk melihat daftar perkara sesuai kategori',
                'Gunakan scroll horizontal untuk melihat kolom-kolom yang tidak muat di layar'
            ]
        ],
        'audio' => [
            'title' => 'Tentang Modul Audio',
            'description' => 'Modul pengelolaan data audio pengumuman.',
            'features' => [
                'Input dan edit audio',
                'Upload file audio',
                'Pencarian audio',
                'Export data audio'
            ],
            'usage' => [
                'Klik tombol Tambah Data untuk menambah audio baru',
                'Klik tombol Edit untuk mengubah data audio',
                'Klik tombol Hapus untuk menghapus audio',
                'Gunakan fitur pencarian untuk mencari audio'
            ]
        ],
        'kartu' => [
            'title' => 'Tentang Modul Cetak Kartu',
            'description' => 'Modul pencetakan kartu jadwal sidang untuk keperluan administrasi pengadilan.',
            'features' => [
                'Pencarian perkara berdasarkan nomor perkara',
                'Cetak kartu jadwal sidang',
                'Preview kartu sebelum dicetak',
                'Format kartu sesuai standar pengadilan'
            ],
            'usage' => [
                'Masukkan nomor perkara pada kolom pencarian',
                'Klik tombol Cari untuk menampilkan data perkara',
                'Periksa data perkara yang ditampilkan',
                'Klik tombol Cetak untuk mencetak kartu jadwal sidang',
                'Kartu akan dicetak dalam format yang sudah ditentukan'
            ],
            'notes' => [
                'Nomor perkara harus sesuai dengan format yang berlaku',
                'Pastikan printer terhubung dan siap untuk mencetak',
                'Kartu jadwal sidang digunakan untuk informasi jadwal persidangan'
            ]
        ],
        'jadwal' => [
            'title' => 'Tentang Modul Jadwal Sidang',
            'description' => 'Modul pengelolaan dan monitoring jadwal sidang perkara di pengadilan.',
            'features' => [
                'Penjadwalan sidang perkara',
                'Penetapan majelis hakim',
                'Penetapan ruang sidang',
                'Monitoring jadwal sidang harian',
                'Notifikasi jadwal sidang',
                'Cetak jadwal sidang'
            ],
            'usage' => [
                'Pilih tanggal pada kalender untuk melihat jadwal',
                'Pilih ruang sidang untuk filter jadwal',
                'Klik tombol "Tambah Jadwal" untuk menambah jadwal baru',
                'Klik tombol "Edit" untuk mengubah jadwal sidang',
                'Klik tombol "Hapus" untuk menghapus jadwal',
                'Gunakan fitur cetak untuk mencetak jadwal sidang'
            ],
            'notes' => [
                'Jadwal sidang harus ditetapkan oleh Panitera Pengganti',
                'Perubahan jadwal akan dikirimkan sebagai notifikasi kepada para pihak',
                'Pastikan ruang sidang tidak bentrok dengan jadwal lain',
                'Majelis hakim dapat diubah sesuai kebutuhan'
            ]
        ],
        'kepegawaian_cuti' => [
            'title' => 'Tentang Modul Manajemen Cuti',
            'description' => 'Modul pengelolaan pengajuan, verifikasi, dan rekapitulasi cuti pegawai.',
            'features' => [
                'Pengajuan cuti',
                'Verifikasi cuti',
                'Rekapitulasi cuti',
                'Persetujuan atasan',
                'Riwayat cuti',
                'Cetak laporan cuti'
            ],
            'usage' => [
                'Pegawai mengajukan cuti melalui formulir pengajuan',
                'Atasan memverifikasi pengajuan cuti',
                'Lihat rekapitulasi cuti per periode',
                'Cetak laporan cuti untuk arsip'
            ],
            'notes' => [
                'Cuti dapat berupa cuti tahunan, cuti sakit, cuti melahirkan, dll',
                'Persetujuan cuti memerlukan verifikasi atasan langsung'
            ]
        ],
        'kepegawaian_presensi' => [
            'title' => 'Tentang Modul Presensi Pegawai',
            'description' => 'Modul statistik dan monitoring presensi kehadiran pegawai.',
            'features' => [
                'Rekam presensi',
                'Statistik kehadiran',
                'Monitoring harian',
                'Laporan presensi',
                'Notifikasi reminder'
            ],
            'usage' => [
                'Pegawai melakukan presensi masuk dan pulang',
                'Pimpin dapat memantau kehadiran pegawai',
                'Lihat laporan presensi per periode',
                'Kirim reminder presensi via WhatsApp'
            ],
            'notes' => [
                'Presensi dilakukan dengan Scan Fingerprint atau manual',
                'Data presensi terintegrasi dengan sistem kepegawaian'
            ]
        ],
        'settings_gamification' => [
            'title' => 'Tentang Modul Gamifikasi',
            'description' => 'Modul sistem gamifikasi untuk meningkatkan partisipasi dan engagement pengguna.',
            'features' => [
                'Poin dan badge',
                'Leaderboard',
                'Tantangan dan misi',
                'Tracking progress',
                'Reward system'
            ],
            'usage' => [
                'Pengguna menyelesaikan misi untuk mendapatkan poin',
                'Poin dapat ditukar dengan reward',
                'Lihat ranking di leaderboard',
                'Ikuti tantangan yang tersedia'
            ],
            'notes' => [
                'Gamifikasi meningkatkan motivasi pengguna',
                'Reward dapat berupa sertifikat atau积分'
            ]
        ],
        'settings_config' => [
            'title' => 'Tentang Modul Konfigurasi Aplikasi',
            'description' => 'Modul pengelolaan konfigurasi aplikasi dan pengaturan sistem.',
            'features' => [
                'Pengaturan umum',
                'Konfigurasi email',
                'Pengaturan WhatsApp',
                'Konfigurasi SMS',
                'Pengaturan sistem lainnya'
            ],
            'usage' => [
                'Ubah pengaturan aplikasi sesuai kebutuhan',
                'Konfigurasi layanan notifikasi',
                'Atur parameter sistem'
            ],
            'notes' => [
                'Perubahan konfigurasi memerlukan hak akses admin',
                'Beberapa pengaturan memerlukan restart aplikasi'
            ]
        ],
        'settings_group' => [
            'title' => 'Tentang Modul Manajemen Grup',
            'description' => 'Modul pengelolaan grup pengguna aplikasi.',
            'features' => [
                'Buat grup baru',
                'Edit grup',
                'Hapus grup',
                'Atur hak akses grup',
                'Anggota grup'
            ],
            'usage' => [
                'Buat grup pengguna baru',
                'Tambahkan anggota ke grup',
                'Atur permissions untuk grup',
                'Hapus atau nonaktifkan grup'
            ],
            'notes' => [
                'Setiap pengguna dapat menjadi anggota beberapa grup',
                'Hak akses ditentukan oleh permissions grup'
            ]
        ],
        'references_ptsp' => [
            'title' => 'Tentang Modul PTSP',
            'description' => 'Modul pengelolaan data lokasi PTSP dan jam kerja.',
            'features' => [
                'Manajemen lokasi PTSP',
                'Pengaturan jam kerja',
                'Informasi alamat',
                'Kontak PTSP'
            ],
            'usage' => [
                'Tambah lokasi PTSP baru',
                'Edit informasi PTSP',
                'Atur jam kerja masing-masing lokasi'
            ],
            'notes' => [
                'PTSP = Pelayanan Terpadu Satu Pintu',
                'Data PTSP ditampilkan di halaman publik'
            ]
        ],
        'references_holiday' => [
            'title' => 'Tentang Modul Hari Libur',
            'description' => 'Modul pengelolaan data hari libur nasional dan cuti bersama.',
            'features' => [
                'Daftar hari libur',
                'Cuti bersama',
                'Filter tahun',
                'Integrasi dengan kalender'
            ],
            'usage' => [
                'Tambah hari libur baru',
                'Edit atau hapus hari libur',
                'Lihat daftar libur per tahun'
            ],
            'notes' => [
                'Hari libur mempengaruhi penjadwalan sidang',
                'Cuti bersama ditambahkan secara nasional'
            ]
        ],
        'troubleshoot_sidangverzet' => [
            'title' => 'Tentang Modul Sidang Verzet',
            'description' => 'Modul data sidang verzet untuk perkara yang memerlukan peninjauan ulang.',
            'features' => [
                'Data sidang verzet',
                'Filter perkara',
                'Detail verzet',
                'Status proses'
            ],
            'usage' => [
                'Cari perkara verzet',
                'Lihat detail sidang verzet',
                'Follow up proses verzet'
            ],
            'notes' => [
                'Verzet = banding terhadap putusan',
                'Memerlukan proses pengadilan ulang'
            ]
        ],
        'troubleshoot_pendaftaranecourt' => [
            'title' => 'Tentang Modul Pendaftaran e-Court',
            'description' => 'Modul informasi pendaftaran e-Court untuk troubleshooting dan monitoring.',
            'features' => [
                'Data pendaftaran e-Court',
                'Status pendaftaran',
                'Filter dan pencarian',
                'Troubleshooting'
            ],
            'usage' => [
                'Cari pendaftaran e-Court',
                'Periksa status pendaftaran',
                'Lakukan troubleshooting jika ada masalah'
            ],
            'notes' => [
                'e-Court adalah sistem pendaftaran perkara online',
                'Terintegrasi dengan SIPP'
            ]
        ],
        'troubleshoot_pendaftaranecourtbanding' => [
            'title' => 'Tentang Modul Pendaftaran e-Court Banding',
            'description' => 'Modul informasi pendaftaran e-Court banding untuk troubleshooting.',
            'features' => [
                'Data banding e-Court',
                'Status banding',
                'Filter perkara',
                'Troubleshooting'
            ],
            'usage' => [
                'Cari pendaftaran banding e-Court',
                'Periksa status banding',
                'Lakukan troubleshooting'
            ],
            'notes' => [
                'Banding = upaya hukum terhadap putusan',
                'e-Court mempercepat proses banding'
            ]
        ],
        'troubleshoot_dokik' => [
            'title' => 'Tentang Modul Validasi Dokumen Ikrar',
            'description' => 'Modul validasi dokumen ikrar untuk memastikan kelengkapan dan keabsahan.',
            'features' => [
                'Validasi dokumen',
                'Cek kelengkapan',
                'Cek keabsahan',
                'Status validasi'
            ],
            'usage' => [
                'Upload dokumen ikrar',
                'Sistem memvalidasi otomatis',
                'Lihat hasil validasi'
            ],
            'notes' => [
                'Dokumen ikrar diperlukan dalam perkara talak',
                'Validasi memastikan keabsahan dokumen'
            ]
        ],
        'app_tosiba' => [
            'title' => 'Tentang Modul TOSIBA',
            'description' => 'Modul monitoring sinkronisasi dan backup data antar sistem pengadilan.',
            'features' => [
                'Monitoring sinkronisasi',
                'Status backup',
                'Log aktivitas',
                'Notifikasi error'
            ],
            'usage' => [
                'Pantau status sinkronisasi',
                'Lihat log backup',
                'Terima notifikasi jika ada error'
            ],
            'notes' => [
                'TOSIBA = Monitoring Sinkronisasi dan Backup',
                'Hanya dapat diakses dari jaringan lokal'
            ]
        ],
        'app_text' => [
            'title' => 'Tentang Modul Pembersih Teks',
            'description' => 'Modul pembersihan teks dari karakter khusus.',
            'features' => [
                'Hapus karakter khusus',
                'Konversi ke ASCII',
                'Preview hasil',
                'Copy hasil'
            ],
            'usage' => [
                'Masukkan teks yang akan dibersihkan',
                'Klik tombol proses',
                'Copy hasil'
            ],
            'notes' => [
                'Berguna untuk membersihkan teks dari dokumen',
                'Hasil dapat langsung dicopy'
            ]
        ],
        'app_slider' => [
            'title' => 'Tentang Modul Galeri',
            'description' => 'Modul galeri foto dan slider untuk halaman utama website.',
            'features' => [
                'Upload foto',
                'Kelola galeri',
                'Atur slider',
                'Tampilan publik'
            ],
            'usage' => [
                'Tambah foto ke galeri',
                'Atur urutan slider',
                'Publikasikan galeri'
            ],
            'notes' => [
                'Galeri ditampilkan di halaman utama',
                'Slider dapat dikonfigurasi'
            ]
        ],
        'app_player' => [
            'title' => 'Tentang Modul Galeri Video',
            'description' => 'Modul galeri video kegiatan dan acara pengadilan.',
            'features' => [
                'Streaming video',
                'Kelola video',
                'Kategori video',
                'Tampilan publik'
            ],
            'usage' => [
                'Tambah video ke galeri',
                'Putar video',
                'Lihat daftar video'
            ],
            'notes' => [
                'Video disimpan di server lokal',
                'Hanya dapat diakses dari jaringan lokal'
            ]
        ],
        'app_gdrive' => [
            'title' => 'Tentang Modul Google Drive',
            'description' => 'Modul upload file ke Google Drive.',
            'features' => [
                'Upload ke GDrive',
                'Generate shareable link',
                'Generate QR code',
                'Manajemen file'
            ],
            'usage' => [
                'Upload file ke Google Drive',
                'Dapatkan link分享',
                'Scan QR code untuk akses'
            ],
            'notes' => [
                'Memerlukan service account Google',
                'File disimpan di Google Drive organisasi'
            ]
        ],
        'monitoring_sisapanjar' => [
            'title' => 'Tentang Modul Sisa Panjar',
            'description' => 'Modul informasi sisa panjar biaya perkara.',
            'features' => [
                'Data sisa panjar',
                'Detail perkara',
                'Filter pencarian',
                'Laporan keuangan'
            ],
            'usage' => [
                'Cari perkara berdasarkan nomor',
                'Lihat detail panjar',
                'Cetak laporan'
            ],
            'notes' => [
                'Panjar adalah biaya muka perkara',
                'Sisa panjar dikembalikan ke pihak'
            ]
        ],
        'monitoring_rencanaputus' => [
            'title' => 'Tentang Modul Rencana Putus',
            'description' => 'Modul rencana perkara yang akan diputus.',
            'features' => [
                'Jadwal rencana putus',
                'Perkara belum putus',
                'Kalender hakim',
                'Filter tanggal'
            ],
            'usage' => [
                'Lihat rencana putus per tanggal',
                'Pantau perkara yang akan diputus',
                'Atur jadwal sidang'
            ],
            'notes' => [
                'Membantu perencanaan sidang',
                'Terintegrasi dengan kalender'
            ]
        ],
        'monitoring_putusecourt' => [
            'title' => 'Tentang Modul e-Court Putus',
            'description' => 'Modul monitoring upload dan TTE putusan e-Court.',
            'features' => [
                'Upload putusan',
                'TTE (Tanda Tangan Elektronik)',
                'Status upload',
                'Monitoring lengkap'
            ],
            'usage' => [
                'Upload putusan ke e-Court',
                'Lakukan TTE',
                'Pantau status upload'
            ],
            'notes' => [
                'e-Court terintegrasi dengan MA',
                'TTE diperlukan untuk keabsahan'
            ]
        ],
        'monitoring_prodeo' => [
            'title' => 'Tentang Modul Prodeo',
            'description' => 'Modul kontrol panggilan perkara prodeo (cuma-cuma).',
            'features' => [
                'Kontrol panggilan',
                'Monitoring prodeo',
                'Status panggilan',
                'Cetak panggilan'
            ],
            'usage' => [
                'Kelola panggilan prodeo',
                'Pantau status panggilan',
                'Cetak surat panggilan'
            ],
            'notes' => [
                'Prodeo = perkara cuma-cuma',
                'Untuk pihak yang tidak mampu'
            ]
        ],
        'monitoring_ghaib' => [
            'title' => 'Tentang Modul Panggilan Ghaib',
            'description' => 'Modul sinkronisasi panggilan ghaib dari SIPP.',
            'features' => [
                'Sinkronisasi SIPP',
                'Status panggilan',
                'Tracking panggilan',
                'Cetak laporan'
            ],
            'usage' => [
                'Sinkronkan data dari SIPP',
                'Pantau status panggilan ghaib',
                'Cetak laporan'
            ],
            'notes' => [
                'Ghaib = pihak yang tidak hadir',
                'Memerlukan panggilan khusus'
            ]
        ],
        'monitoring_ghaibpbt' => [
            'title' => 'Tentang Modul PBT',
            'description' => 'Modul sinkronisasi pemberitahuan putusan (PBT) dari SIPP.',
            'features' => [
                'Sinkronisasi PBT',
                'Status pemberitahuan',
                'Tracking PBT',
                'Cetak laporan'
            ],
            'usage' => [
                'Sinkronkan data PBT dari SIPP',
                'Pantau status pemberitahuan',
                'Cetak laporan'
            ],
            'notes' => [
                'PBT = Pemberitahuan Putusan',
                'Wajib disampaikan ke pihak'
            ]
        ],
        'monitoring_disposisi' => [
            'title' => 'Tentang Modul Disposisi',
            'description' => 'Modul ini menampilkan progress disposisi perkara.',
            'features' => [
                'Manajemen disposisi',
                'Monitoring disposisi'
            ],
            'usage' => [
                'Buat disposisi perkara',
                'Pantau status disposisi'
            ],
            'notes' => [
                'Disposisi = penugasan perkara',
            ]
        ],
        'monitoring_rencana_bht' => [
            'title' => 'Tentang Modul Rencana BHT',
            'description' => 'Modul ini menampilkan monitoring input tanggal BHT.',
            'features' => [
                'Input tanggal rencana BHT',
                'Monitoring input tanggal BHT',
                'Monitoring status BHT',
                'Monitoring status Akta Cerai',
            ],
            'usage' => [
                'Input tanggal rencana BHT',
                'Pantau status BHT',
                'Input tanggal BHT pada hari H'
            ],
        ],
        'monitoring_acweb' => [
            'title' => 'Tentang Modul AC Web',
            'description' => 'Modul sinkronisasi akta cerai dari SIPP ke web.',
            'features' => [
                'Sinkronisasi AC',
                'Status akta cerai',
                'Publikasi online',
                'Cetak laporan'
            ],
            'usage' => [
                'Sinkronkan data AC dari SIPP',
                'Pantau status AC',
                'Publikasi ke website'
            ],
            'notes' => [
                'AC = Akta Cerai',
                'Tersedia untuk publik'
            ]
        ],
        'monitoring_acreservation' => [
            'title' => 'Tentang Modul Reservasi AC',
            'description' => 'Modul reservasi akta cerai melalui sistem online.',
            'features' => [
                'Reservasi online',
                'Jadwal pengambilan',
                'Konfirmasi reservasi',
                'Statistik reservasi'
            ],
            'usage' => [
                'Pelanggan reservasi jadwal',
                'Petugas konfirmasi reservasi',
                'Pantau statistik'
            ],
            'notes' => [
                'Integrasi dengan SIPACAR',
                'Tersedia di kantor dan MPP'
            ]
        ],
        'monitoring_ac' => [
            'title' => 'Tentang Modul Akta Cerai Terbit',
            'description' => 'Modul monitoring dan kontrol akta cerai yang telah diterbitkan dari perkara cerai.',
            'features' => [
                'Monitoring Akta Cerai',
                'Filter berdasarkan tahun dan bulan BHT',
                'Filter berdasarkan status AC (Sudah/Belum)',
                'Filter berdasarkan jenis perkara',
                'Status perkara',
                'Informasi verzet, banding, kasasi, PK'
            ],
            'usage' => [
                'Gunakan filter tahun untuk memilih periode BHT',
                'Gunakan filter bulan untuk memilih bulan BHT',
                'Pilih status AC untuk memfilter perkara',
                'Pilih jenis perkara untuk filter spesifik',
                'Gunakan scroll horizontal untuk melihat kolom lainnya'
            ],
            'notes' => [
                'AC = Akta Cerai',
                'BHT = Berkas Masuk Tailor',
                'Data diambil dari database SIPP'
            ]
        ],
        'monitoring_pbt' => [
            'title' => 'Tentang Modul Kontrol PIP',
            'description' => 'Modul kontrol pemberitahuan isi putusan (PIP) untuk memonitor proses pengiriman putusan kepada pihak-pihak yang bersangkutan.',
            'features' => [
                'Monitoring PIP',
                'Filter berdasarkan hakim',
                'Filter berdasarkan panitera pengganti',
                'Filter berdasarkan jenis perkara',
                'Status pengiriman',
                'Cetak laporan'
            ],
            'usage' => [
                'Lihat daftar perkara yang perlu PIP',
                'Pilih filter untuk melihat spesifik',
                'Update status pengiriman',
                'Cetak laporan jika diperlukan'
            ],
            'notes' => [
                'PIP = Pemberitahuan Isi Putusan',
                'Data diambil dari database SIPP'
            ]
        ],
        'pnbp_jenis' => [
            'title' => 'Tentang Modul PNBP Jenis',
            'description' => 'Modul statistik PNBP (Penerimaan Negara Bukan Pajak) berdasarkan jenis perkara.',
            'features' => [
                'Statistik PNBP',
                'Filter berdasarkan jenis perkara',
                'Filter periode',
                'Grafik visualisasi'
            ],
            'usage' => [
                'Lihat statistik PNBP',
                'Pilih filter jenis perkara',
                'Pilih periode filter'
            ],
            'notes' => [
                'PNBP = Penerimaan Negara Bukan Pajak',
                'Data diambil dari database SIPP'
            ]
        ],
        'pnbp_terima' => [
            'title' => 'Tentang Modul PNBP Diterima',
            'description' => 'Modul statistik PNBP yang telah diterima.',
            'features' => [
                'Statistik PNBP',
                'Filter jenis biaya',
                'Filter periode',
                'Grafik visualisasi'
            ],
            'usage' => [
                'Lihat statistik PNBP',
                'Pilih filter jenis biaya',
                'Pilih periode filter'
            ],
            'notes' => [
                'PNBP = Penerimaan Negara Bukan Pajak',
                'Data diambil dari database SIPP'
            ]
        ],
        'monitoring_pk' => [
            'title' => 'Tentang Modul PK',
            'description' => 'Modul rekapitulasi keadaan perkara peninjauan kembali (PK).',
            'features' => [
                'Statistik PK',
                'Filter periode',
                'Data perkara PK',
                'Grafik visualisasi'
            ],
            'usage' => [
                'Lihat statistik PK',
                'Filter berdasarkan periode',
                'Lihat detail perkara'
            ],
            'notes' => [
                'PK = Peninjauan Kembali',
                'Upaya hukum luar biasa'
            ]
        ],
        'monitoring_banding' => [
            'title' => 'Tentang Modul Banding',
            'description' => 'Modul rekapitulasi keadaan perkara banding secara periodik.',
            'features' => [
                'Statistik perkara banding',
                'Filter periode bulanan/tahunan',
                'Data perkara banding',
                'Grafik visualisasi',
                'Detail perkara banding'
            ],
            'usage' => [
                'Gunakan filter periode untuk memilih bulan/tahun',
                'Lihat statistik banding',
                'Klik pada angka untuk melihat detail perkara',
                'Gunakan scroll horizontal untuk melihat kolom lainnya'
            ],
            'notes' => [
                'Banding = upaya hukum terhadap putusan',
                'Data diambil dari database SIPP'
            ]
        ],
        'monitoring_kasasi' => [
            'title' => 'Tentang Modul Kasasi',
            'description' => 'Modul rekapitulasi keadaan perkara kasasi.',
            'features' => [
                'Statistik kasasi',
                'Filter periode',
                'Data perkara kasasi',
                'Grafik visualisasi'
            ],
            'usage' => [
                'Lihat statistik kasasi',
                'Filter berdasarkan periode',
                'Lihat detail perkara'
            ],
            'notes' => [
                'Kasasi = upaya hukum ke MA',
                'Merupakan tingkat tertinggi'
            ]
        ],
        'monitoring_jadwal' => [
            'title' => 'Tentang Modul Jadwal Sidang',
            'description' => 'Modul monitoring jadwal sidang dan notifikasi antrian.',
            'features' => [
                'Monitoring jadwal sidang',
                'Notifikasi antrian',
                'Filter ruangan',
                'Statistik sidang'
            ],
            'usage' => [
                'Lihat jadwal sidang',
                'Pilih ruangan sidang',
                'Kirim notifikasi antrian',
                'Gunakan scroll horizontal untuk melihat kolom lainnya'
            ],
            'notes' => [
                'Data diambil dari database SIPP',
                'Notifikasi dikirim via WhatsApp'
            ]
        ],
        'monitoring_pihak' => [
            'title' => 'Tentang Modul Pihak',
            'description' => 'Modul statistik jumlah pihak yang berperkara.',
            'features' => [
                'Data pihak',
                'Statistik pihak',
                'Filter periode',
                'Laporan pihak'
            ],
            'usage' => [
                'Lihat statistik pihak',
                'Filter berdasarkan periode',
                'Cetak laporan'
            ],
            'notes' => [
                'Mencakup penggugat dan tergugat',
                'Data dari SIPP'
            ]
        ],
        'monitoring_faktor' => [
            'title' => 'Tentang Modul Faktor Perceraian',
            'description' => 'Modul statistik faktor penyebab perceraian.',
            'features' => [
                'Data faktor',
                'Statistik faktor',
                'Filter periode',
                'Grafik visualisasi'
            ],
            'usage' => [
                'Lihat faktor perceraian',
                'Filter berdasarkan periode',
                'Analisis data'
            ],
            'notes' => [
                'Faktor meliputi: ekonomi, KDKB, etc',
                'Berguna untuk pencegahan'
            ]
        ],
        'monitoring_cerai' => [
            'title' => 'Tentang Modul Cerai',
            'description' => 'Modul data perkara cerai dengan usia di bawah 19 tahun dan dispensasi kawin.',
            'features' => [
                'Data cerai muda',
                'Dispensasi kawin',
                'Filter usia',
                'Laporan'
            ],
            'usage' => [
                'Lihat data cerai muda',
                'Filter berdasarkan usia',
                'Cetak laporan'
            ],
            'notes' => [
                'Usia di bawah 19 memerlukan dispensasi',
                'Data untuk pengawasan'
            ]
        ],
        'kinerja_tunggakan' => [
            'title' => 'Tentang Modul Tunggakan',
            'description' => 'Modul monitoring tunggakan perkara berdasarkan durasi.',
            'features' => [
                'Data tunggakan',
                'Kategorisasi durasi',
                'Filter perkara',
                'Laporan tunggakan'
            ],
            'usage' => [
                'Lihat tunggakan per kategori',
                'Filter berdasarkan durasi',
                'Follow up perkara'
            ],
            'notes' => [
                'Tunggakan会影响 performa pengadilan',
                'Perlu diminimalisir'
            ]
        ],
        'whatsapp_queue' => [
            'title' => 'Tentang Modul WhatsApp Queue',
            'description' => 'Modul antrian pengiriman pesan WhatsApp.',
            'features' => [
                'Kelola antrian',
                'Status pengiriman',
                'Retry failed',
                'Log pesan'
            ],
            'usage' => [
                'Lihat antrian pesan',
                'Kelola pesan gagal',
                'Cek log pengiriman'
            ],
            'notes' => [
                'Terintegrasi dengan WhatsApp Gateway',
                'Pesan dikirim secara terjadwal'
            ]
        ],
        'app_sipacar' => [
            'title' => 'Tentang Modul SIPACAR',
            'description' => 'Modul reservasi akta cerai online.',
            'features' => [
                'Reservasi online',
                'Jadwal pengambilan',
                'Konfirmasi reservasi',
                'Notifikasi'
            ],
            'usage' => [
                'Pilih tanggal dan jam',
                'Terima konfirmasi'
            ],
            'notes' => [
                'SIPACAR = Sistem Informasi Pemesanan Akta Cerai',
                'Mengurangi antrian di kantor'
            ]
        ],
        'rekapitulasi_kecamatan' => [
            'title' => 'Tentang Modul Rekapitulasi Kecamatan',
            'description' => 'Modul rekapitulasi perkara berdasarkan kecamatan.',
            'features' => [
                'Data perkara per kecamatan',
                'Filter periode',
                'Statistik kecamatan',
                'Grafik visualisasi'
            ],
            'usage' => [
                'Pilih periode filter',
                'Lihat data per kecamatan',
                'Klik untuk detail perkara'
            ],
            'notes' => [
                'Data diambil dari database SIPP'
            ]
        ],
        'external_antrian' => [
            'title' => 'Tentang Modul Antrian Publik',
            'description' => 'Modul antrian publik untuk pelayanan pengadilan.',
            'features' => [
                'Ambil nomor antrian',
                'Display antrian',
                'Panggil nomor',
                'Riwayat antrian'
            ],
            'usage' => [
                'Klik Ambil Nomor untuk antrian',
                'Display menunjukkan nomor saat ini',
                'Petugas memanggil nomor'
            ],
            'notes' => [
                'Tersedia untuk publik',
                'Hanya dapat diakses dari lokal'
            ]
        ],
        'external_lhk' => [
            'title' => 'Tentang Modul LHK Publik',
            'description' => 'Modul pengelolaan LHK untuk akses publik.',
            'features' => [
                'Upload LHK',
                'Verifikasi LHK',
                'Publikasi LHK',
                'Pencarian LHK'
            ],
            'usage' => [
                'Upload file LHK',
                'Verifikasi kelengkapan',
                'Publikasi untuk publik'
            ],
            'notes' => [
                'LHK = Laporan Harian Kerja',
                'Tersedia untuk publik'
            ]
        ],
        'external_pegawai' => [
            'title' => 'Tentang Modul Profil Pegawai Publik',
            'description' => 'Modul menampilkan profil pegawai untuk akses publik.',
            'features' => [
                'Daftar pegawai',
                'Profil individu',
                'Jabatan dan struktur',
                'Kontak'
            ],
            'usage' => [
                'Lihat daftar pegawai',
                'Klik profil untuk detail',
                'Cari pegawai'
            ],
            'notes' => [
                'Tersedia untuk publik',
                'Data read-only'
            ]
        ],
        'app_faq' => [
            'title' => 'Tentang Modul FAQ',
            'description' => 'Modul pengelolaan Frequently Asked Questions.',
            'features' => [
                'Kelola FAQ',
                'Kategori FAQ',
                'Pencarian FAQ',
                'Tampilan publik'
            ],
            'usage' => [
                'Tambah FAQ baru',
                'Edit FAQ yang ada',
                'Kelola kategori',
                'Publikasi FAQ'
            ],
            'notes' => [
                'FAQ = Frequently Asked Questions',
                'Tersedia untuk publik'
            ]
        ],
        'app_siaster' => [
            'title' => 'Tentang Modul SIASTER',
            'description' => 'Modul sistem antrian sidang terjadwal.',
            'features' => [
                'Antrian online',
                'Display antrian',
                'Panggil nomor',
                'Statistik antrian'
            ],
            'usage' => [
                'Ambil nomor antrian',
                'Display menunjukkan urutan',
                'Panggil oleh petugas'
            ],
            'notes' => [
                'SIASTER = Sistem Antrian Sidang Terjadwal',
                'Hanya dapat diakses dari lokal'
            ]
        ]
    ];

    return isset($defaults[$moduleType]) ? $defaults[$moduleType] : null;
}

/**
 * Get color class for score label
 * 
 * @param string $colorName Color name (Hijau, Biru, Cyan, Kuning, Merah)
 * @return string Bootstrap color class
 */
function get_score_color_class($colorName)
{
    $map = array(
        'Hijau' => 'success',
        'Biru' => 'primary',
        'Cyan' => 'info',
        'Kuning' => 'warning',
        'Merah' => 'danger'
    );
    return isset($map[$colorName]) ? $map[$colorName] : 'secondary';
}
