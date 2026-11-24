# NasHRIS — Enterprise Human Resource Information System

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-13.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 13" />
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.2+" />
  <img src="https://img.shields.io/badge/Tailwind_CSS-3.4-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS" />
  <img src="https://img.shields.io/badge/Alpine.js-3.x-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white" alt="Alpine.js" />
  <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL 8.0" />
  <img src="https://img.shields.io/badge/Tests-57%20Passed%20(166%20Assertions)-success?style=for-the-badge" alt="Tests" />
  <img src="https://img.shields.io/badge/License-MIT-green?style=for-the-badge" alt="License" />
</p>

---

## 1. Tentang Proyek (Overview)

**NasHRIS** adalah aplikasi web Enterprise Human Resource Information System (HRIS) modern dengan pendekatan desain *desktop-first responsive*. Aplikasi ini dirancang untuk mendemonstrasikan perancangan arsitektur database relasional berskala perusahaan, integritas transaksi data (`DB::transaction()`), kontrol hak akses berbasis peran (RBAC), alur persetujuan berjenjang (*multi-level approval workflow*), kalkulasi presensi & penggajian otomatis (*payroll engine*), serta jejak audit komprehensif (*audit trail*).

Dikembangkan secara monolitik menggunakan **Laravel 13**, **Tailwind CSS**, dan **Alpine.js** tanpa ketergantungan framework SPA (No React/Vue/Inertia/Livewire), NasHRIS menghadirkan performa server-side rendering yang sangat cepat, aman dari celah manipulasi URL (*Anti-IDOR*), dan siap digunakan untuk kebutuhan operasional perusahaan.

---

## 2. Fitur Utama & Keunggulan Arsitektur

### 🔐 1. Role-Based Access Control (RBAC) & Keamanan Autentikasi Produksi
- **3 Peran Pengguna Utama:**
  - **Super Admin / HR (`hr`):** Akses penuh pengelolaan master data, direktori karyawan, persetujuan cuti tahap final, generator payroll bulanan, analytics dashboard, dan audit log.
  - **Department Manager (`manager`):** Supervisi presensi tim divisi dan peninjau pengajuan cuti tahap 1 (*Pending Manager* &rarr; *Pending HR*).
  - **Employee Self-Service (`employee`):** Portal mandiri karyawan untuk Clock-In/Out harian, pengajuan cuti berlampiran, pemantauan saldo kuota cuti, dan unduh slip gaji PDF.
- **Autentikasi Produksi & Manajemen Password:**
  - Login standar email & password dilindungi *rate limiting* (`throttle:5,1`).
  - Pembuatan akun karyawan oleh HR otomatis men-generate *temporary password* yang dapat langsung disalin HR dan menandai `must_change_password = true`, sehingga karyawan wajib mengganti kata sandi pada login pertama kali.
  - Halaman **Profil Saya** tersedia untuk seluruh peran (HR, Manager, Employee) guna memperbarui nomor HP, alamat domisili, dan kata sandi secara mandiri.

### 🏢 2. Master Data & Direktori Karyawan Lengkap
- **Departemen & Jabatan:** Pengaturan struktur divisi dan batas gaji pokok default dengan proteksi integritas relasi foreign key.
- **Auto-Generated NIK:** Format kode karyawan terstandar otomatis `EMP-YYYY-XXX` (contoh: `EMP-2026-001`).
- **Manajemen Berkas & Anti-IDOR:** Unggah dan pratinjau dokumen digital (`KTP`, `NPWP`, `CV`, `CONTRACT`) yang dilindungi Laravel Policy (`EmployeeDocumentPolicy`).

### ⏱️ 3. Presensi Berbasis Shift & Otomatisasi Keterlambatan
- Master jadwal kerja shift dengan toleransi keterlambatan (*grace period*, default: 15 menit).
- Kalkulasi otomatis durasi keterlambatan dalam menit (`late_minutes`) dan penentuan status kehadiran (`Present`, `Late`, `Absent`, `On Leave`).
- Widget Clock-In / Clock-Out interaktif dengan jam realtime (Alpine.js) dan proteksi Clock-In ganda.
- Antarmuka koreksi absensi manual khusus HR.

