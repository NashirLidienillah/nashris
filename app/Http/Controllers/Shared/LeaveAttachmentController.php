<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LeaveAttachmentController extends Controller
{
    /**
     * Anti-IDOR: Stream or view private leave attachment.
     * Enforces LeaveRequestPolicy 'view' check.
     */
    public function show(LeaveRequest $leave): BinaryFileResponse
    {
        Gate::authorize('view', $leave);

        if (! $leave->attachment_path || ! Storage::disk('local')->exists($leave->attachment_path)) {
            abort(404, 'Berkas lampiran cuti tidak ditemukan pada server.');
        }

        $fullPath = Storage::disk('local')->path($leave->attachment_path);

        return response()->file($fullPath);
    }
}
