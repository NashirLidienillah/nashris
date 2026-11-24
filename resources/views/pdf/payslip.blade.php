<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Slip Gaji - {{ $item->employee->employee_code }} - {{ $item->payroll->month }}/{{ $item->payroll->year }}</title>
    <style>
        @page {
            margin: 25px 30px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 11px;
            line-height: 1.4;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }
        .brand-logo {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
        }
        .brand-logo span {
            color: #4f46e5;
        }
        .brand-sub {
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .doc-title {
            text-align: right;
        }
        .doc-title h2 {
            margin: 0;
            font-size: 14px;
            text-transform: uppercase;
            color: #1e293b;
            letter-spacing: 0.5px;
        }
        .doc-title .period {
            font-size: 11px;
            font-weight: bold;
            color: #4f46e5;
            margin-top: 3px;
        }
        .info-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 18px;
        }
        .info-table {
            width: 100%;
        }
        .info-table td {
            padding: 3px 6px;
            vertical-align: top;
        }
        .info-label {
            color: #64748b;
            font-weight: 600;
            width: 18%;
        }
        .info-val {
            color: #0f172a;
            font-weight: 700;
            width: 32%;
        }
        .salary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }
        .salary-table th {
            background-color: #f1f5f9;
            color: #334155;
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: 0.5px;
            padding: 8px 10px;
            border: 1px solid #cbd5e1;
        }
        .salary-table td {
            padding: 8px 10px;
            border: 1px solid #e2e8f0;
        }
        .amount-col {
            text-align: right;
            font-family: 'Courier New', Courier, monospace;
            font-weight: bold;
        }
        .subtotal-row {
            background-color: #f8fafc;
            font-weight: bold;
        }
        .net-salary-box {
            background-color: #eef2ff;
            border: 2px solid #6366f1;
            border-radius: 6px;
            padding: 12px 16px;
            margin-bottom: 25px;
        }
        .net-salary-table {
            width: 100%;
        }
        .net-salary-label {
            font-size: 12px;
            font-weight: 800;
            color: #312e81;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .net-salary-amount {
            text-align: right;
            font-size: 16px;
            font-weight: 800;
            color: #4338ca;
            font-family: 'Courier New', Courier, monospace;
        }
        .footer-table {
            width: 100%;
            border-top: 1px solid #e2e8f0;
            padding-top: 12px;
            color: #94a3b8;
            font-size: 9px;
        }
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            background-color: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <table class="header-table">
        <tr>
            <td style="vertical-align: middle;">
                <div class="brand-logo">Nas<span>HRIS</span></div>
                <div class="brand-sub">Enterprise Human Resource Information System</div>
            </td>
            <td class="doc-title" style="vertical-align: middle;">
                <h2>Slip Gaji Karyawan</h2>
                <div class="period">
                    Periode: {{ \Carbon\Carbon::create($item->payroll->year, $item->payroll->month, 1)->translatedFormat('F Y') }}
                </div>
                <div style="margin-top: 4px;">
                    <span class="status-badge">{{ $item->payroll->status }}</span>
                </div>
            </td>
        </tr>
    </table>

    <!-- Employee Information Details -->
    <div class="info-card">
        <table class="info-table">
            <tr>
                <td class="info-label">Nama Lengkap:</td>
                <td class="info-val">{{ $item->employee->user->name }}</td>
                <td class="info-label">Departemen:</td>
                <td class="info-val">{{ $item->employee->department->name }}</td>
            </tr>
            <tr>
                <td class="info-label">NIK / Kode:</td>
                <td class="info-val">{{ $item->employee->employee_code }}</td>
                <td class="info-label">Jabatan:</td>
                <td class="info-val">{{ $item->employee->position->title }}</td>
            </tr>
            <tr>
                <td class="info-label">Status Kontrak:</td>
                <td class="info-val">{{ $item->employee->contract_type }}</td>
                <td class="info-label">Tgl. Bergabung:</td>
                <td class="info-val">{{ \Carbon\Carbon::parse($item->employee->join_date)->translatedFormat('d F Y') }}</td>
            </tr>
        </table>
    </div>

    <!-- Salary Component Breakdown Table -->
    <table class="salary-table">
        <thead>
            <tr>
                <th style="width: 50%;">Komponen Penerimaan (Earnings)</th>
                <th style="width: 50%;">Komponen Potongan (Deductions)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <!-- Penerimaan -->
                <td style="vertical-align: top; padding: 0;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="border: none; padding: 6px 8px;">Gaji Pokok (Base Salary)</td>
                            <td class="amount-col" style="border: none; padding: 6px 8px;">
                                Rp {{ number_format($item->base_salary, 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr>
                            <td style="border: none; padding: 6px 8px;">Tunjangan Tetap (Fixed Allowance)</td>
                            <td class="amount-col" style="border: none; padding: 6px 8px;">
                                Rp {{ number_format($item->allowances, 0, ',', '.') }}
                            </td>
                        </tr>
                    </table>
                </td>

                <!-- Potongan -->
                <td style="vertical-align: top; padding: 0;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="border: none; padding: 6px 8px;">Potongan Keterlambatan (Late)</td>
                            <td class="amount-col" style="border: none; padding: 6px 8px; color: #b91c1c;">
                                Rp {{ number_format($item->late_deduction, 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr>
                            <td style="border: none; padding: 6px 8px;">Potongan Alpa (Absence)</td>
                            <td class="amount-col" style="border: none; padding: 6px 8px; color: #b91c1c;">
                                Rp {{ number_format($item->absence_deduction, 0, ',', '.') }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="subtotal-row">
                <td>
                    <table style="width: 100%;">
                        <tr>
                            <td style="border: none; padding: 4px 0; font-weight: bold;">Total Penerimaan Kotor:</td>
                            <td class="amount-col" style="border: none; padding: 4px 0;">
                                Rp {{ number_format($item->base_salary + $item->allowances, 0, ',', '.') }}
                            </td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table style="width: 100%;">
                        <tr>
                            <td style="border: none; padding: 4px 0; font-weight: bold;">Total Potongan:</td>
                            <td class="amount-col" style="border: none; padding: 4px 0; color: #b91c1c;">
                                Rp {{ number_format($item->late_deduction + $item->absence_deduction, 0, ',', '.') }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Take Home Pay (Net Salary) Box -->
    <div class="net-salary-box">
        <table class="net-salary-table">
            <tr>
                <td class="net-salary-label">
                    Gaji Bersih Diterima (Take Home Pay)
                </td>
                <td class="net-salary-amount">
                    Rp {{ number_format($item->net_salary, 0, ',', '.') }}
                </td>
            </tr>
        </table>
    </div>

    <!-- Footer Disclaimers -->
    <table class="footer-table">
        <tr>
            <td style="width: 60%;">
                <div>Dicetak secara otomatis pada: {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y H:i') }} WIB</div>
                <div style="font-style: italic; margin-top: 2px;">
                    Dokumen ini adalah slip gaji resmi yang diterbitkan secara elektronik oleh NasHRIS dan sah tanpa memerlukan tanda tangan basah.
                </div>
            </td>
            <td style="width: 40%; text-align: right; vertical-align: top;">
                <div>Diproses oleh: <strong>{{ $item->payroll->processor->name ?? 'HR Administrator' }}</strong></div>
                <div>Departemen Human Resources</div>
            </td>
        </tr>
    </table>
</body>
</html>
