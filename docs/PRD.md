# Product Requirements Document (PRD)
**Nama Proyek:** NasHRIS — Enterprise Human Resource Information System  
**Versi Dokumen:** 1.0  
**Tech Stack:** Laravel 13 (Monolith), MySQL (Laragon), Tailwind CSS, Alpine.js  
**Target Desain:** Desktop-First Responsive Web Application  

---

## 1. Ringkasan Eksekutif & Tujuan Proyek
**NasHRIS** adalah aplikasi berbasis web untuk mengelola siklus administrasi sumber daya manusia (SDM) secara terpusat—mulai dari pendataan karyawan, pencatatan absensi berbasis *shift*, alur persetujuan cuti berjenjang, hingga kalkulasi penggajian (*payroll*) dan analitik HR.

**Tujuan Pengembangan:**
1. Mendemonstrasikan perancangan arsitektur database relasional yang kompleks, normalisasi data, dan integritas transaksi (`DB::transaction`).
2. Menerapkan *Role-Based Access Control* (RBAC) dan *multi-level approval workflow* sesuai standar operasional perusahaan.
3. Menyajikan antarmuka *desktop-first* yang lega untuk pengolahan tabel data besar bagi tim HR, namun tetap responsif untuk akses cepat karyawan via perangkat seluler.

---

## 2. Peran Pengguna & Hak Akses (RBAC)

| Peran (Role) | Deskripsi & Tanggung Jawab Utama | Hak Akses Utama |
| :--- | :--- | :--- |
| **Super Admin / HR (`hr`)** | Mengelola seluruh siklus data SDM, struktur organisasi, jadwal kerja, dan penggajian. | Full CRUD karyawan, departemen, jabatan, shift, approval final cuti, generate payroll, melihat seluruh dashboard & audit log. |
| **Manager / Head (`manager`)** | Memimpin satu departemen/divisi tertentu dan memantau kedisiplinan timnya. | Melihat data & rekap absensi anggota departemennya saja, memberikan persetujuan tahap 1 (Approve/Reject) pada pengajuan cuti tim. |
| **Employee (`employee`)** | Karyawan reguler yang menggunakan fitur *Employee Self-Service* (ESS). | Melakukan Clock-In/Out harian, melihat riwayat absensi pribadi, mengajukan cuti/izin, memperbarui profil terbatas, dan mengunduh slip gaji PDF. |

> **Fitur One-Click Demo Login:** Pada halaman `/login`, sediakan tombol akses cepat (*"Login as HR Admin"*, *"Login as Manager"*, *"Login as Employee"*) agar penguji dapat langsung mengeksplorasi setiap peran tanpa registrasi manual.

---

## 3. Spesifikasi Fitur Fungsional (Functional Requirements)

### Modul 1: Master Data Organisasi & Karyawan
* **Manajemen Departemen & Jabatan:** CRUD data departemen (misal: *IT Engineering, Human Resources, Finance, Marketing*) dan level jabatan beserta rentang gaji pokok default.
* **Direktori Karyawan:**
  * Data identitas: NIK Karyawan (auto-generate: `EMP-2026-001`), Nama Lengkap, Email, No. HP, Alamat.
  * Data kepegawaian: Departemen, Jabatan, Atasan Langsung (`manager_id`), Shift Kerja, Tanggal Bergabung, Status Kontrak (`PKWTT`, `PKWT`, `Probation`, `Internship`), serta Tanggal Berakhir Kontrak.
  * Manajemen Dokumen: Unggah dan pratinjau dokumen digital (`KTP`, `NPWP`, `CV`, `CONTRACT`) dengan validasi tipe file (`PDF/JPG/PNG`, maksimal 2MB) yang dilindungi Policy.

### Modul 2: Manajemen Shift & Absensi (Attendance)
* **Pengaturan Shift Kerja:** Admin dapat membuat master shift (misal: *Regular Office* `08:00 - 17:00`, *Shift Pagi* `07:00 - 15:00`) beserta batas toleransi keterlambatan (default: 15 menit).
* **Clock-In & Clock-Out Harian (ESS):**
  * Karyawan hanya dapat melakukan *Clock-In* satu kali per hari sesuai shift yang ditugaskan.
  * Sistem otomatis mencatat *timestamp*, IP address, dan menghitung status kehadiran (`Present`, `Late`, `Absent`).
  * Jika melewati batas toleransi, sistem otomatis menghitung durasi keterlambatan dalam satuan menit (`late_minutes`).
* **Rekapitulasi & Koreksi Absensi:** HR dapat memfilter absensi berdasarkan rentang tanggal, departemen, atau karyawan, serta melakukan koreksi manual.

### Modul 3: Pengajuan Cuti & Izin (Leave Management)
* **Kuota Cuti Otomatis:** Setiap karyawan memiliki kuota cuti tahunan (default 12 hari/tahun) yang tersimpan pada tabel `leave_balances`.
* **Form Pengajuan (ESS):** Karyawan memilih jenis izin (`Annual`, `Sick`, `Special`), tanggal mulai, tanggal selesai, alasan, dan lampiran surat dokter (opsional/wajib jika sakit lebih dari 1 hari).
* **Alur Persetujuan Berjenjang (Multi-Level Approval):**
  1. Status awal: `Pending Manager`.
  2. **Manager** departemen terkait meninjau pengajuan -> jika disetujui, status berubah menjadi `Pending HR`. Jika ditolak, status menjadi `Rejected` beserta catatan penolakan.
  3. **HR Admin** melakukan verifikasi akhir -> jika disetujui (`Approved`), sistem otomatis memotong saldo cuti pada `leave_balances` (khusus kategori `Annual`) dan menandai tanggal tersebut pada rekap absensi sebagai `On Leave`.

### Modul 4: Penggajian Dasar (Basic Payroll)
* **Komponen Gaji Karyawan:**
  * Pendapatan: Gaji Pokok (`base_salary`) dan Tunjangan Tetap (`allowance_fixed`).
  * Potongan: Potongan Keterlambatan (`late_deduction`) dan Potongan Absen Tanpa Keterangan (`absence_deduction`).
* **Generate Payroll Bulanan:** HR memilih periode bulan dan tahun, sistem mengkalkulasi draft gaji seluruh karyawan aktif menggunakan `DB::transaction()`.
* **Finalisasi & Slip Gaji PDF:** Setelah HR mengubah status payroll dari `Draft` ke `Paid`, karyawan dapat melihat rincian gaji dan mengunduh **Slip Gaji resmi berformat PDF** dari akun masing-masing.

### Modul 5: HR Analytics Dashboard & Audit Log
* **KPI Summary Cards:** Total Karyawan Aktif, Persentase Kehadiran Hari Ini, Pengajuan Cuti Menunggu Persetujuan, dan Total Estimasi Pengeluaran Payroll Bulan Ini.
* **Visualisasi Grafik:**
  * Grafik batang (*Bar Chart*) distribusi jumlah karyawan per departemen.
  * Grafik garis (*Line Chart*) tren tingkat kehadiran vs keterlambatan 6 bulan terakhir.
* **Contract Expiry Reminder:** Widget peringatan otomatis yang menampilkan daftar karyawan berstatus `PKWT / Probation` yang masa kontraknya akan berakhir dalam **30 hari ke depan**.
* **Audit Log:** Tabel riwayat aktivitas menggunakan `spatie/laravel-activitylog` yang mencatat perubahan data penting lengkap dengan nama aktor dan waktu kejadian.