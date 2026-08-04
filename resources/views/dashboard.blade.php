@extends('layouts.app')

@section('content')
<div class="container py-4">

    <!-- 1. Header & Month Selector -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-0 fw-bold text-dark">Financial Overview</h2>
            <p class="text-muted small mb-0">Track your incoming and outgoing balance</p>
        </div>

        <!-- Submits form automatically on date change -->
        <form method="GET" action="{{ route('dashboard') }}" class="d-flex align-items-center gap-2">
            <label for="month" class="form-label mb-0 text-muted fw-semibold small">Period:</label>
            <input
                type="month"
                id="month"
                name="month"
                class="form-control form-control-sm"
                value="{{ $month ?? date('Y-m') }}"
                onchange="this.form.submit()"
            >
        </form>
    </div>

    <!-- 2. Metric Cards Row -->
    <div class="row g-3 mb-4">
        <!-- Total Income Card -->
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm bg-success text-white">
                <div class="card-body p-3">
                    <span class="text-white-50 text-uppercase fw-bold small">Total Income</span>
                    <h3 class="fw-bold mt-1 mb-0">${{ number_format($totalIncome, 2) }}</h3>
                </div>
            </div>
        </div>

        <!-- Total Expense Card -->
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm bg-danger text-white">
                <div class="card-body p-3">
                    <span class="text-white-50 text-uppercase fw-bold small">Total Expense</span>
                    <h3 class="fw-bold mt-1 mb-0">${{ number_format($totalExpense, 2) }}</h3>
                </div>
            </div>
        </div>

        <!-- Net Balance Card -->
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body p-3">
                    <span class="text-white-50 text-uppercase fw-bold small">Net Balance</span>
                    <h3 class="fw-bold mt-1 mb-0">${{ number_format($totalIncome - $totalExpense, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Visuals Section: Chart & Recent Table -->
    <div class="row g-4">

        <!-- Left Column: Chart Container -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="mb-0 fw-bold text-dark">Expense Breakdown by Category</h6>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <div style="width: 100%; max-width: 320px;">
                        <canvas id="expenseChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Recent Transactions Table -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-dark">Recent Transactions</h6>
                    <a href="{{ route('transactions.create') }}" class="btn btn-sm btn-primary">
                        + Add Transaction
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Date</th>
                                    <th>Category</th>
                                    <th class="text-end pe-3">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentTransactions as $tx)
                                    <tr>
                                        <!-- Formatted Date -->
                                        <td class="ps-3 text-secondary small">
                                            {{ $tx->transaction_date->format('M d, Y') }}
                                        </td>
                                        <!-- Category Badge with DB Dynamic Color -->
                                        <td>
                                            <span class="badge rounded-pill text-white px-2 py-1"
                                                  style="background-color: {{ $tx->category->color ?? '#6c757d' }}">
                                                {{ $tx->category->name }}
                                            </span>
                                        </td>
                                        <!-- Amount with Type Color Indicator -->
                                        <td class="text-end pe-3 fw-bold {{ $tx->type === 'income' ? 'text-success' : 'text-danger' }}">
                                            {{ $tx->type === 'income' ? '+' : '-' }}${{ number_format($tx->amount, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">
                                            No recent transactions logged yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- 4. Load Chart.js Library and Pass Backend Array into Javascript -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const ctx = document.getElementById('expenseChart').getContext('2d');

        // Convert Laravel Eloquent Collection to JS Object Array
        const rawCategoryData = @json($expensesByCategory);

        // Map categories to labels, values, and dynamic DB colors
        const labels = rawCategoryData.map(item => item.category ? item.category.name : 'Unassigned');
        const amounts = rawCategoryData.map(item => item.total);
        const colors = rawCategoryData.map(item => item.category ? item.category.color : '#6c757d');

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: amounts,
                    backgroundColor: colors,
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 15
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
