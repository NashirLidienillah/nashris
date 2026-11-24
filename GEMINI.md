# AI Agent Rules & Coding Standards — NasHRIS

## 1. Project Context
You are building **NasHRIS**, a desktop-first responsive Enterprise Human Resource Information System (HRIS) web application. Always read `docs/PRD.md`, `docs/SCHEMA.md`, `docs/DESIGN.md`, and `docs/TASKS.md` before writing or modifying code.

## 2. Strict Tech Stack & Environment
- **Application Name:** NasHRIS
- **Framework:** Laravel 13.x (PHP 8.2+)
- **Database:** MySQL 8.0 (Laragon local environment: `DB_DATABASE=nashris`, `DB_USERNAME=root`, `DB_PASSWORD=`)
- **Frontend:** Blade Templates + Tailwind CSS + Alpine.js ONLY. Do NOT install React, Vue, Inertia, or Livewire.
- **Packages:** `spatie/laravel-permission`, `spatie/laravel-activitylog`, `barryvdh/laravel-dompdf`, `maatwebsite/excel`.

## 3. Architecture & Security Rules
- **Authorization (Anti-IDOR):** Always use Laravel Policies and Middleware (`role:hr|manager|employee`). Never allow an employee to view or download another employee's payslip or private documents via URL manipulation.
- **Validation:** Never validate inside Controllers. Always create dedicated `FormRequest` classes (`app/Http/Requests/...`).
- **Data Integrity:** Wrap multi-table mutations (Payroll generation, Leave approval + quota deduction, Employee creation + User account creation) inside `DB::transaction()`.
- **Skinny Controllers:** Extract complex business logic (payroll calculations, late deduction formulas, leave day counting) into Service classes (`app/Services/...`).

## 4. Workflow Discipline
- Work sequentially based on `docs/TASKS.md`.
- After completing a task or phase, mark the checkbox `- [x]` in `docs/TASKS.md`.
- Write clean, modular Blade components (`resources/views/components/...`) to avoid repetitive HTML.

## 5. Folder & File Organization Standards (Phase 3 — Phase 6)
1. **Controllers (`app/Http/Controllers/`):**
   - Pisahkan berdasarkan role menjadi subfolder:
     - `HR/`: Seluruh controller untuk modul HR (Dashboard, Department, Position, Employee, Attendance, Leave, Payroll, Analytics).
     - `Manager/`: Seluruh controller untuk peran Manager (Dashboard, Attendance, LeaveApproval).
     - `Employee/`: Seluruh controller untuk portal ESS Karyawan (Dashboard, Attendance, Leave, Payslip, Profile).
     - `Shared/`: Controller umum untuk fitur bersama (Profile, Notification).
     - `Auth/`: Controller autentikasi (Login, Logout, Demo Access).
2. **Form Requests (`app/Http/Requests/`):**
   - Pisahkan validasi berdasarkan role ke dalam subfolder `HR/`, `Manager/`, dan `Employee/` (serta `Auth/` untuk autentikasi). Dilarang keras melakukan validasi di dalam controller.
3. **Services & Policies (`app/Services/` & `app/Policies/`):**
   - Simpan seluruh logika kalkulasi bisnis, rumus, dan mutasi data transaksi kompleks di `app/Services/` (seperti `EmployeeService`, `AttendanceService`, `LeaveService`, `PayrollService`).
   - Simpan aturan otorisasi Anti-IDOR di `app/Policies/` (seperti `EmployeeDocumentPolicy`, `LeaveRequestPolicy`, `PayrollPolicy`).
4. **Views (`resources/views/`):**
   - Pisahkan halaman berdasarkan role ke dalam folder `hr/`, `manager/`, `employee/`.
   - `components/`: Komponen Blade reusable (`<x-app-layout>`, `<x-stat-card>`, `<x-status-badge>`, `<x-data-table>`, `<x-modal>`, `<x-empty-state>`).
   - `layouts/`: Master layout (`app.blade.php`, `guest.blade.php`).
   - `pdf/`: Template dokumen PDF untuk DomPDF (seperti template slip gaji).
5. **Modular Routes (`routes/`):**
   - Pecah rute di `routes/web.php` menjadi file modular terpisah yang di-load dengan prefix, name, dan middleware masing-masing:
     - `routes/hr.php`: prefix `/hr`, name prefix `hr.`, middleware `['auth', 'role:hr']`.
     - `routes/manager.php`: prefix `/manager`, name prefix `manager.`, middleware `['auth', 'role:manager']`.
     - `routes/employee.php`: prefix `/employee`, name prefix `employee.`, middleware `['auth', 'role:employee']`.
