# Database Schema & Business Logic Formulas — NasHRIS

## 1. Tables & Columns

### `users`
- `id` (PK), `name` (string), `email` (unique), `password` (string), `is_active` (boolean, default true), `timestamps`
- Roles managed via Spatie Permission: `hr`, `manager`, `employee`.

### `departments`
- `id` (PK), `name` (string), `code` (string, unique, e.g. `ENG`, `HRD`), `description` (text, nullable), `timestamps`

### `positions`
- `id` (PK), `department_id` (FK -> departments), `title` (string), `base_salary_default` (decimal 15,2), `timestamps`

### `shifts`
- `id` (PK), `name` (string, e.g. `Regular Office`), `start_time` (time, e.g. `08:00:00`), `end_time` (time, e.g. `17:00:00`), `grace_period_minutes` (integer, default 15), `timestamps`

### `employees`
- `id` (PK), `user_id` (FK -> users, cascadeOnDelete), `department_id` (FK -> departments), `position_id` (FK -> positions), `manager_id` (FK -> employees, nullable), `shift_id` (FK -> shifts)
- `employee_code` (string, unique, format: `EMP-2026-001`)
- `phone` (string), `address` (text), `join_date` (date)
- `contract_type` (enum: `PKWTT`, `PKWT`, `Probation`, `Internship`)
- `contract_end_date` (date, nullable — required if contract_type != PKWTT)
- `base_salary` (decimal 15,2), `allowance_fixed` (decimal 15,2, default 0)
- `timestamps`

### `employee_documents`
- `id` (PK), `employee_id` (FK -> employees, cascadeOnDelete), `document_type` (enum: `KTP`, `NPWP`, `CV`, `CONTRACT`), `file_path` (string), `timestamps`

### `attendances`
- `id` (PK), `employee_id` (FK -> employees), `shift_id` (FK -> shifts), `date` (date), `clock_in` (time, nullable), `clock_out` (time, nullable), `late_minutes` (integer, default 0), `status` (enum: `Present`, `Late`, `Absent`, `On Leave`), `ip_address` (string, nullable), `notes` (text, nullable), `timestamps`
- Unique constraint: `['employee_id', 'date']`.

### `leave_balances`
- `id` (PK), `employee_id` (FK -> employees), `year` (year), `total_quota` (integer, default 12), `used_quota` (integer, default 0), `remaining_quota` (integer, default 12), `timestamps`

### `leave_requests`
- `id` (PK), `employee_id` (FK -> employees), `leave_type` (enum: `Annual`, `Sick`, `Special`), `start_date` (date), `end_date` (date), `total_days` (integer), `reason` (text), `attachment_path` (string, nullable)
- `status` (enum: `Pending Manager`, `Pending HR`, `Approved`, `Rejected`, default `Pending Manager`)
- `manager_approved_by` (FK -> users, nullable), `hr_approved_by` (FK -> users, nullable), `rejection_note` (text, nullable), `timestamps`

### `payrolls`
- `id` (PK), `month` (tinyInteger 1-12), `year` (year), `status` (enum: `Draft`, `Paid`, default `Draft`), `processed_by` (FK -> users), `processed_at` (timestamp, nullable), `timestamps`
- Unique constraint: `['month', 'year']`.

### `payroll_items`
- `id` (PK), `payroll_id` (FK -> payrolls, cascadeOnDelete), `employee_id` (FK -> employees)
- `base_salary` (decimal 15,2), `allowances` (decimal 15,2), `late_deduction` (decimal 15,2), `absence_deduction` (decimal 15,2), `net_salary` (decimal 15,2), `timestamps`

---

## 2. Business Calculation Rules
1. **Late Calculation:** If `clock_in` is greater than (`shift.start_time` + `grace_period_minutes`), status = `Late`, and `late_minutes` = difference in minutes between `clock_in` and `shift.start_time`.
2. **Leave Quota Deduction:** Only `Annual` leave deducts `leave_balances.remaining_quota` and increments `used_quota` when status becomes `Approved`.
3. **Payroll Formula (Monthly):**
   - `daily_rate` = `base_salary / 22`
   - `minute_rate` = `daily_rate / 480` (8 working hours)
   - `late_deduction` = `total_late_minutes_in_month * minute_rate`
   - `absence_deduction` = `total_absent_days_in_month * daily_rate`
   - `net_salary` = `(base_salary + allowances) - (late_deduction + absence_deduction)`