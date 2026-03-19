<?php

namespace App\Livewire;

use App\Models\BeneficiaryPersonalDetail;
use App\Models\Codemaster;
use Illuminate\Database\Eloquent\Builder;
use App\Exports\JNMPExport;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Maatwebsite\Excel\Facades\Excel;
use Rappasoft\LaravelLivewireTables\Views\Filters\TextFilter;

class JnmpBeneficiaryListingDetailsDataTable extends DataTableComponent
{
    public ?int $perPage = 5;
    public $district = null;
    public int $serial = 0;
    public int $page = 1;

    public function mount($district = null): void
    {
        $this->district = $district ? (int) $district : null;
        $this->resetSerial();
    }

    public function configure(): void
    {
        $this->setPrimaryKey('application_id')
            ->setPaginationEnabled()
            ->setPerPageAccepted([5, 10])
            ->setPerPage($this->perPage)
            ->setPerPageVisibilityEnabled()
            ->setSearchEnabled()
            ->setSearchLive()
            ->setBulkActionsEnabled();

        $this->setHideBulkActionsWhenEmptyEnabled();

        // EXCEL EXPORT BUTTONS
        $this->setConfigurableAreas([
            'toolbar-left-start' => 'livewire.export_excel_buttons',
        ]);

        // TABLE STYLING
        $this->setTableWrapperAttributes([
            'class' => 'overflow-x-auto overflow-y-auto max-h-[500px] border rounded-lg shadow-sm',
        ]);

        $this->setTableAttributes([
            'class' => 'min-w-full text-sm text-gray-700 text-center overflow-x-auto',
        ]);

        $this->setTheadAttributes([
            'class' => 'bg-violet-800 text-xs uppercase py-3 px-4 text-white',
        ]);

        $this->setThAttributes(fn() => [
            'class' => 'px-4 py-3 text-white bg-violet-800 text-xs',
        ]);

        $this->setTdAttributes(fn() => [
            'class' => 'px-4 py-3 text-gray-700 text-center',
        ]);

        $this->setTbodyAttributes([
            'class' => 'px-4 py-3 divide-y divide-gray-200 bg-white overflow-y-auto',
        ]);
    }

    /** Reset Serial No. on Pagination Change */
    public function updatedPage()
    {
        $this->resetSerial();
    }

    public function resetSerial()
    {
        $this->serial = ($this->page - 1) * $this->perPage;
    }

    /** Filters **/
    public function filters(): array
    {
        return [
            TextFilter::make('Application ID')
                ->filter(function ($query, $value) {
                    $query->where('application_id', 'ILIKE', "%{$value}%");
                }),

            TextFilter::make('Applicant Name')
                ->filter(function ($query, $value) {
                    $query->where('beneficiary_name', 'ILIKE', "%{$value}%");
                }),
        ];
    }

    /** Table Columns **/
    public function columns(): array
    {
        return [

            Column::make("Sl No")
                ->label(fn() => ++$this->serial),

            Column::make("Application ID")
                ->label(fn($row) => $row->application_id ?? 'N/A'),

            Column::make("Beneficiary ID")
                ->label(fn($row) => $row->beneficiary_id ?? 'N/A'),

            Column::make("Name")
                ->label(fn($row) => $row->beneficiary_name ?? 'N/A'),

            Column::make("Address")
                ->label(fn($row) => $row->contact->getFullAddress() ?? 'N/A')
                ->html(),

            Column::make("Mobile No.")
                ->label(fn($row) => $row->mobile_no ?? 'N/A'),
        ];
    }

    /** Main Query Builder **/
    public function builder(): Builder
    {
        return BeneficiaryPersonalDetail::query()
            ->with(['contact', 'mapping', 'relationships'])

            // payment_suspend = 1
            ->whereHas('mapping', function ($q) {
                $q->where('payment_suspend', 1);
            })

            // District filter (contact table)
            ->when($this->district, function ($query) {
                $district = (int) $this->district;

                $query->whereHas('contact', function ($q) use ($district) {
                    $q->where('district_id', $district);
                });
            });
    }

    public function exportExcel()
    {
        $records = $this->builder()->get();

        $exportData = $records->map(function ($row) {

            $father = $row->relationships
                ->where('relation_type_id', Codemaster::getIdByCode(131))
                ->first();

            $address = $row->contact->getFullAddress() ?? 'N/A';
            $address = strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $address));

            return [
                'Application ID' => $row->application_id ?? 'N/A',
                'Beneficiary ID' => $row->beneficiary_id ?? 'N/A',
                'Full Name' => $row->beneficiary_name ?? 'N/A',
                'Father Name' => $father->full_name ?? 'N/A',
                'DOB' => $row->dob
                    ? \Carbon\Carbon::parse($row->dob)->format('d-m-Y')
                    : 'N/A',
                'Mobile No' => $row->mobile_no ?? 'N/A',
                'Address' => $address,
            ];
        });

        return Excel::download(new JNMPExport($exportData), 'jnmp_beneficiaries_all.xlsx');
    }
}
