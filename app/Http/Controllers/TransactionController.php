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

    public function create()
    {
        // Fetch categories owned by the logged-in user to populate the dropdown
        $categories = Category::where('user_id', auth()->id())->get();

        return view('transactions.create', compact('categories'));
    }

    /**
     * Store a newly created transaction in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_id'      => 'required|exists:categories,id',
            'type'             => 'required|in:income,expense',
            'amount'           => 'required|numeric|min:0.01',
            'transaction_date' => 'required|date',
            'description'      => 'nullable|string|max:255',
        ]);

        Transaction::create([
            'user_id'          => auth()->id(),
            'category_id'      => $request->category_id,
            'type'             => $request->type,
            'amount'           => $request->amount,
            'transaction_date' => $request->transaction_date,
            'description'      => $request->description,
        ]);

        return redirect()->route('dashboard')->with('success', 'Transaction added successfully!');
    }



}
