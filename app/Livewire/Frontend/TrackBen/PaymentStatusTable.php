<?php

namespace App\Livewire\Frontend\TrackBen;

use App\Models\BenFailedPaymentDetailsJB;
use App\Models\BenFailedPaymentDetailsLB;
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
    public $ben_status;
    public $bank_code;
    public $ifsc;
    public $fin_year;

    public function configure(): void
    {
        $this->setPrimaryKey('month_idx')
            ->setPerPageAccepted([12])
            ->setPerPage(12)
            ->setPaginationStatus(false)
            ->setSearchStatus(false)
            ->setSortingDisabled()
            ->setColumnSelectDisabled()
            ->setFiltersStatus(false);
        // ->setTableAttributes([
        //     'class' => 'w-full border-collapse bg-white',
        // ])
        // ->setThAttributes(function (Column $column) {
        //     return ['class' => 'font-bold text-gray-700 bg-gray-50 border-b border-gray-200 text-[13px] py-4 px-6 text-left uppercase tracking-wider'];
        // })
        // ->setTdAttributes(function (Column $column, $row, $columnIndex, $rowIndex) {
        //     return ['class' => 'px-6 py-4 border-b border-gray-100 text-gray-700 align-middle'];
        // });

        $this->setTableWrapperAttributes([
            'class' => 'overflow-x-auto overflow-y-auto max-h-[500px] border border-gray-200 rounded-lg shadow-sm',
        ]);

        $this->setTableAttributes([
            'class' => 'min-w-full text-sm text-gray-700 text-center overflow-x-auto',
        ]);

        $this->setTheadAttributes([
            'class' => 'bg-violet-800 text-xs uppercase py-3 px-4 text-white',
        ]);
        $this->setThAttributes(function ($column) {
            return [
                'class' => 'px-4 py-3 text-white bg-violet-800 text-xs',
            ];
        });

        $this->setTdAttributes(function ($row) {
            return [
                'class' => 'px-4 py-3 text-gray-700 text-center',
            ];
        });

        $this->setTbodyAttributes([
            'class' => 'px-4 py-3 divide-y divide-gray-200 bg-white overflow-y-auto',
        ]);
    }

    public function builder(): Builder
    {
        // dd($this->ben_id, $this->scheme_id, $this->fin_year);
        $model = $this->scheme_id == 20 ? BenTransactionDetailsLB::class : BenTransactionDetailsJB::class;
        // dd($model);
        $months = [
            1 => ['apr', 'April'],
            2 => ['may', 'May'],
            3 => ['jun', 'June'],
            4 => ['jul', 'July'],
            5 => ['aug', 'August'],
            6 => ['sep', 'September'],
            7 => ['oct', 'October'],
            8 => ['nov', 'November'],
            9 => ['dec', 'December'],
            10 => ['jan', 'January'],
            11 => ['feb', 'February'],
            12 => ['mar', 'March']
        ];

        $query = null;
        // Fetch the created_at of the beneficiary
        if ($this->scheme_id == 20) {
            $benPersonal = BenTransactionDetailsLB::select('created_at')->where('ben_id', $this->ben_id)->first();
        } else {
            $benPersonal = BenTransactionDetailsJB::select('created_at')->where('ben_id', $this->ben_id)->first();
        }
        $start_month_idx = 1;
        $end_month_idx = 12;

        /*
        if ($benPersonal && $benPersonal->created_at) {
            $created_time = strtotime($benPersonal->created_at);
            $created_year = (int) date('Y', $created_time);
            $created_month = (int) date('m', $created_time);
            $created_fin_year = ($created_month >= 4) ? $created_year . '-' . ($created_year + 1) : ($created_year - 1) . '-' . $created_year;

            if ($this->fin_year === $created_fin_year) {
                $start_month_idx = $created_month >= 4 ? $created_month - 3 : $created_month + 9;
            } elseif ($this->fin_year !== null && $this->fin_year < $created_fin_year) {
                $start_month_idx = 13; // Don't show any months if fin_year is strictly before the creation fin_year
            }
        }

        $current_time = time();

        $current_year = (int) date('Y', $current_time);
        $current_month = (int) date('m', $current_time);
        $current_fin_year = ($current_month >= 4) ? $current_year . '-' . ($current_year + 1) : ($current_year - 1) . '-' . $current_year;

        if ($this->fin_year === $current_fin_year) {
            $end_month_idx = ($current_month >= 4) ? $current_month - 3 : $current_month + 9;
        } elseif ($this->fin_year !== null && $this->fin_year > $current_fin_year) {
            $end_month_idx = 0; // Don't show any months for future financial years
        }
        */

        // foreach ($months as $idx => $m) {
        //     if ($idx < $start_month_idx || $idx > $end_month_idx) {
        //         continue;
        //     }

        //     $prefix = $m[0];
        //     $monthName = $m[1];

        //     $q = $model::query()
        //         ->selectRaw("{$idx} as month_idx, '{$monthName}' as month_name, {$prefix}_lot_status as lot_status, '{$prefix}' as prefix")
        //         ->where('ben_id', $this->ben_id)
        //         ->limit(1);

        //     if ($this->scheme_id != 20) {
        //         $q->where('scheme_id', $this->scheme_id);
        //     }

        //     if (!empty($this->fin_year)) {
        //         $q->where('fin_year', $this->fin_year);
        //     }

        //     if ($query === null) {
        //         $query = $q;
        //     } else {
        //         $query->unionAll($q);
        //     }
        // }
        // dd($model);
        $data = $model::query()
            ->where('ben_id', $this->ben_id)
            ->when($this->scheme_id != 20, function ($q) {
                $q->where('scheme_id', $this->scheme_id);
            })
            ->when(!empty($this->fin_year), function ($q) {
                $q->where('fin_year', $this->fin_year);
            })
            ->first();

        $dummy = new DummyPaymentModel();
        $dummy->dynamicConnection = app($model)->getConnectionName();

        if (!$data) {
            // Return an empty result with the same columns and types
            return $dummy->newQuery()
                ->selectRaw("0 as month_idx, '' as month_name, '' as lot_status, '' as prefix")
                ->fromRaw("(SELECT 1 as dummy) as d")
                ->whereRaw('1=0');
        }

        $query = null;

        foreach ($months as $idx => $m) {
            if ($idx < $start_month_idx || $idx > $end_month_idx) {
                continue;
            }
            $prefix = $m[0];
            $monthName = $m[1];
            $column = "{$prefix}_lot_status";
            $val = $data ? ($data->$column ?? '') : '';

            if ($val === 'R') {
                continue;
            }

            // Create a virtual row of constants for each month
            $q = $dummy->newQuery()
                ->selectRaw("{$idx} as month_idx, '{$monthName}' as month_name, '{$val}' as lot_status, '{$prefix}' as prefix")
                ->fromRaw("(SELECT 1 as dummy) as d")
                ->limit(1);

            if ($query === null) {
                $query = $q;
            } else {
                $query->unionAll($q);
            }
        }
        if ($query === null && $data === null) {
            $query = $dummy->newQuery()
                ->selectRaw("0 as month_idx, '' as month_name, '' as lot_status, '' as prefix")
                ->fromRaw("(SELECT 1 as dummy) as d")
                ->whereRaw('1=0');
        }
        /** @var \Illuminate\Database\Eloquent\Builder $builder */
        $builder = $dummy->newQuery()->fromSub($query, 'payments')->orderBy('month_idx', 'asc');
        return $builder;
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
                    $statusColor = 'gray';
                    $statusLabel = 'Payment yet to be generated';
                    $statusIcon = 'fa-solid fa-clock-rotate-left';
                    if ($value === 'S') {
                        $statusColor = 'emerald';
                        $statusLabel = 'Payment Success';
                        $statusIcon = 'fa-solid fa-circle-check';
                    } elseif ($value === 'R' || $value === 'E') {
                        $statusColor = 'amber';
                        $statusLabel = 'Payment yet to be generated';
                        $statusIcon = 'fa-solid fa-clock-rotate-left';
                    } elseif ($value === 'P') {
                        $statusColor = 'blue';
                        $statusLabel = 'Payment Under Process';
                        $statusIcon = 'fa-solid fa-spinner fa-spin';
                    } elseif ($value === 'F' || $value === 'M') {
                        $statusColor = 'rose';
                        $statusLabel = 'Payment Failed';
                        $statusIcon = 'fa-solid fa-circle-exclamation';
                    } elseif ($value !== '' && $value !== null) {
                        $statusColor = 'blue';
                        $statusLabel = 'Payment Under Process';
                        $statusIcon = 'fa-solid fa-spinner fa-spin';
                    }
                    $html = "<span class=\"inline-flex items-center gap-1.5 bg-{$statusColor}-50 text-{$statusColor}-700 text-[13px] font-semibold px-3 py-1.5 rounded-full border border-{$statusColor}-200 shadow-sm\"><i class=\"{$statusIcon} text-{$statusColor}-500\"></i> {$statusLabel}</span>";

                    if ($this->scheme_id == 20) {
                        $model = BenFailedPaymentDetailsLB::class;
                    } else {
                        $model = BenFailedPaymentDetailsJB::class;
                    }
                    if ($value === 'F' || $value === 'M') {
                        if ($this->scheme_id == 20) {
                            $failedPaymentDetails = $model::where('ben_id', $this->ben_id)
                                ->value('lot_no');
                        } else {
                            $failedPaymentDetails = $model::where('ben_id', $this->ben_id)
                                ->where('scheme_id', $this->scheme_id)
                                ->whereIn('failed_type', [3, 4, 5])
                                ->value('lot_no');
                        }

                        $btnValue = "{$failedPaymentDetails}_{$this->ben_id}_{$this->fin_year}_{$this->scheme_id}";

                        $html .= "<button wire:click.prevent=\"showError('{$btnValue}')\" class=\"inline-flex items-center gap-1 bg-red-500 hover:bg-red-600 text-white text-[12px] font-semibold px-2.5 py-1 rounded shadow-sm focus:outline-none transition-colors border border-red-600 ml-2\"><i class=\"fa-solid fa-eye\"></i> View Error</button>";
                    }

                    return $html;
                })
                ->html(),
        ];
    }

    public function showError($value)
    {
        $params = explode('_', $value);
        $lot_no = $params[0] ?? null;
        $pension_id = $params[1] ?? null;
        $fin_year = $params[2] ?? null;
        $schemeId = $params[3] ?? null;

        if (!$lot_no || $lot_no === '') {
            $this->dispatch('toastr', [
                'type' => 'error',
                'message' => 'No error details found.'
            ]);
            return;
        }

        try {
            if ($schemeId == 20) {
                $model = BenTransactionDetailsLB::class;
                $lotObj = $model::where('ben_id', $pension_id)
                    ->where('scheme_id', $schemeId)
                    ->whereIn('failed_type', [3, 4, 5])
                    ->first();
            } else {
                $model = BenTransactionDetailsJB::class;
                $lotObj = $model::where('ben_id', $pension_id)
                    ->where('scheme_id', $schemeId)
                    ->whereIn('failed_type', [3, 4, 5])
                    ->first();
            }

            if (!$lotObj) {
                $this->dispatch('toastr', [
                    'type' => 'info',
                    'message' => 'No detailed error message found.'
                ]);
                return;
            }

            if ($lotObj->pmt_mode == 1 && $schemeId != 20) {
                $results = BenFailedPaymentDetailsJB::where('ben_id', $pension_id)
                    ->where('scheme_id', $schemeId)
                    ->where('lot_no', $lot_no)
                    ->orderBy('created_at', 'desc')
                    ->first();

                $this->dispatch('toastr', [
                    'type' => 'error',
                    'message' => $results->remarks ?? 'No remarks found'
                ]);

                $this->dispatch('toastr', [
                    'type' => 'error',
                    'message' => $results->remarks ?? 'No remarks found'
                ]);
            } elseif ($lotObj->pmt_mode == 2 && $schemeId != 20) {
                $results = BenFailedPaymentDetailsJB::where('ben_id', $pension_id)
                    ->where('scheme_id', $schemeId)
                    ->where('lot_no', $lot_no)
                    ->orderBy('created_at', 'desc')
                    ->first();

                $this->dispatch('toastr', [
                    'type' => 'error',
                    'message' => $results->description ?? 'No description found'
                ]);
            } else if ($schemeId == 20) {
                $results = BenFailedPaymentDetailsLB::where('ben_id', $pension_id)
                    ->where('lot_no', $lot_no)
                    ->orderBy('created_at', 'desc')
                    ->first();

                $this->dispatch('toastr', [
                    'type' => 'error',
                    'message' => $results->description ?? 'No description found'
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('toastr', [
                'type' => 'error',
                'message' => 'Error! Please try again.'
            ]);
        }
    }
}
