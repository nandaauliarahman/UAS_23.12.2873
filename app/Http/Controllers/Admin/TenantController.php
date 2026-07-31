<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::with('owner')->withCount('events')->latest()->paginate(10);
        return view('admin.tenants.index', compact('tenants'));
    }

    public function approve(Tenant $tenant)
    {
        $tenant->update(['is_approved' => true, 'approved_at' => now()]);
        return back()->with('success', "Penyelenggara \"{$tenant->name}\" telah disetujui.");
    }

    public function reject(Tenant $tenant)
    {
        $tenant->update(['is_approved' => false, 'approved_at' => null]);
        return back()->with('success', "Status \"{$tenant->name}\" diset belum disetujui.");
    }
}