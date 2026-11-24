@props(['status'])

@php
    $statusString = (string) $status;

    // Design Guidelines Mapping
    $styles = match ($statusString) {
        'Present', 'Approved', 'Paid', 'PKWTT', 'Aktif', 'active' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'Late', 'Pending Manager', 'Pending HR', 'Draft', 'PKWT' => 'bg-amber-50 text-amber-700 border-amber-200',
        'Absent', 'Rejected', 'Non-Aktif', 'inactive' => 'bg-rose-50 text-rose-700 border-rose-200',
        'On Leave', 'Probation', 'Internship' => 'bg-sky-50 text-sky-700 border-sky-200',
        default => 'bg-slate-100 text-slate-700 border-slate-200',
    };
@endphp

<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $styles }}">
    <span class="w-1.5 h-1.5 rounded-full bg-current opacity-75"></span>
    {{ $statusString }}
</span>
