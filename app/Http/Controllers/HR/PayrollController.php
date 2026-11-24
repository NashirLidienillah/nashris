<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Http\Requests\HR\GeneratePayrollRequest;
use App\Models\Department;
use App\Models\Payroll;
use App\Models\PayrollItem;
use App\Services\PayrollService;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PayrollController extends Controller
{
    public function __construct(
        protected PayrollService $payrollService
    ) {}

    public function index(Request $request): View
    {
        $payrolls = Payroll::with('processor')
            ->withCount('items')
            ->withSum('items as total_net_salary', 'net_salary')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('hr.payrolls.index', compact('payrolls'));
    }

    public function store(GeneratePayrollRequest $request): RedirectResponse
    {
        Gate::authorize('generate', Payroll::class);

        try {
            $payroll = $this->payrollService->generateMonthlyPayroll(
                (int) $request->validated('month'),
                (int) $request->validated('year'),
                auth()->user()
            );

            return redirect()->route('hr.payrolls.show', $payroll)
                ->with('success', "Draft payroll periode {$payroll->month}/{$payroll->year} berhasil digenerate untuk {$payroll->items()->count()} karyawan.");
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(Payroll $payroll, Request $request): View
    {
        Gate::authorize('viewBatch', $payroll);

        $departments = Cache::remember('departments_all', 600, fn() => Department::orderBy('name')->get());

        $query = $payroll->items()->with(['employee.user', 'employee.department', 'employee.position']);

        if ($request->filled('department_id')) {
            $query->whereHas('employee', fn($q) => $q->where('department_id', $request->input('department_id')));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('employee_code', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%{$search}%"));
            });
        }

        $items = $query->paginate(15)->withQueryString();

        $agg = $payroll->items()
            ->selectRaw('COALESCE(SUM(base_salary + allowances), 0) as gross, COALESCE(SUM(late_deduction), 0) as late, COALESCE(SUM(absence_deduction), 0) as absence, COALESCE(SUM(net_salary), 0) as net')
            ->first();

        $totals = [
            'gross' => (float) ($agg->gross ?? 0),
            'late' => (float) ($agg->late ?? 0),
            'absence' => (float) ($agg->absence ?? 0),
            'net' => (float) ($agg->net ?? 0),
        ];

        return view('hr.payrolls.show', compact('payroll', 'items', 'departments', 'totals'));
    }

    public function finalize(Payroll $payroll): RedirectResponse
    {
        Gate::authorize('finalize', $payroll);

        try {
            $this->payrollService->finalizePayroll($payroll);

            return back()->with('success', "Payroll periode {$payroll->month}/{$payroll->year} berhasil difinalisasi (Status: Paid). Karyawan sekarang dapat melihat & mengunduh slip gaji resmi.");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function downloadPdf(PayrollItem $item): Response
    {
        Gate::authorize('downloadPdf', $item);

        $item->load(['payroll.processor', 'employee.user', 'employee.department', 'employee.position']);

        $pdf = Pdf::loadView('pdf.payslip', compact('item'));
        $filename = "Payslip_{$item->employee->employee_code}_{$item->payroll->month}_{$item->payroll->year}.pdf";

        return $pdf->download($filename);
    }
}