### 🏖️ 4. Alur Persetujuan Cuti Berjenjang (Multi-Level Approval)
- Saldo cuti tahunan otomatis (default 12 hari/tahun) tersimpan pada tabel `leave_balances`.
- **Workflow Persetujuan 2 Tahap:**
  1. Karyawan mengajukan cuti &rarr; status: `Pending Manager`.
  2. Manager divisi mereview &rarr; jika disetujui, status menjadi `Pending HR`.
  3. HR memverifikasi akhir &rarr; status menjadi `Approved`, **kuota cuti otomatis dipotong**, dan **tabel presensi otomatis disinkronkan** menjadi `On Leave`.
  4. Jika ditolak pada tahap manapun, status menjadi `Rejected` disertai catatan penolakan dan saldo kuota tidak berkurang.

### 💰 5. Mesin Penggajian (Payroll Engine) & Slip Gaji PDF
- **Kalkulasi Formula Penggajian Bulanan Otomatis:**
  $$\text{daily\_rate} = \frac{\text{base\_salary}}{22}$$
  $$\text{minute\_rate} = \frac{\text{daily\_rate}}{480}$$
  $$\text{late\_deduction} = \text{total\_late\_minutes} \times \text{minute\_rate}$$
  $$\text{absence\_deduction} = \text{total\_absent\_days} \times \text{daily\_rate}$$
  $$\text{net\_salary} = (\text{base\_salary} + \text{allowances}) - (\text{late\_deduction} + \text{absence\_deduction})$$
- Dijalankan di dalam transaksi atomik `DB::transaction()`.
- Siklus batch penggajian dari status `Draft` hingga difinalisasi menjadi `Paid`.
- **Slip Gaji PDF Resmi:** Dibuat menggunakan `barryvdh/laravel-dompdf` dengan proteksi Anti-IDOR (`PayrollItemPolicy`), sehingga karyawan hanya dapat mengunduh slip gaji miliknya sendiri yang telah berstatus `Paid`.

### 📊 6. HR Analytics Dashboard & Audit Trail
- KPI Cards: Total Karyawan, Persentase Presensi Hari Ini, Cuti Menunggu Review HR, dan Estimasi Pengeluaran Payroll Bulanan.
- **Visualisasi Grafik Chart.js:**
  - *Doughnut Chart*: Distribusi proporsi karyawan per departemen.
  - *Multi-Line Chart*: Tren kedisiplinan 6 bulan terakhir (Hadir vs Terlambat vs Alpa).
- **Contract Expiry Reminder:** Widget otomatis untuk memantau karyawan berstatus `PKWT / Probation` yang kontraknya berakhir dalam **30 hari ke depan**.
- **Audit Log Viewer:** Penampil riwayat mutasi data dari `spatie/laravel-activitylog` lengkap dengan diff JSON perubahan atribut sebelum dan sesudah mutasi.

---

## 3. Diagram Alur & Arsitektur Sistem

### Alur Persetujuan Cuti Berjenjang (Multi-Level Approval Workflow)

```mermaid
flowchart TD
    A["Karyawan (ESS)"] -->|"Mengajukan Cuti (Formulir + Lampiran)"| B["Status: Pending Manager"]
    B --> C{"Review Manager Divisi"}
    C -->|"Tolak (Rejection Note)"| D["Status: Rejected (Kuota Utuh)"]
    C -->|"Setujui (Tahap 1)"| E["Status: Pending HR"]
    E --> F{"Verifikasi Akhir HR"}
    F -->|"Tolak (Rejection Note)"| D
    F -->|"Setujui Final"| G["Status: Approved"]
    G --> H["DB::transaction()"]
    H --> I["Potong Saldo Kuota (leave_balances)"]
    H --> J["Sinkronisasi Presensi: On Leave (attendances)"]
```

### Entity-Relationship Diagram (ERD)

