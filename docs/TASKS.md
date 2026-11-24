# Implementation Roadmap & Checklist — NasHRIS

## Phase 1: Project Initialization & Database Foundation
- [x] Install Laravel 13 in root directory (`F:\NASHIR\nashris`) and configure `.env` (`APP_NAME=NasHRIS`, `DB_DATABASE=nashris`, `DB_USERNAME=root`, `DB_PASSWORD=`).
- [x] Install Composer packages (`spatie/laravel-permission`, `spatie/laravel-activitylog`, `barryvdh/laravel-dompdf`, `maatwebsite/excel`) and setup Tailwind CSS + Alpine.js.
- [x] Create all database migrations matching `docs/SCHEMA.md` in proper foreign-key order (`shifts` before `employees`).
- [x] Create Eloquent Models with complete relationships (`belongsTo`, `hasMany`, `hasOne`) and fillable/casts attributes.
- [x] Run `php artisan migrate` and verify all tables exist in MySQL database `nashris`.

## Phase 2: Authentication, RBAC & Realistic Seeder
- [x] Setup Spatie Roles (`hr`, `manager`, `employee`) and middleware protection.
- [x] Build Login page for **NasHRIS** with **One-Click Demo Login** buttons (Login as HR Admin, Login as Manager, Login as Employee).
- [x] Build `DatabaseSeeder` with: 4 Departments, 10 Positions, 2 Shifts, 1 HR Admin, 4 Managers, 30 Employees (Indonesian realistic names), 3 months of attendance logs, leave balances, and sample leave requests.

## Phase 3: Desktop-First Layout & Master Data CRUD
- [x] Build `<x-app-layout>` with responsive sidebar navigation filtered by user role.
- [x] Build Departments & Positions CRUD (HR only).
- [x] Build Employee Directory (Search, Filter by Department/Contract, Create/Edit Employee + Auto-generate `EMP-2026-XXX`, Upload & Secure Preview Documents with Policy).

## Phase 4: Attendance & Multi-Level Leave Workflow
- [x] Build Employee Clock-In / Clock-Out widget with automatic late calculation (`AttendanceService`).
- [x] Build Attendance Recap & Filter table for HR and Managers (Managers only see their department).
- [x] Build Leave Request submission form & Leave Balance tracker for Employees.
- [x] Build 2-Step Approval workflow (`Pending Manager` -> `Pending HR` -> `Approved` with automatic quota deduction and attendance sync).

## Phase 5: Payroll Engine & PDF Payslip
- [x] Build `PayrollService` to calculate monthly salary, allowances, late deductions, and absence deductions inside `DB::transaction()`.
- [x] Build HR Payroll Generator & Status finalization (`Draft` -> `Paid`).
- [x] Build Employee Payslip list and secure PDF download (`barryvdh/laravel-dompdf`) protected by `PayrollPolicy`.

## Phase 6: HR Analytics Dashboard, Audit Log & Portfolio Polish
- [x] Build HR Analytics Dashboard with KPI cards, Chart.js graphs (Department Distribution & 6-Month Attendance Trend), and 30-Day Contract Expiry Alert widget.
- [x] Build Audit Log viewer page using `spatie/laravel-activitylog`.
- [x] Write a comprehensive `README.md` with setup instructions, demo credentials, and Mermaid ERD diagram.