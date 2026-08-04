<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function dashboard(Request $request){
        $userId = auth()->id();
        $month = $request->get('month', Carbon::now()->format('Y-m'));

        $totalIncome = Transaction::where('user_id', $userId)
        ->where('type', 'income')
        ->whereRaw("TO_CHAR(transaction_date, 'YYYY-MM') = ? ", [$month])
        ->sum('amount');

        $totalExpense = Transaction::where('user_id', $userId)
        ->where('type', 'expense')
        ->whereRaw("TO_CHAR(transaction_date, 'YYYY-MM') = ? ", [$month])
        ->sum('amount');

        // Breakdown by category for charts
        $expensesByCategory = Transaction::with('category')
        ->select('category_id', DB::raw('SUM(amount) as total'))
        ->where('user_id', $userId)
        ->where('type', 'expense')
        ->whereRaw("TO_CHAR(transaction_date, 'YYYY-MM') = ?", [$month])
        ->groupBy('category_id')
        ->get();

        $recentTransactions = Transaction::with('category')
        ->where('user_id', $userId)
        ->latest('transaction_date')
        ->take(5)
        ->get();

        return view('dashboard', compact('totalIncome', 'totalExpense', 'expensesByCategory', 'recentTransactions', 'month'));
    }
}
