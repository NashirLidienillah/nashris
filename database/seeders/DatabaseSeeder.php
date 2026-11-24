<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Employee;
use App\Models\LeaveBalance;
use App\Models\Position;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's production baseline data.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // 1. Spatie Roles
            $hrRole = Role::firstOrCreate(['name' => 'hr']);
            Role::firstOrCreate(['name' => 'manager']);
            Role::firstOrCreate(['name' => 'employee']);

            // 2. Default Work Shift
            Shift::firstOrCreate(
                ['name' => 'Regular Office'],
                [
                    'start_time' => '08:00:00',
                    'end_time' => '17:00:00',
                    'grace_period_minutes' => 15,
                ]
            );

            // 3. Super Admin HR Account
            $adminUser = User::firstOrCreate(
                ['email' => 'admin@nashris.com'],
                [
                    'name' => 'Super Admin HR',
                    'password' => Hash::make('password123'),
                    'is_active' => true,
                    'must_change_password' => false,
                ]
            );

            if (! $adminUser->hasRole($hrRole)) {
                $adminUser->assignRole($hrRole);
            }

            // 4. Enterprise Corporate Departments & Positions Structure
            $departmentsData = [
                [
                    'name' => 'Human Resources & General Affairs',
                    'code' => 'HRD',
                    'description' => 'Manajemen talenta, kompensasi & benefit, hubungan industrial, pelatihan karyawan, dan operasional umum kantor.',
                    'positions' => [
                        ['title' => 'VP of Human Resources', 'salary' => 25000000],
                        ['title' => 'HR Manager', 'salary' => 18000000],
                        ['title' => 'Talent Acquisition Specialist', 'salary' => 11000000],
                        ['title' => 'Compensation & Benefits Specialist', 'salary' => 10500000],
                        ['title' => 'People & Organization Development Specialist', 'salary' => 9500000],
                        ['title' => 'General Affairs & Facilities Officer', 'salary' => 7500000],
                    ],
                ],
                [
                    'name' => 'IT Engineering',
                    'code' => 'ENG',
                    'description' => 'Rekayasa perangkat lunak, arsitektur sistem, infrastruktur cloud/server, keamanan siber, dan operasional teknologi informasi.',
                    'positions' => [
                        ['title' => 'VP of Engineering / CTO', 'salary' => 32000000],
                        ['title' => 'Engineering Manager', 'salary' => 22000000],
                        ['title' => 'Senior Software Engineer', 'salary' => 16000000],
                        ['title' => 'DevOps & Cloud Engineer', 'salary' => 14000000],
                        ['title' => 'Fullstack Developer', 'salary' => 11500000],
                        ['title' => 'QA Automation Engineer', 'salary' => 10000000],
                        ['title' => 'Cyber Security Specialist', 'salary' => 13500000],
                        ['title' => 'IT Support & Systems Administrator', 'salary' => 7500000],
                    ],
                ],
                [
                    'name' => 'Finance, Accounting & Tax',
                    'code' => 'FIN',
                    'description' => 'Manajemen perbendaharaan keuangan perusahaan, pelaporan akuntansi, audit keuangan, dan perpajakan korporat.',
                    'positions' => [
                        ['title' => 'VP of Finance / CFO', 'salary' => 28000000],
                        ['title' => 'Finance & Accounting Manager', 'salary' => 19000000],
                        ['title' => 'Senior Financial Analyst', 'salary' => 14000000],
                        ['title' => 'Senior Corporate Accountant', 'salary' => 11500000],
                        ['title' => 'Tax & Regulatory Specialist', 'salary' => 10000000],
                        ['title' => 'Junior Accountant & AP/AR Officer', 'salary' => 7500000],
                    ],
                ],
                [
                    'name' => 'Marketing & Corporate Communications',
                    'code' => 'MKT',
                    'description' => 'Pemasaran strategis, branding korporat, digital acquisition, media sosial, dan hubungan masyarakat.',
                    'positions' => [
                        ['title' => 'Head of Marketing / CMO', 'salary' => 24000000],
                        ['title' => 'Marketing Communications Manager', 'salary' => 17000000],
                        ['title' => 'Digital Marketing & Growth Specialist', 'salary' => 11500000],
                        ['title' => 'Brand & Creative Designer', 'salary' => 9500000],
                        ['title' => 'Public Relations Specialist', 'salary' => 9000000],
                        ['title' => 'Social Media & Content Creator', 'salary' => 7500000],
                    ],
                ],
                [
                    'name' => 'Sales & Business Development',
                    'code' => 'SLS',
                    'description' => 'Penjualan korporat B2B, ekspansi kemitraan bisnis strategis, dan pengelolaan relasi klien.',
                    'positions' => [
                        ['title' => 'Head of Commercial & Sales', 'salary' => 24000000],
                        ['title' => 'Business Development Manager', 'salary' => 17500000],
                        ['title' => 'Senior Key Account Manager', 'salary' => 13000000],
                        ['title' => 'Corporate Sales Executive', 'salary' => 9500000],
                        ['title' => 'Customer Success Specialist', 'salary' => 8000000],
                    ],
                ],
                [
                    'name' => 'Operations & Supply Chain',
                    'code' => 'OPR',
                    'description' => 'Manajemen operasional bisnis, rantai pasok logistik, pengadaan barang & jasa, serta manajemen mutu.',
                    'positions' => [
                        ['title' => 'Chief Operating Officer / VP Operations', 'salary' => 26000000],
                        ['title' => 'Supply Chain & Logistics Manager', 'salary' => 18000000],
                        ['title' => 'Procurement & Purchasing Specialist', 'salary' => 10500000],
                        ['title' => 'Warehouse & Inventory Supervisor', 'salary' => 9000000],
                        ['title' => 'Quality Assurance & Process Officer', 'salary' => 8000000],
                    ],
                ],
                [
                    'name' => 'Legal & Corporate Governance',
                    'code' => 'LGL',
                    'description' => 'Kepatuhan regulasi korporasi, perancangan kontrak, audit kepatuhan internal, dan penanganan sengketa hukum.',
                    'positions' => [
                        ['title' => 'Head of Legal & Corporate Secretary', 'salary' => 23000000],
                        ['title' => 'Senior Corporate Legal Counsel', 'salary' => 15000000],
                        ['title' => 'Internal Audit & Risk Lead', 'salary' => 14000000],
                        ['title' => 'Legal & Compliance Officer', 'salary' => 9500000],
                    ],
                ],
                [
                    'name' => 'Product & Design',
                    'code' => 'PRD',
                    'description' => 'Riset kebutuhan pengguna, perancangan peta jalan produk inovatif, dan desain antarmuka pengguna (UI/UX).',
                    'positions' => [
                        ['title' => 'Head of Product', 'salary' => 25000000],
                        ['title' => 'Lead Product Manager', 'salary' => 18000000],
                        ['title' => 'UI/UX Design Lead', 'salary' => 14000000],
                        ['title' => 'Product Owner', 'salary' => 11000000],
                        ['title' => 'UX Researcher', 'salary' => 9500000],
                    ],
                ],
            ];

            foreach ($departmentsData as $deptItem) {
                $department = Department::firstOrCreate(
                    ['code' => $deptItem['code']],
                    [
                        'name' => $deptItem['name'],
                        'description' => $deptItem['description'],
                    ]
                );

                foreach ($deptItem['positions'] as $posItem) {
                    Position::firstOrCreate(
                        [
                            'department_id' => $department->id,
                            'title' => $posItem['title'],
                        ],
                        [
                            'base_salary_default' => $posItem['salary'],
                        ]
                    );
                }
            }

            // 5. Baseline Accounts (1 Account per Role)
            $managerRole = Role::firstOrCreate(['name' => 'manager']);
            $employeeRole = Role::firstOrCreate(['name' => 'employee']);
            $regularShift = Shift::where('name', 'Regular Office')->first();
            $deptEngineering = Department::where('code', 'ENG')->first();
            $posEngManager = Position::where('department_id', $deptEngineering->id)
                ->where('title', 'like', '%Manager%')
                ->first();
            $posDev = Position::where('department_id', $deptEngineering->id)
                ->where('title', 'Fullstack Developer')
                ->first();

            // 1 Manager Account: Bambang Wijaya (IT Engineering Manager)
            $mgrUser = User::firstOrCreate(
                ['email' => 'manager@nashris.com'],
                [
                    'name' => 'Bambang Wijaya',
                    'password' => Hash::make('password123'),
                    'is_active' => true,
                    'must_change_password' => false,
                ]
            );
            $mgrUser->syncRoles([$managerRole]);

            $mgrEmp = Employee::firstOrCreate(
                ['user_id' => $mgrUser->id],
                [
                    'employee_code' => 'EMP-2026-002',
                    'department_id' => $deptEngineering->id,
                    'position_id' => $posEngManager->id,
                    'manager_id' => null,
                    'shift_id' => $regularShift->id,
                    'phone' => '081234567802',
                    'address' => 'Jl. Jenderal Sudirman No. 45, Jakarta Selatan',
                    'join_date' => '2024-01-01',
                    'contract_type' => 'PKWTT',
                    'contract_end_date' => null,
                    'base_salary' => 22000000,
                    'allowance_fixed' => 3000000,
                ]
            );

            // 1 Employee Account: Muhammad Nashir Lidienillah (Fullstack Developer)
            $empUser = User::firstOrCreate(
                ['email' => 'nashir@gmail.com'],
                [
                    'name' => 'Muhammad Nashir Lidienillah',
                    'password' => Hash::make('password123'),
                    'is_active' => true,
                    'must_change_password' => false,
                ]
            );
            $empUser->syncRoles([$employeeRole]);

            $nashirEmp = Employee::firstOrCreate(
                ['user_id' => $empUser->id],
                [
                    'employee_code' => 'EMP-2026-001',
                    'department_id' => $deptEngineering->id,
                    'position_id' => $posDev->id,
                    'manager_id' => $mgrEmp->id,
                    'shift_id' => $regularShift->id,
                    'phone' => '081386136200',
                    'address' => 'Serang',
                    'join_date' => '2026-11-25',
                    'contract_type' => 'Internship',
                    'contract_end_date' => '2027-02-25',
                    'base_salary' => 9000000,
                    'allowance_fixed' => 1000000,
                ]
            );
            $nashirEmp->update(['manager_id' => $mgrEmp->id]);

            // Leave Balances for 2026
            foreach ([$mgrEmp, $nashirEmp] as $emp) {
                LeaveBalance::firstOrCreate(
                    ['employee_id' => $emp->id, 'year' => 2026],
                    [
                        'total_quota' => 12,
                        'used_quota' => 0,
                        'remaining_quota' => 12,
                    ]
                );
            }
        });
    }
}
