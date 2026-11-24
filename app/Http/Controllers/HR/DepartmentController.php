<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Http\Requests\HR\StoreDepartmentRequest;
use App\Http\Requests\HR\UpdateDepartmentRequest;
use App\Models\Department;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Department::withCount(['positions', 'employees'])->orderBy('id', 'asc');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $departments = $query->paginate(15)->withQueryString();

        return view('hr.departments.index', compact('departments'));
    }

    public function store(StoreDepartmentRequest $request): RedirectResponse
    {
        Department::create($request->validated());

        Cache::forget('departments_all');
        Cache::forget('hr_dashboard_analytics');

        return redirect()->route('hr.departments.index')
            ->with('success', 'Departemen baru berhasil ditambahkan.');
    }

    public function update(UpdateDepartmentRequest $request, Department $department): RedirectResponse
    {
        $department->update($request->validated());

        Cache::forget('departments_all');
        Cache::forget('hr_dashboard_analytics');

        return redirect()->route('hr.departments.index')
            ->with('success', 'Data departemen berhasil diperbarui.');
    }

    public function destroy(Department $department): RedirectResponse
    {
        if ($department->employees()->exists()) {
            return back()->with('error', 'Gagal menghapus: Departemen ini masih memiliki karyawan aktif yang terhubung.');
        }

        if ($department->positions()->exists()) {
            return back()->with('error', 'Gagal menghapus: Hapus atau pindahkan jabatan terkait terlebih dahulu.');
        }

        $department->delete();

        Cache::forget('departments_all');
        Cache::forget('hr_dashboard_analytics');

        return redirect()->route('hr.departments.index')
            ->with('success', 'Departemen berhasil dihapus dari sistem.');
    }
}