```mermaid
erDiagram
    users ||--o{ employees : "has profile"
    users ||--o{ payrolls : "processes"
    users ||--o{ leave_requests : "manager approves"
    users ||--o{ leave_requests : "hr approves"
    departments ||--o{ positions : "has"
    departments ||--o{ employees : "belongs to"
    positions ||--o{ employees : "assigned to"
    shifts ||--o{ employees : "scheduled"
    shifts ||--o{ attendances : "shift record"
    employees ||--o{ employees : "supervises (manager_id)"
    employees ||--o{ employee_documents : "owns"
    employees ||--o{ attendances : "records"
    employees ||--o{ leave_balances : "tracks"
    employees ||--o{ leave_requests : "submits"
    employees ||--o{ payroll_items : "receives"
    payrolls ||--o{ payroll_items : "contains"

    users {
        bigint id PK
        string name
        string email UK
        string password
        boolean is_active
        boolean must_change_password
        timestamps timestamps
    }

    departments {
        bigint id PK
        string name
        string code UK
        text description
    }

    positions {
        bigint id PK
        bigint department_id FK
        string title
        decimal base_salary_default
    }

    shifts {
        bigint id PK
        string name
        time start_time
        time end_time
        integer grace_period_minutes
    }

    employees {
        bigint id PK
        bigint user_id FK
        bigint department_id FK
        bigint position_id FK
        bigint manager_id FK
        bigint shift_id FK
        string employee_code UK
        string phone
        text address
        date join_date
        enum contract_type
        date contract_end_date
        decimal base_salary
        decimal allowance_fixed
    }

    employee_documents {
        bigint id PK
        bigint employee_id FK
        enum document_type
        string file_path
    }

    attendances {
        bigint id PK
        bigint employee_id FK
        bigint shift_id FK
        date date
        time clock_in
        time clock_out
        integer late_minutes
        enum status
        string ip_address
        text notes
    }

    leave_balances {
        bigint id PK
        bigint employee_id FK
        integer year
        integer total_quota
        integer used_quota
        integer remaining_quota
    }

    leave_requests {
        bigint id PK
        bigint employee_id FK
        enum leave_type
        date start_date
        date end_date
        integer total_days
        text reason
        string attachment_path
        enum status
        bigint manager_approved_by FK
        bigint hr_approved_by FK
        text rejection_note
    }

    payrolls {
        bigint id PK
        integer month
        integer year
        enum status
        bigint processed_by FK
        timestamp processed_at
    }

    payroll_items {
        bigint id PK
        bigint payroll_id FK
        bigint employee_id FK
        decimal base_salary
        decimal allowances
        decimal late_deduction
        decimal absence_deduction
        decimal net_salary
    }
```

---

## 4. Kredensial Akun Utama (Production Baseline)

Setelah menjalankan `php artisan migrate:fresh --seed`, sistem membuat 1 akun untuk masing-masing peran (*role*) utama:

| Peran (Role) | Nama Pengguna | Alamat Email | Password Awal | Keterangan / Hak Akses |
| :--- | :--- | :--- | :--- | :--- |
| **Super Admin HR** | Super Admin HR | `admin@nashris.com` | `password123` | Akses penuh Master Data, Karyawan, Payroll, Analytics, Audit Log |
| **Manager** | Bambang Wijaya | `manager@nashris.com` | `password123` | Manager IT Engineering: Approval cuti & monitoring tim divisi |
| **Employee** | Muhammad Nashir Lidienillah | `nashir@gmail.com` | `password123` | Fullstack Developer (IT): ESS, Absensi mandiri, Pengajuan Cuti, Slip Gaji |

> **Catatan Keamanan:** Segera ubah kata sandi melalui menu **Profil Saya** setelah login pertama kali pada environment produksi. Jika Anda membutuhkan 30+ data dummy untuk simulasi lokal, jalankan `php artisan db:seed --class=DummyDataSeeder`.

---

## 5. Panduan Instalasi & Menjalankan Lokal

### Prasyarat Sistem
- PHP 8.2 atau lebih tinggi (disarankan PHP 8.4)
- MySQL 8.0 (atau Laragon / XAMPP)
- Composer 2.x
- Node.js 18+ & npm

### Langkah Instalasi
1. **Clone repository:**
   ```bash
   git clone https://github.com/your-username/nashris.git
   cd nashris
   ```

2. **Install dependensi Composer:**
   ```bash
   composer install
   ```

3. **Install dependensi Node.js & build frontend assets:**
   ```bash
   npm install
   npm run build
   ```

4. **Konfigurasi file `.env`:**
   Salin `.env.example` ke `.env` dan sesuaikan koneksi database MySQL:
   ```env
   APP_NAME=NasHRIS
   APP_ENV=local
   APP_KEY=
   APP_DEBUG=true
   APP_URL=http://localhost:8000

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=nashris
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Generate Application Key:**
   ```bash
   php artisan key:generate
   ```

6. **Migrasi Database & Jalankan Production Baseline Seeder:**
   Perintah ini akan membuat seluruh tabel serta men-seed data baseline produksi (Spatie roles `hr`/`manager`/`employee`, 1 shift default `Regular Office 08:00-17:00`, dan 1 akun Super Admin HR utama):
   ```bash
   php artisan migrate:fresh --seed
   ```
   - **Kredensial Super Admin HR Default:**
     - Email: `admin@nashris.com`
     - Password: `password123`
   *(Opsional: Jika ingin mengisi data contoh/dummy untuk keperluan pengujian lokal, jalankan `php artisan db:seed --class=DummyDataSeeder`.)*

7. **Jalankan Web Server Lokal:**
   ```bash
   php artisan serve
   ```
   Buka peramban Anda di `http://127.0.0.1:8000`.

