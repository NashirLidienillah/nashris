<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Http\Requests\HR\StorePositionRequest;
use App\Http\Requests\HR\UpdatePositionRequest;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class PositionController extends Controller
{
    public function index(Request $request): View
    {
        $departments = Cache::remember('departments_all', 600, fn() => Department::orderBy('name')->get());

        $query = Position::with(['department'])->withCount('employees')->latest();

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->input('department_id'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('title', 'like', "%{$search}%");
        }

        $positions = $query->paginate(15)->withQueryString();

        return view('hr.positions.index', compact('positions', 'departments'));
    }

    public function store(StorePositionRequest $request): RedirectResponse
    {
        Position::create($request->validated());

        Cache::forget('positions_all');
        Cache::forget('hr_dashboard_analytics');

        return redirect()->route('hr.positions.index')
            ->with('success', 'Jabatan baru berhasil didaftarkan.');
    }

    public function update(UpdatePositionRequest $request, Position $position): RedirectResponse
    {
        $position->update($request->validated());

        Cache::forget('positions_all');
        Cache::forget('hr_dashboard_analytics');

        return redirect()->route('hr.positions.index')
            ->with('success', 'Data jabatan berhasil diperbarui.');
    }

    public function destroy(Position $position): RedirectResponse
    {
        if ($position->employees()->exists()) {
            return back()->with('error', 'Gagal menghapus: Jabatan ini sedang diemban oleh karyawan aktif.');
        }

        $position->delete();

        Cache::forget('positions_all');
        Cache::forget('hr_dashboard_analytics');

        return redirect()->route('hr.positions.index')
            ->with('success', 'Jabatan berhasil dihapus dari sistem.');
    }
}
