<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function stats(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $month  = $request->query('month', now()->format('Y-m'));

        $sections = $request->filled('sections')
            ? array_map('trim', explode(',', $request->query('sections')))
            : ['accounts', 'recent_transactions', 'monthly_trend', 'income_by_category', 'expense_by_category', 'balance_trend'];

        $accounts     = Account::forUser($userId)->where('is_archived', false)->get();
        $totalBalance = $accounts->sum('balance');

        $income = Transaction::forUser($userId)
            ->where('type', 'income')
            ->inMonth($month)
            ->sum('amount');

        $expense = Transaction::forUser($userId)
            ->where('type', 'expense')
            ->inMonth($month)
            ->sum('amount');

        $data = [
            'total_balance'   => (float) $totalBalance,
            'monthly_income'  => (float) $income,
            'monthly_expense' => (float) $expense,
        ];

        if (in_array('accounts', $sections)) {
            $data['accounts'] = $accounts;
        }

        if (in_array('recent_transactions', $sections)) {
            $data['recent_transactions'] = Transaction::forUser($userId)
                ->with(['account', 'category'])
                ->orderBy('date', 'desc')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        }

        if (in_array('monthly_trend', $sections)) {
            $data['monthly_trend'] = Transaction::forUser($userId)
                ->selectRaw('DATE_FORMAT(date, "%Y-%m") as month, type, SUM(amount) as total')
                ->whereIn('type', ['income', 'expense'])
                ->where('date', '>=', now()->subMonths(5)->startOfMonth())
                ->groupBy('month', 'type')
                ->orderBy('month')
                ->get();
        }

        if (in_array('expense_by_category', $sections)) {
            $data['expense_by_category'] = Transaction::forUser($userId)
                ->with('category')
                ->selectRaw('category_id, SUM(amount) as total')
                ->where('type', 'expense')
                ->inMonth($month)
                ->whereNotNull('category_id')
                ->groupBy('category_id')
                ->orderByDesc('total')
                ->limit(5)
                ->get();
        }

        if (in_array('income_by_category', $sections)) {
            $data['income_by_category'] = Transaction::forUser($userId)
                ->with('category')
                ->selectRaw('category_id, SUM(amount) as total')
                ->where('type', 'income')
                ->inMonth($month)
                ->whereNotNull('category_id')
                ->groupBy('category_id')
                ->orderByDesc('total')
                ->limit(5)
                ->get();
        }

        if (in_array('balance_trend', $sections)) {
            $accountIds  = $accounts->pluck('id')->toArray();
            $initialSum  = (float) $accounts->sum('initial_balance');
            $year        = (int) substr($month, 0, 4);
            $mon         = (int) substr($month, 5, 2);
            $monthStart  = \Carbon\Carbon::create($year, $mon, 1)->startOfDay();
            $monthEnd    = $monthStart->copy()->endOfMonth();

            $priorNet = Transaction::forUser($userId)
                ->whereIn('account_id', $accountIds)
                ->where('date', '<', $monthStart)
                ->selectRaw("
                    COALESCE(SUM(CASE WHEN type IN ('income','adjustment') THEN amount ELSE 0 END), 0) as gains,
                    COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) as losses,
                    COALESCE(SUM(CASE WHEN type = 'transfer' THEN amount ELSE 0 END), 0) as t_out
                ")->first();
            $priorTIn = Transaction::forUser($userId)
                ->whereIn('transfer_account_id', $accountIds)
                ->where('type', 'transfer')
                ->where('date', '<', $monthStart)
                ->sum('amount');

            $carryForward = $initialSum + (float) $priorNet->gains - (float) $priorNet->losses - (float) $priorNet->t_out + (float) $priorTIn;

            $dailyTx = Transaction::forUser($userId)
                ->whereIn('account_id', $accountIds)
                ->whereBetween('date', [$monthStart->toDateString(), $monthEnd->toDateString()])
                ->selectRaw("date,
                    COALESCE(SUM(CASE WHEN type IN ('income','adjustment') THEN amount ELSE 0 END), 0) as gains,
                    COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) as losses,
                    COALESCE(SUM(CASE WHEN type = 'transfer' THEN amount ELSE 0 END), 0) as t_out")
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->keyBy(fn ($r) => $r->date->toDateString());

            $dailyTIn = Transaction::forUser($userId)
                ->whereIn('transfer_account_id', $accountIds)
                ->where('type', 'transfer')
                ->whereBetween('date', [$monthStart->toDateString(), $monthEnd->toDateString()])
                ->selectRaw("date, COALESCE(SUM(amount), 0) as total")
                ->groupBy('date')->get()->keyBy(fn ($r) => $r->date->toDateString());

            $trend   = [];
            $running = $carryForward;
            $cursor  = $monthStart->copy();
            $today   = now()->endOfDay();

            while ($cursor->lte($monthEnd) && $cursor->lte($today)) {
                $key  = $cursor->toDateString();
                $row  = $dailyTx->get($key);
                $running += (float) ($row->gains ?? 0) - (float) ($row->losses ?? 0)
                    - (float) ($row->t_out ?? 0)
                    + (float) ($dailyTIn->get($key)?->total ?? 0);

                $trend[] = [
                    'date'    => $cursor->format('d M Y'),
                    'balance' => round($running, 2),
                ];
                $cursor->addDay();
            }

            $data['balance_trend'] = $trend;
        }

        return $this->successResponse($data);
    }

    public function balanceTrend(Request $request): JsonResponse
    {
        $userId    = $request->user()->id;
        $months    = (int) $request->query('months', 6);
        $accountId = $request->query('account_id');

        $startDate = now()->subMonths($months - 1)->startOfMonth();
        $endDate   = now()->endOfMonth();

        $accountQuery = Account::forUser($userId)->where('is_archived', false);
        if ($accountId) {
            $accountQuery->where('id', $accountId);
        }
        $accounts = $accountQuery->get();

        if ($accounts->isEmpty()) {
            return $this->successResponse([]);
        }

        $accountIds    = $accounts->pluck('id')->toArray();
        $initialSum    = (float) $accounts->sum('initial_balance');

        $txQuery = Transaction::forUser($userId)
            ->whereIn('account_id', $accountIds)
            ->where('date', '<', $startDate->toDateString())
            ->selectRaw("
                COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) as income,
                COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) as expense
            ");

        if (!$accountId) {
            $transferIn = Transaction::forUser($userId)
                ->whereIn('transfer_account_id', $accountIds)
                ->where('type', 'transfer')
                ->where('date', '<', $startDate->toDateString())
                ->sum('amount');
            $transferOut = Transaction::forUser($userId)
                ->whereIn('account_id', $accountIds)
                ->where('type', 'transfer')
                ->where('date', '<', $startDate->toDateString())
                ->sum('amount');
        } else {
            $transferIn = Transaction::forUser($userId)
                ->where('transfer_account_id', $accountId)
                ->where('type', 'transfer')
                ->where('date', '<', $startDate->toDateString())
                ->sum('amount');
            $transferOut = Transaction::forUser($userId)
                ->where('account_id', $accountId)
                ->where('type', 'transfer')
                ->where('date', '<', $startDate->toDateString())
                ->sum('amount');
        }

        $priorTx = $txQuery->first();
        $carryForward = $initialSum + (float) $priorTx->income - (float) $priorTx->expense + (float) $transferIn - (float) $transferOut;

        $monthlyTx = Transaction::forUser($userId)
            ->whereIn('account_id', $accountIds)
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->selectRaw("
                DATE_FORMAT(date, '%Y-%m') as month,
                COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) as income,
                COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) as expense
            ")
            ->groupByRaw("DATE_FORMAT(date, '%Y-%m')")
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        if (!$accountId) {
            $monthlyTransferIn = Transaction::forUser($userId)
                ->whereIn('transfer_account_id', $accountIds)
                ->where('type', 'transfer')
                ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
                ->selectRaw("DATE_FORMAT(date, '%Y-%m') as month, COALESCE(SUM(amount), 0) as total")
                ->groupByRaw("DATE_FORMAT(date, '%Y-%m')")
                ->get()
                ->keyBy('month');
            $monthlyTransferOut = Transaction::forUser($userId)
                ->whereIn('account_id', $accountIds)
                ->where('type', 'transfer')
                ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
                ->selectRaw("DATE_FORMAT(date, '%Y-%m') as month, COALESCE(SUM(amount), 0) as total")
                ->groupByRaw("DATE_FORMAT(date, '%Y-%m')")
                ->get()
                ->keyBy('month');
        } else {
            $monthlyTransferIn = Transaction::forUser($userId)
                ->where('transfer_account_id', $accountId)
                ->where('type', 'transfer')
                ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
                ->selectRaw("DATE_FORMAT(date, '%Y-%m') as month, COALESCE(SUM(amount), 0) as total")
                ->groupByRaw("DATE_FORMAT(date, '%Y-%m')")
                ->get()
                ->keyBy('month');
            $monthlyTransferOut = Transaction::forUser($userId)
                ->where('account_id', $accountId)
                ->where('type', 'transfer')
                ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
                ->selectRaw("DATE_FORMAT(date, '%Y-%m') as month, COALESCE(SUM(amount), 0) as total")
                ->groupByRaw("DATE_FORMAT(date, '%Y-%m')")
                ->get()
                ->keyBy('month');
        }

        $result  = [];
        $running = $carryForward;
        $cursor  = $startDate->copy();

        while ($cursor->lte($endDate)) {
            $key = $cursor->format('Y-m');
            $row = $monthlyTx->get($key);
            $tIn  = (float) ($monthlyTransferIn->get($key)?->total ?? 0);
            $tOut = (float) ($monthlyTransferOut->get($key)?->total ?? 0);

            $running += (float) ($row->income ?? 0) - (float) ($row->expense ?? 0) + $tIn - $tOut;

            $result[] = [
                'month'   => $cursor->format('M Y'),
                'balance' => round($running, 2),
            ];

            $cursor->addMonth();
        }

        return $this->successResponse($result);
    }
}