---

## 6. Pengujian Otomatis (Automated Testing)

NasHRIS dilengkapi dengan unit dan feature tests komprehensif yang mencakup seluruh alur autentikasi, master data CRUD, otorisasi Anti-IDOR, kalkulasi keterlambatan presensi, persetujuan cuti berjenjang, hingga mesin penggajian dan audit log:

```bash
php artisan test
```

### Hasil Test Suite Terverifikasi:
```
   PASS  Tests\Unit\ExampleTest
  ✓ that true is true

   PASS  Tests\Feature\AuthTest (10 tests)
  ✓ root redirects to login for guests
  ✓ login screen can be rendered without demo buttons
  ✓ demo login route is removed
  ✓ super admin can authenticate using the login screen
  ✓ user cannot authenticate with invalid password
  ✓ login is rate limited after five attempts
  ✓ user with must change password is redirected and can update password
  ✓ authenticated user can view and update profile and password
  ✓ user can logout
  ✓ dashboard requires authentication

   PASS  Tests\Feature\ExampleTest (1 test)
  ✓ the application returns a successful response

   PASS  Tests\Feature\Phase3MasterDataTest (8 tests)
  ✓ hr can view departments index
  ✓ hr can create department
  ✓ hr cannot delete department with employees
  ✓ hr can view and create position
  ✓ hr can view employee directory
  ✓ hr can create employee with auto generated nik and leave balance
  ✓ hr can upload and download document and anti idor protects other employees
  ✓ non hr cannot access hr master data routes

   PASS  Tests\Feature\Phase4AttendanceAndLeaveTest (13 tests)
  ✓ employee can view attendance page
  ✓ employee can clock in on time as present
  ✓ employee clocking in after grace period is marked late with correct minutes
  ✓ employee cannot clock in twice on same day
  ✓ employee can clock out
  ✓ manager can view only team members in their department
  ✓ hr can view all attendances and perform manual correction
  ✓ employee can submit leave request with status pending manager
  ✓ employee cannot submit annual leave exceeding quota
  ✓ manager step 1 approval transitions to pending hr
  ✓ manager from different department cannot review leave request
  ✓ hr step 2 final approval deducts quota and syncs attendance
  ✓ leave rejection workflow does not deduct quota

   PASS  Tests\Feature\Phase5PayrollTest (8 tests)
  ✓ hr can view payrolls index
  ✓ hr can generate monthly payroll draft for all employees
  ✓ payroll calculation accurately computes late and absence deductions
  ✓ hr can finalize payroll from draft to paid
  ✓ employee cannot view or download draft payslip
  ✓ employee can view and download their own paid payslip pdf
  ✓ employee cannot download another employees payslip anti idor
  ✓ non hr cannot generate or finalize payroll

   PASS  Tests\Feature\Phase6AnalyticsAndAuditTest (4 tests)
  ✓ hr can view analytics dashboard with kpi and charts
  ✓ hr can view audit logs index
  ✓ non hr cannot access analytics and audit logs
  ✓ model mutations automatically trigger activity logging

   PASS  Tests\Feature\SecurityAuditTest (12 tests)
  ✓ employee cannot view another employees payslip
  ✓ employee cannot download another employees payslip pdf
  ✓ employee cannot view or download own payslip when payroll is draft
  ✓ owner employee can view and download own paid payslip
  ✓ employee cannot view or download another employees private document
  ✓ manager cannot view private documents of employees
  ✓ owner employee and hr can view and download own document
  ✓ employee cannot view another employees leave attachment
  ✓ owner employee their department manager and hr can view leave attachment
  ✓ manager cannot approve leave of employee from different department
  ✓ manager cannot reject leave of employee from different department
  ✓ manager cannot filter attendance of employee outside department

  Tests:    57 passed (166 assertions)
```

---

## 7. Lisensi

NasHRIS dirilis di bawah lisensi terbuka [MIT License](LICENSE).
