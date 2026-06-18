<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class SuperadminDashboardController extends Controller
{
    public function stats(): JsonResponse
    {
        $totalUsers        = User::count();
        $activeUsers       = User::whereNotNull('updated_at')->count();
        $totalTransactions = Transaction::count();
        $recentUsers       = User::latest()->limit(5)->get(['id', 'name', 'email', 'created_at', 'role']);
        $recentAuditLogs   = AuditLog::with('user:id,name,email')->latest()->limit(10)->get();

        return $this->successResponse([
            'total_users'        => $totalUsers,
            'active_users'       => $activeUsers,
            'total_transactions' => $totalTransactions,
            'recent_users'       => $recentUsers,
            'recent_audit_logs'  => $recentAuditLogs,
        ]);
    }
}
