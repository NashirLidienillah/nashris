<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\PayrollItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PayslipController extends Controller
{
    public function index(Request $request): View
    {
        $employee = auth()->user()->employee;
        abort_if(! $employee, 403, 'Profil karyawan tidak ditemukan.');

        $payslips = PayrollItem::with(['payroll'])
            ->where('employee_id', $employee->id)
            ->whereHas('payroll', fn($q) => $q->where('status', 'Paid'))
            ->join('payrolls', 'payroll_items.payroll_id', '=', 'payrolls.id')
            ->orderBy('payrolls.year', 'desc')
            ->orderBy('payrolls.month', 'desc')
            ->select('payroll_items.*')
            ->paginate(15)
            ->withQueryString();

        return view('employee.payslips.index', compact('employee', 'payslips'));
    }

    public function show(PayrollItem $item): View
    {
        Gate::authorize('view', $item);

        $item->load(['payroll.processor', 'employee.user', 'employee.department', 'employee.position']);

        return view('employee.payslips.show', compact('item'));
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
