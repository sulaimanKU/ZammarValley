<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OfficeExpensesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $expenses;

    public function __construct($expenses)
    {
        $this->expenses = $expenses;
    }

    public function collection()
    {
        return $this->expenses;
    }

    public function headings(): array
    {
        return [
            '#', 'Date', 'Type', 'Category', 'Fund Source',
            'Paid To / Party', 'Amount (PKR)', 'Payment Method',
            'Reference No.', 'Voucher No.', 'Status', 'Remarks',
        ];
    }

    public function map($expense): array
    {
        static $i = 0;
        $i++;

        $fundLabels = [
            'plot_payments'   => 'Plot Payments',
            'security_fee'    => 'Security Fee',
            'registry_fee'    => 'Registry Fee',
            'development_fee' => 'Development Fee',
            'transfer_fee'    => 'Transfer Fee',
            'misc_income'     => 'Misc. Income',
        ];

        return [
            $i,
            \Carbon\Carbon::parse($expense->expense_date)->format('d-m-Y'),
            ucfirst($expense->type ?? 'expense'),
            $expense->category,
            $expense->fund_source ? ($fundLabels[$expense->fund_source] ?? $expense->fund_source) : '—',
            $expense->paid_to,
            $expense->amount,
            ucwords(str_replace('_', ' ', $expense->payment_method ?? '')),
            $expense->reference_no ?? '',
            $expense->voucher_no ?? '',
            ucfirst($expense->status),
            $expense->remarks ?? '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1E3A8A']]],
        ];
    }
}
