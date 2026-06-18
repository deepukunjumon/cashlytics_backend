<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class AdminDashboardController extends Controller
{
    public function stats(): JsonResponse
    {
        $totalIncome  = Transaction::where('type', 'income')->sum('amount');
        $totalExpense = Transaction::where('type', 'expense')->sum('amount');

        return $this->successResponse([
            'total_users'        => User::count(),
            'total_transactions' => Transaction::count(),
            'total_income'       => number_format((float) $totalIncome, 2, '.', ''),
            'total_expense'      => number_format((float) $totalExpense, 2, '.', ''),
            'net_balance'        => number_format((float) ($totalIncome - $totalExpense), 2, '.', ''),
        ]);
    }
}
