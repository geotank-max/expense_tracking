<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TransactionsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function collection()
    {
        return Transaction::with('category')
            ->where('user_id', $this->userId)
            ->get();
    }

    public function headings(): array
    {
        return ['Date', 'Type', 'Category', 'Amount ($)', 'Description'];
    }

    public function map($transaction): array
    {
        return [
            $transaction->transaction_date->format('Y-m-d'),
            ucfirst($transaction->type),
            $transaction->category->name ?? 'Unassigned',
            $transaction->amount,
            $transaction->description ?? '-',
        ];
    }
}
