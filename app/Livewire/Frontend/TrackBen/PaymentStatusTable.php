<?php

namespace App\Livewire\Frontend\TrackBen;

use App\Models\BenTransactionDetailsJB;
use App\Models\BenTransactionDetailsLB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class DummyPaymentModel extends Model
{
    protected $table = 'payments';
    public $timestamps = false;
    public $dynamicConnection = null;

    public function getConnectionName()
    {
        return $this->dynamicConnection ?? parent::getConnectionName();
    }
}

class PaymentStatusTable extends DataTableComponent
{
    public $ben_id;
    public $scheme_id;

    public $fin_year;

    public function configure(): void
    {
        $this->setPrimaryKey('month_idx')
            ->setPerPageAccepted([12])
            ->setPerPage(12)
            ->setPaginationStatus(false)
            ->setSearchStatus(false)
            ->setSortingDisabled()
            ->setTableAttributes([
                'class' => 'w-full border-collapse rounded-xl overflow-hidden',
            ])
            ->setThAttributes(function (Column $column) {
                return ['class' => 'font-bold text-gray-800 bg-white border-b border-gray-100 text-[13px] py-4 px-6 text-left'];
            })
            ->setTdAttributes(function (Column $column, $row, $columnIndex, $rowIndex) {
                return ['class' => 'px-6 py-3 border-b border-gray-50 text-gray-600 align-middle'];
            });
    }

    public function builder(): Builder
    {
        $model = $this->scheme_id == 20 ? BenTransactionDetailsLB::class : BenTransactionDetailsJB::class;

        $months = [
            1 => ['apr', 'April'], 2 => ['may', 'May'], 3 => ['jun', 'June'], 4 => ['jul', 'July'],
            5 => ['aug', 'August'], 6 => ['sep', 'September'], 7 => ['oct', 'October'], 8 => ['nov', 'November'],
            9 => ['dec', 'December'], 10 => ['jan', 'January'], 11 => ['feb', 'February'], 12 => ['mar', 'March']
        ];

        $query = null;

        foreach ($months as $idx => $m) {
            $prefix = $m[0];
            $monthName = $m[1];
            
            // Build the select dynamically for the current month prefix
            $q = $model::query()
                ->selectRaw("{$idx} as month_idx, '{$monthName}' as month_name, {$prefix}_lot_status as lot_status, '{$prefix}' as prefix")
                ->where('ben_id', $this->ben_id);

            if ($this->scheme_id != 20) {
                $q->where('scheme_id', $this->scheme_id);
            }

            if (!empty($this->fin_year)) {
                $q->where('fin_year', $this->fin_year);
            }

            if ($query === null) {
                $query = $q;
            } else {
                $query->unionAll($q);
            }
        }
        
        $dummy = new DummyPaymentModel();
        $dummy->dynamicConnection = app($model)->getConnectionName();

        if ($query === null) {
            // fallback generic query if needed
            return $dummy->newQuery()->where('month_idx', 0);
        }

        // We wrap the subquery inside a clean DummyPaymentModel. 
        // This ensures Rappasoft doesn't inject SoftDeletes ("deleted_at" is null) 
        // and safely maps the physical prefix exclusively as "payments." instead of a rigid Postgres Schema.
        return $dummy->newQuery()->fromSub($query, 'payments')->orderBy('month_idx', 'asc');
    }

    public function columns(): array
    {
        return [
            Column::make('Month')
                ->label(fn($row, Column $column) => $row->month_name)
                ->html(),

            Column::make('Payment Status')
                ->label(function ($row, Column $column) {
                    $value = $row->lot_status;
                    $statusColor = 'text-gray-500';
                    $statusLabel = 'Payment yet to be generated';
                    
                    if ($value === 'S') {
                        $statusColor = 'text-gray-600';
                        $statusLabel = 'Payment Success';
                    } elseif ($value === 'R' || empty($value)) {
                        $statusColor = 'text-gray-600';
                        $statusLabel = 'Payment yet to be generated';
                    } elseif ($value === 'P') {
                        $statusColor = 'text-gray-600';
                        $statusLabel = 'Payment Under Process';
                    } else {
                         $statusLabel = 'Payment Under Process'; // fallback
                    }
                    
                    return "<span class=\"font-medium {$statusColor}\">{$statusLabel}</span>";
                })
                ->html(),
        ];
    }
}
