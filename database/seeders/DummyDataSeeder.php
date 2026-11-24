<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Department;
use App\Models\Employee;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\Position;
use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DummyDataSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // 1. Roles
            $hrRole = Role::firstOrCreate(['name' => 'hr']);
            $managerRole = Role::firstOrCreate(['name' => 'manager']);
            $employeeRole = Role::firstOrCreate(['name' => 'employee']);

            // 2. Shifts
            $regularShift = Shift::create([
                'name' => 'Regular Office',
                'start_time' => '08:00:00',
                'end_time' => '17:00:00',
                'grace_period_minutes' => 15,
            ]);

            $morningShift = Shift::create([
                'name' => 'Shift Pagi',
                'start_time' => '07:00:00',
                'end_time' => '15:00:00',
                'grace_period_minutes' => 15,
            ]);

            // 3. Departments & Positions
            $deptEngineering = Department::firstOrCreate(
                ['code' => 'ENG'],
                [
                    'name' => 'IT Engineering',
                    'description' => 'Departemen Rekayasa Perangkat Lunak, Arsitektur Sistem, dan Infrastruktur IT.',
                ]
            );

            $deptHR = Department::firstOrCreate(
                ['code' => 'HRD'],
                [
                    'name' => 'Human Resources',
                    'description' => 'Departemen Manajemen Talenta, Kompensasi, dan Operasional Kepegawaian.',
                ]
            );

            $deptFinance = Department::firstOrCreate(
                ['code' => 'FIN'],
                [
                    'name' => 'Finance & Accounting',
                    'description' => 'Departemen Keuangan Perusahaan, Akuntansi, Perpajakan, dan Penggajian.',
                ]
            );

            $deptMarketing = Department::firstOrCreate(
                ['code' => 'MKT'],
                [
                    'name' => 'Marketing & Sales',
                    'description' => 'Departemen Pemasaran Digital, Komunikasi Korporat, dan Penjualan.',
                ]
            );

            // 10 Positions
            $posEngLead = Position::firstOrCreate(
                ['department_id' => $deptEngineering->id, 'title' => 'Senior Software Engineer'],
                ['base_salary_default' => 16000000]
            );
            $posEngDev = Position::firstOrCreate(
                ['department_id' => $deptEngineering->id, 'title' => 'Fullstack Developer'],
                ['base_salary_default' => 11500000]
            );
            $posEngQA = Position::firstOrCreate(
                ['department_id' => $deptEngineering->id, 'title' => 'QA Engineer'],
                ['base_salary_default' => 9000000]
            );

            $posHRHead = Position::firstOrCreate(
                ['department_id' => $deptHR->id, 'title' => 'HR Manager'],
                ['base_salary_default' => 16000000]
            );
            $posHRSpec = Position::firstOrCreate(
                ['department_id' => $deptHR->id, 'title' => 'HR Specialist'],
                ['base_salary_default' => 8500000]
            );
            $posHRRec = Position::firstOrCreate(
                ['department_id' => $deptHR->id, 'title' => 'Talent Acquisition'],
                ['base_salary_default' => 8000000]
            );

            $posFinHead = Position::firstOrCreate(
                ['department_id' => $deptFinance->id, 'title' => 'Finance Manager'],
                ['base_salary_default' => 16000000]
            );
            $posFinSr = Position::firstOrCreate(
                ['department_id' => $deptFinance->id, 'title' => 'Senior Accountant'],
                ['base_salary_default' => 11000000]
            );
            $posFinJr = Position::firstOrCreate(
                ['department_id' => $deptFinance->id, 'title' => 'Junior Accountant'],
                ['base_salary_default' => 7500000]
            );

            $posMktHead = Position::firstOrCreate(
                ['department_id' => $deptMarketing->id, 'title' => 'Marketing Manager'],
                ['base_salary_default' => 15500000]
            );
            $posMktSpec = Position::firstOrCreate(
                ['department_id' => $deptMarketing->id, 'title' => 'Digital Marketing Specialist'],
                ['base_salary_default' => 9000000]
            );

            // Helper to get unique employee code without collision
            $getUniqueCode = function (string $preferred) {
                if (! Employee::where('employee_code', $preferred)->exists()) {
                    return $preferred;
                }
                $idx = 100;
                while (Employee::where('employee_code', sprintf('EMP-2026-%03d', $idx))->exists()) {
                    $idx++;
                }
                return sprintf('EMP-2026-%03d', $idx);
            };

            // 4. Create 1 HR Admin
            $hrUser = User::firstOrCreate(
                ['email' => 'hr@nashris.test'],
                [
                    'name' => 'HR Administrator',
                    'password' => Hash::make('password'),
                    'is_active' => true,
                    'must_change_password' => false,
                ]
            );
            $hrUser->syncRoles([$hrRole]);

            $hrEmployee = Employee::where('user_id', $hrUser->id)->first() ?? Employee::create([
                'user_id' => $hrUser->id,
                'department_id' => $deptHR->id,
                'position_id' => $posHRHead->id,
                'manager_id' => null,
                'shift_id' => $regularShift->id,
                'employee_code' => $getUniqueCode('EMP-2026-001'),
                'phone' => '081234567801',
                'address' => 'Jl. Jenderal Sudirman No. 12, Senayan, Jakarta Selatan',
                'join_date' => '2023-01-10',
                'contract_type' => 'PKWTT',
                'contract_end_date' => null,
                'base_salary' => 16000000,
                'allowance_fixed' => 2500000,
            ]);

            // 5. Create 4 Department Managers
            // Manager 1: IT Engineering
            $itManagerUser = User::firstOrCreate(
                ['email' => 'manager.it@nashris.test'],
                [
                    'name' => 'Bambang Wijaya',
                    'password' => Hash::make('password'),
                    'is_active' => true,
                    'must_change_password' => false,
                ]
            );
            $itManagerUser->syncRoles([$managerRole]);

            $itManagerEmp = Employee::where('user_id', $itManagerUser->id)->first() ?? Employee::create([
                'user_id' => $itManagerUser->id,
                'department_id' => $deptEngineering->id,
                'position_id' => $posEngLead->id,
                'manager_id' => null,
                'shift_id' => $regularShift->id,
                'employee_code' => $getUniqueCode('EMP-2026-002'),
                'phone' => '081234567802',
                'address' => 'Jl. Gatot Subroto No. 45, Tebet, Jakarta Selatan',
                'join_date' => '2023-02-01',
                'contract_type' => 'PKWTT',
                'contract_end_date' => null,
                'base_salary' => 16000000,
                'allowance_fixed' => 2500000,
            ]);

            // Manager 2: HR Manager
            $hrManagerUser = User::firstOrCreate(
                ['email' => 'manager.hr@nashris.test'],
                [
                    'name' => 'Siti Aminah',
                    'password' => Hash::make('password'),
                    'is_active' => true,
                    'must_change_password' => false,
                ]
            );
            $hrManagerUser->syncRoles([$managerRole]);

            $hrManagerEmp = Employee::where('user_id', $hrManagerUser->id)->first() ?? Employee::create([
                'user_id' => $hrManagerUser->id,
                'department_id' => $deptHR->id,
                'position_id' => $posHRSpec->id,
                'manager_id' => $hrEmployee->id,
                'shift_id' => $regularShift->id,
                'employee_code' => $getUniqueCode('EMP-2026-003'),
                'phone' => '081234567803',
                'address' => 'Jl. HR Rasuna Said Kav. 8, Setiabudi, Jakarta Selatan',
                'join_date' => '2023-03-15',
                'contract_type' => 'PKWTT',
                'contract_end_date' => null,
                'base_salary' => 14000000,
                'allowance_fixed' => 2000000,
            ]);

            // Manager 3: Finance Manager
            $finManagerUser = User::firstOrCreate(
                ['email' => 'manager.fin@nashris.test'],
                [
                    'name' => 'Hendro Santoso',
                    'password' => Hash::make('password'),
                    'is_active' => true,
                    'must_change_password' => false,
                ]
            );
            $finManagerUser->syncRoles([$managerRole]);

            $finManagerEmp = Employee::where('user_id', $finManagerUser->id)->first() ?? Employee::create([
                'user_id' => $finManagerUser->id,
                'department_id' => $deptFinance->id,
                'position_id' => $posFinHead->id,
                'manager_id' => null,
                'shift_id' => $regularShift->id,
                'employee_code' => $getUniqueCode('EMP-2026-004'),
                'phone' => '081234567804',
                'address' => 'Jl. MH Thamrin No. 9, Menteng, Jakarta Pusat',
                'join_date' => '2023-01-20',
                'contract_type' => 'PKWTT',
                'contract_end_date' => null,
                'base_salary' => 16000000,
                'allowance_fixed' => 2500000,
            ]);

            // Manager 4: Marketing Manager
            $mktManagerUser = User::firstOrCreate(
                ['email' => 'manager.mkt@nashris.test'],
                [
                    'name' => 'Dewi Lestari',
                    'password' => Hash::make('password'),
                    'is_active' => true,
                    'must_change_password' => false,
                ]
            );
            $mktManagerUser->syncRoles([$managerRole]);

            $mktManagerEmp = Employee::where('user_id', $mktManagerUser->id)->first() ?? Employee::create([
                'user_id' => $mktManagerUser->id,
                'department_id' => $deptMarketing->id,
                'position_id' => $posMktHead->id,
                'manager_id' => null,
                'shift_id' => $regularShift->id,
                'employee_code' => $getUniqueCode('EMP-2026-005'),
                'phone' => '081234567805',
                'address' => 'Jl. Kemang Raya No. 17, Bangka, Jakarta Selatan',
                'join_date' => '2023-04-01',
                'contract_type' => 'PKWTT',
                'contract_end_date' => null,
                'base_salary' => 15500000,
                'allowance_fixed' => 2200000,
            ]);

            // 6. 30 Realistic Employees
            $employeeData = [
                // IT Engineering (10 staff)
                [
                    'name' => 'Budi Santoso',
                    'email' => 'budi.santoso@nashris.test',
                    'dept' => $deptEngineering,
                    'pos' => $posEngDev,
                    'manager' => $itManagerEmp,
                    'shift' => $regularShift,
                    'contract' => 'PKWTT',
                    'end_date' => null,
                    'salary' => 11500000,
                    'allowance' => 1500000,
                    'join' => '2023-05-10',
                ],
                [
                    'name' => 'Ahmad Fauzi',
                    'email' => 'ahmad.fauzi@nashris.test',
                    'dept' => $deptEngineering,
                    'pos' => $posEngDev,
                    'manager' => $itManagerEmp,
                    'shift' => $regularShift,
                    'contract' => 'PKWTT',
                    'end_date' => null,
                    'salary' => 11000000,
                    'allowance' => 1500000,
                    'join' => '2023-06-01',
                ],
                [
                    'name' => 'Rizky Pratama',
                    'email' => 'rizky.pratama@nashris.test',
                    'dept' => $deptEngineering,
                    'pos' => $posEngDev,
                    'manager' => $itManagerEmp,
                    'shift' => $morningShift,
                    'contract' => 'PKWT',
                    'end_date' => '2026-12-10', // Expiry in < 30 days!
                    'salary' => 10500000,
                    'allowance' => 1200000,
                    'join' => '2024-01-15',
                ],
                [
                    'name' => 'Putri Anggraini',
                    'email' => 'putri.anggraini@nashris.test',
                    'dept' => $deptEngineering,
                    'pos' => $posEngQA,
                    'manager' => $itManagerEmp,
                    'shift' => $regularShift,
                    'contract' => 'PKWTT',
                    'end_date' => null,
                    'salary' => 9500000,
                    'allowance' => 1000000,
                    'join' => '2023-08-01',
                ],
                [
                    'name' => 'Fajar Ramadhan',
                    'email' => 'fajar.ramadhan@nashris.test',
                    'dept' => $deptEngineering,
                    'pos' => $posEngQA,
                    'manager' => $itManagerEmp,
                    'shift' => $morningShift,
                    'contract' => 'Probation',
                    'end_date' => '2026-12-18', // Expiry in < 30 days!
                    'salary' => 8500000,
                    'allowance' => 800000,
                    'join' => '2026-09-18',
                ],
                [
                    'name' => 'Eko Prasetyo',
                    'email' => 'eko.prasetyo@nashris.test',
                    'dept' => $deptEngineering,
                    'pos' => $posEngDev,
                    'manager' => $itManagerEmp,
                    'shift' => $regularShift,
                    'contract' => 'PKWTT',
                    'end_date' => null,
                    'salary' => 12000000,
                    'allowance' => 1500000,
                    'join' => '2023-03-20',
                ],
                [
                    'name' => 'Dian Permata',
                    'email' => 'dian.permata@nashris.test',
                    'dept' => $deptEngineering,
                    'pos' => $posEngDev,
                    'manager' => $itManagerEmp,
                    'shift' => $regularShift,
                    'contract' => 'PKWTT',
                    'end_date' => null,
                    'salary' => 11000000,
                    'allowance' => 1200000,
                    'join' => '2023-07-15',
                ],
                [
                    'name' => 'Gita Gutawa',
                    'email' => 'gita.gutawa@nashris.test',
                    'dept' => $deptEngineering,
                    'pos' => $posEngQA,
                    'manager' => $itManagerEmp,
                    'shift' => $regularShift,
                    'contract' => 'PKWT',
                    'end_date' => '2027-03-31',
                    'salary' => 9000000,
                    'allowance' => 1000000,
                    'join' => '2024-04-01',
                ],
                [
                    'name' => 'Hadi Saputra',
                    'email' => 'hadi.saputra@nashris.test',
                    'dept' => $deptEngineering,
                    'pos' => $posEngDev,
                    'manager' => $itManagerEmp,
                    'shift' => $morningShift,
                    'contract' => 'PKWTT',
                    'end_date' => null,
                    'salary' => 11500000,
                    'allowance' => 1500000,
                    'join' => '2023-09-01',
                ],
                [
                    'name' => 'Bayu Nugroho',
                    'email' => 'bayu.nugroho@nashris.test',
                    'dept' => $deptEngineering,
                    'pos' => $posEngDev,
                    'manager' => $itManagerEmp,
                    'shift' => $regularShift,
                    'contract' => 'Internship',
                    'end_date' => '2027-01-31',
                    'salary' => 4500000,
                    'allowance' => 500000,
                    'join' => '2026-08-01',
                ],

                // Human Resources (6 staff)
                [
                    'name' => 'Rina Handayani',
                    'email' => 'rina.handayani@nashris.test',
                    'dept' => $deptHR,
                    'pos' => $posHRSpec,
                    'manager' => $hrManagerEmp,
                    'shift' => $regularShift,
                    'contract' => 'PKWTT',
                    'end_date' => null,
                    'salary' => 8500000,
                    'allowance' => 1000000,
                    'join' => '2023-06-15',
                ],
                [
                    'name' => 'Indah Kusuma',
                    'email' => 'indah.kusuma@nashris.test',
                    'dept' => $deptHR,
                    'pos' => $posHRRec,
                    'manager' => $hrManagerEmp,
                    'shift' => $regularShift,
                    'contract' => 'PKWTT',
                    'end_date' => null,
                    'salary' => 8000000,
                    'allowance' => 1000000,
                    'join' => '2023-07-01',
                ],
                [
                    'name' => 'Joko Widodo',
                    'email' => 'joko.widodo@nashris.test',
                    'dept' => $deptHR,
                    'pos' => $posHRSpec,
                    'manager' => $hrManagerEmp,
                    'shift' => $regularShift,
                    'contract' => 'PKWT',
                    'end_date' => '2026-12-22', // Expiry in < 30 days!
                    'salary' => 8500000,
                    'allowance' => 1000000,
                    'join' => '2024-01-01',
                ],
                [
                    'name' => 'Kartika Sari',
                    'email' => 'kartika.sari@nashris.test',
                    'dept' => $deptHR,
                    'pos' => $posHRRec,
                    'manager' => $hrManagerEmp,
                    'shift' => $regularShift,
                    'contract' => 'PKWTT',
                    'end_date' => null,
                    'salary' => 8000000,
                    'allowance' => 800000,
                    'join' => '2023-10-01',
                ],
                [
                    'name' => 'Lestari Wulandari',
                    'email' => 'lestari.wulandari@nashris.test',
                    'dept' => $deptHR,
                    'pos' => $posHRSpec,
                    'manager' => $hrManagerEmp,
                    'shift' => $regularShift,
                    'contract' => 'PKWTT',
                    'end_date' => null,
                    'salary' => 8500000,
                    'allowance' => 1000000,
                    'join' => '2023-11-15',
                ],
                [
                    'name' => 'Muhammad Iqbal',
                    'email' => 'muhammad.iqbal@nashris.test',
                    'dept' => $deptHR,
                    'pos' => $posHRRec,
                    'manager' => $hrManagerEmp,
                    'shift' => $regularShift,
                    'contract' => 'Internship',
                    'end_date' => '2027-02-28',
                    'salary' => 4500000,
                    'allowance' => 500000,
                    'join' => '2026-09-01',
                ],

                // Finance & Accounting (7 staff)
                [
                    'name' => 'Nurul Hidayah',
                    'email' => 'nurul.hidayah@nashris.test',
                    'dept' => $deptFinance,
                    'pos' => $posFinSr,
                    'manager' => $finManagerEmp,
                    'shift' => $regularShift,
                    'contract' => 'PKWTT',
                    'end_date' => null,
                    'salary' => 11000000,
                    'allowance' => 1500000,
                    'join' => '2023-04-10',
                ],
                [
                    'name' => 'Oki Setiawan',
                    'email' => 'oki.setiawan@nashris.test',
                    'dept' => $deptFinance,
                    'pos' => $posFinJr,
                    'manager' => $finManagerEmp,
                    'shift' => $regularShift,
                    'contract' => 'PKWTT',
                    'end_date' => null,
                    'salary' => 7500000,
                    'allowance' => 1000000,
                    'join' => '2023-08-15',
                ],
                [
                    'name' => 'Pranata Kusuma',
                    'email' => 'pranata.kusuma@nashris.test',
                    'dept' => $deptFinance,
                    'pos' => $posFinSr,
                    'manager' => $finManagerEmp,
                    'shift' => $regularShift,
                    'contract' => 'PKWT',
                    'end_date' => '2027-04-30',
                    'salary' => 11000000,
                    'allowance' => 1200000,
                    'join' => '2024-05-01',
                ],
                [
                    'name' => 'Qoriatul Aini',
                    'email' => 'qoriatul.aini@nashris.test',
                    'dept' => $deptFinance,
                    'pos' => $posFinJr,
                    'manager' => $finManagerEmp,
                    'shift' => $regularShift,
                    'contract' => 'Probation',
                    'end_date' => '2026-12-14', // Expiry in < 30 days!
                    'salary' => 7200000,
                    'allowance' => 800000,
                    'join' => '2026-09-14',
                ],
                [
                    'name' => 'Rian Ardiansyah',
                    'email' => 'rian.ardiansyah@nashris.test',
                    'dept' => $deptFinance,
                    'pos' => $posFinJr,
                    'manager' => $finManagerEmp,
                    'shift' => $regularShift,
                    'contract' => 'PKWTT',
                    'end_date' => null,
                    'salary' => 7500000,
                    'allowance' => 1000000,
                    'join' => '2024-02-01',
                ],
                [
                    'name' => 'Surya Darmawan',
                    'email' => 'surya.darmawan@nashris.test',
                    'dept' => $deptFinance,
                    'pos' => $posFinSr,
                    'manager' => $finManagerEmp,
                    'shift' => $regularShift,
                    'contract' => 'PKWTT',
                    'end_date' => null,
                    'salary' => 11500000,
                    'allowance' => 1500000,
                    'join' => '2023-05-20',
                ],
                [
                    'name' => 'Cindy Fatikasari',
                    'email' => 'cindy.fatikasari@nashris.test',
                    'dept' => $deptFinance,
                    'pos' => $posFinJr,
                    'manager' => $finManagerEmp,
                    'shift' => $regularShift,
                    'contract' => 'PKWT',
                    'end_date' => '2027-06-30',
                    'salary' => 7500000,
                    'allowance' => 900000,
                    'join' => '2024-07-01',
                ],

                // Marketing & Sales (7 staff)
                [
                    'name' => 'Taufik Hidayat',
                    'email' => 'taufik.hidayat@nashris.test',
                    'dept' => $deptMarketing,
                    'pos' => $posMktSpec,
                    'manager' => $mktManagerEmp,
                    'shift' => $regularShift,
                    'contract' => 'PKWTT',
                    'end_date' => null,
                    'salary' => 9000000,
                    'allowance' => 1200000,
                    'join' => '2023-07-10',
                ],
                [
                    'name' => 'Utami Ningsih',
                    'email' => 'utami.ningsih@nashris.test',
                    'dept' => $deptMarketing,
                    'pos' => $posMktSpec,
                    'manager' => $mktManagerEmp,
                    'shift' => $regularShift,
                    'contract' => 'PKWTT',
                    'end_date' => null,
                    'salary' => 9200000,
                    'allowance' => 1200000,
                    'join' => '2023-08-01',
                ],
                [
                    'name' => 'Vina Panduwinata',
                    'email' => 'vina.panduwinata@nashris.test',
                    'dept' => $deptMarketing,
                    'pos' => $posMktSpec,
                    'manager' => $mktManagerEmp,
                    'shift' => $morningShift,
                    'contract' => 'PKWT',
                    'end_date' => '2026-12-08', // Expiry in < 30 days!
                    'salary' => 8800000,
                    'allowance' => 1000000,
                    'join' => '2024-01-10',
                ],
                [
                    'name' => 'Wahyu Hidayat',
                    'email' => 'wahyu.hidayat@nashris.test',
                    'dept' => $deptMarketing,
                    'pos' => $posMktSpec,
                    'manager' => $mktManagerEmp,
                    'shift' => $regularShift,
                    'contract' => 'PKWTT',
                    'end_date' => null,
                    'salary' => 9000000,
                    'allowance' => 1200000,
                    'join' => '2023-11-01',
                ],
                [
                    'name' => 'Xaverius Bagus',
                    'email' => 'xaverius.bagus@nashris.test',
                    'dept' => $deptMarketing,
                    'pos' => $posMktSpec,
                    'manager' => $mktManagerEmp,
                    'shift' => $regularShift,
                    'contract' => 'PKWTT',
                    'end_date' => null,
                    'salary' => 9500000,
                    'allowance' => 1300000,
                    'join' => '2023-05-15',
                ],
                [
                    'name' => 'Yuliana Safitri',
                    'email' => 'yuliana.safitri@nashris.test',
                    'dept' => $deptMarketing,
                    'pos' => $posMktSpec,
                    'manager' => $mktManagerEmp,
                    'shift' => $regularShift,
                    'contract' => 'PKWTT',
                    'end_date' => null,
                    'salary' => 9000000,
                    'allowance' => 1100000,
                    'join' => '2024-03-01',
                ],
                [
                    'name' => 'Zainal Abidin',
                    'email' => 'zainal.abidin@nashris.test',
                    'dept' => $deptMarketing,
                    'pos' => $posMktSpec,
                    'manager' => $mktManagerEmp,
                    'shift' => $regularShift,
                    'contract' => 'PKWT',
                    'end_date' => '2027-05-31',
                    'salary' => 9000000,
                    'allowance' => 1100000,
                    'join' => '2024-06-01',
                ],
            ];

            $allEmployees = collect([$hrEmployee, $itManagerEmp, $hrManagerEmp, $finManagerEmp, $mktManagerEmp]);

            $codeIndex = 6;
            foreach ($employeeData as $data) {
                $user = User::firstOrCreate(
                    ['email' => $data['email']],
                    [
                        'name' => $data['name'],
                        'password' => Hash::make('password'),
                        'is_active' => true,
                        'must_change_password' => false,
                    ]
                );
                $user->syncRoles([$employeeRole]);

                $preferredCode = sprintf('EMP-2026-%03d', $codeIndex++);
                $emp = Employee::where('user_id', $user->id)->first() ?? Employee::create([
                    'user_id' => $user->id,
                    'department_id' => $data['dept']->id,
                    'position_id' => $data['pos']->id,
                    'manager_id' => $data['manager']->id,
                    'shift_id' => $data['shift']->id,
                    'employee_code' => $getUniqueCode($preferredCode),
                    'phone' => sprintf('0812345678%02d', $codeIndex),
                    'address' => 'Jl. Kebon Jeruk Raya No. ' . ($codeIndex * 3) . ', Jakarta Barat',
                    'join_date' => $data['join'],
                    'contract_type' => $data['contract'],
                    'contract_end_date' => $data['end_date'],
                    'base_salary' => $data['salary'],
                    'allowance_fixed' => $data['allowance'],
                ]);

                $allEmployees->push($emp);
            }

            // 7. Leave Balances for 2026
            foreach ($allEmployees as $emp) {
                LeaveBalance::firstOrCreate(
                    ['employee_id' => $emp->id, 'year' => 2026],
                    [
                        'total_quota' => 12,
                        'used_quota' => 0,
                        'remaining_quota' => 12,
                    ]
                );
            }

            // 8. Sample Leave Requests
            $approvedLeaveDates = []; // [employee_id => ['2026-10-12', '2026-10-13']]

            $budiEmp = $allEmployees->firstWhere('employee_code', 'EMP-2026-006'); // Budi Santoso
            if ($budiEmp) {
                // Approved Annual Leave
                $leave1 = LeaveRequest::create([
                    'employee_id' => $budiEmp->id,
                    'leave_type' => 'Annual',
                    'start_date' => '2026-10-12',
                    'end_date' => '2026-10-13',
                    'total_days' => 2,
                    'reason' => 'Keperluan keluarga dan acara pernikahan adik di luar kota.',
                    'status' => 'Approved',
                    'manager_approved_by' => $itManagerUser->id,
                    'hr_approved_by' => $hrUser->id,
                    'rejection_note' => null,
                ]);

                // Update quota
                $lb = LeaveBalance::where('employee_id', $budiEmp->id)->where('year', 2026)->first();
                if ($lb) {
                    $lb->increment('used_quota', 2);
                    $lb->decrement('remaining_quota', 2);
                }
                $approvedLeaveDates[$budiEmp->id] = ['2026-10-12', '2026-10-13'];

                // Pending Manager Leave
                LeaveRequest::create([
                    'employee_id' => $budiEmp->id,
                    'leave_type' => 'Annual',
                    'start_date' => '2026-12-24',
                    'end_date' => '2026-12-25',
                    'total_days' => 2,
                    'reason' => 'Cuti akhir tahun bersama keluarga.',
                    'status' => 'Pending Manager',
                    'manager_approved_by' => null,
                    'hr_approved_by' => null,
                    'rejection_note' => null,
                ]);
            }

            // More sample leaves across departments
            $putriEmp = $allEmployees->firstWhere('employee_code', 'EMP-2026-009'); // Putri Anggraini
            if ($putriEmp) {
                LeaveRequest::create([
                    'employee_id' => $putriEmp->id,
                    'leave_type' => 'Sick',
                    'start_date' => '2026-11-05',
                    'end_date' => '2026-11-06',
                    'total_days' => 2,
                    'reason' => 'Demam tinggi dan istirahat dokter (surat terlampir).',
                    'status' => 'Approved',
                    'manager_approved_by' => $itManagerUser->id,
                    'hr_approved_by' => $hrUser->id,
                ]);
                $approvedLeaveDates[$putriEmp->id] = ['2026-11-05', '2026-11-06'];
            }

            $rinaEmp = $allEmployees->firstWhere('employee_code', 'EMP-2026-016'); // Rina Handayani
            if ($rinaEmp) {
                LeaveRequest::create([
                    'employee_id' => $rinaEmp->id,
                    'leave_type' => 'Annual',
                    'start_date' => '2026-11-26',
                    'end_date' => '2026-11-27',
                    'total_days' => 2,
                    'reason' => 'Cuti tahunan.',
                    'status' => 'Pending HR',
                    'manager_approved_by' => $hrManagerUser->id,
                    'hr_approved_by' => null,
                ]);
            }

            $okiEmp = $allEmployees->firstWhere('employee_code', 'EMP-2026-023'); // Oki Setiawan
            if ($okiEmp) {
                LeaveRequest::create([
                    'employee_id' => $okiEmp->id,
                    'leave_type' => 'Special',
                    'start_date' => '2026-11-10',
                    'end_date' => '2026-11-10',
                    'total_days' => 1,
                    'reason' => 'Izin keperluan mendesak.',
                    'status' => 'Rejected',
                    'manager_approved_by' => null,
                    'hr_approved_by' => null,
                    'rejection_note' => 'Jadwal audit keuangan bulan November sedang berlangsung, mohon atur ulang tanggal.',
                ]);
            }

            // 9. 3 Months of Attendance Logs (Sep 1, 2026 to Nov 23, 2026)
            $startDate = Carbon::create(2026, 9, 1);
            $endDate = Carbon::create(2026, 11, 23);
            $period = CarbonPeriod::create($startDate, $endDate);

            $attendanceRows = [];
            $now = now();

            foreach ($period as $date) {
                // Skip weekends (Saturday & Sunday)
                if ($date->isWeekend()) {
                    continue;
                }

                $dateString = $date->format('Y-m-d');

                foreach ($allEmployees as $emp) {
                    $shift = $emp->shift;
                    $isApprovedLeave = isset($approvedLeaveDates[$emp->id]) && in_array($dateString, $approvedLeaveDates[$emp->id]);

                    if ($isApprovedLeave) {
                        $attendanceRows[] = [
                            'employee_id' => $emp->id,
                            'shift_id' => $shift->id,
                            'date' => $dateString,
                            'clock_in' => null,
                            'clock_out' => null,
                            'late_minutes' => 0,
                            'status' => 'On Leave',
                            'ip_address' => '127.0.0.1',
                            'notes' => 'Izin cuti disetujui.',
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                        continue;
                    }

                    // Deterministic pseudo-random generation based on employee id & date
                    $seed = crc32($emp->id . '-' . $dateString);
                    $rand = abs($seed % 100);

                    if ($rand < 85) {
                        // Present (On Time)
                        // Clock in between (start_time - 25 mins) and (start_time + 10 mins)
                        $startHour = (int) substr($shift->start_time, 0, 2);
                        $minuteOffset = ($seed % 35) - 25; // -25 to +9 minutes
                        $inTime = Carbon::createFromTime($startHour, 0, 0)->addMinutes($minuteOffset);
                        $outTime = Carbon::createFromTime((int) substr($shift->end_time, 0, 2), 0, 0)->addMinutes(abs($seed % 30));

                        $attendanceRows[] = [
                            'employee_id' => $emp->id,
                            'shift_id' => $shift->id,
                            'date' => $dateString,
                            'clock_in' => $inTime->format('H:i:s'),
                            'clock_out' => $outTime->format('H:i:s'),
                            'late_minutes' => 0,
                            'status' => 'Present',
                            'ip_address' => '192.168.1.' . (abs($seed % 200) + 10),
                            'notes' => null,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    } elseif ($rand < 95) {
                        // Late (between 16 and 45 minutes late)
                        $startHour = (int) substr($shift->start_time, 0, 2);
                        $lateMins = 16 + (abs($seed % 30)); // 16 to 45 mins
                        $inTime = Carbon::createFromTime($startHour, 0, 0)->addMinutes($lateMins);
                        $outTime = Carbon::createFromTime((int) substr($shift->end_time, 0, 2), 0, 0)->addMinutes(abs($seed % 40));

                        $attendanceRows[] = [
                            'employee_id' => $emp->id,
                            'shift_id' => $shift->id,
                            'date' => $dateString,
                            'clock_in' => $inTime->format('H:i:s'),
                            'clock_out' => $outTime->format('H:i:s'),
                            'late_minutes' => $lateMins,
                            'status' => 'Late',
                            'ip_address' => '192.168.1.' . (abs($seed % 200) + 10),
                            'notes' => 'Terlambat ' . $lateMins . ' menit (kendala lalu lintas).',
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    } else {
                        // Absent (Tanpa keterangan)
                        $attendanceRows[] = [
                            'employee_id' => $emp->id,
                            'shift_id' => $shift->id,
                            'date' => $dateString,
                            'clock_in' => null,
                            'clock_out' => null,
                            'late_minutes' => 0,
                            'status' => 'Absent',
                            'ip_address' => null,
                            'notes' => 'Tidak hadir tanpa keterangan.',
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }
            }

            // Chunk insert for optimal MySQL performance
            foreach (array_chunk($attendanceRows, 500) as $chunk) {
                Attendance::insertOrIgnore($chunk);
            }
        });
    }
}
