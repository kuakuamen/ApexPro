<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FinancialReportExport implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    public function __construct(private Collection $payments) {}

    public function collection(): Collection
    {
        return $this->payments->map(fn($p) => [
            $p->student->name ?? '—',
            $p->studentPlan->financialPlan->name ?? '—',
            $p->due_date?->format('d/m/Y') ?? '—',
            $p->paid_at?->format('d/m/Y') ?? '—',
            'R$ ' . number_format($p->original_amount ?? $p->amount, 2, ',', '.'),
            $p->discount_value ? 'R$ ' . number_format($p->discount_value, 2, ',', '.') : '—',
            'R$ ' . number_format($p->amount, 2, ',', '.'),
            match($p->status) {
                'paid'    => 'Pago',
                'pending' => 'Pendente',
                'overdue' => 'Vencido',
                default   => $p->status,
            },
            match($p->payment_method ?? '') {
                'pix'    => 'Pix',
                'card'   => 'Cartão',
                'cash'   => 'Dinheiro',
                'other'  => 'Outro',
                default  => '—',
            },
        ]);
    }

    public function headings(): array
    {
        return ['Aluno', 'Plano', 'Vencimento', 'Data Pagto', 'Valor Original', 'Desconto', 'Valor Final', 'Status', 'Forma de Pagto'];
    }

    public function title(): string
    {
        return 'Pagamentos';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
