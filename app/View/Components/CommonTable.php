<?php

namespace App\View\Components;

use Illuminate\View\Component;

class CommonTable extends Component
{
    /**
     * @param array|\Illuminate\Support\Collection $rows  Prepared rows (array of arrays or collection of objects/arrays)
     * @param array $columns  Column definitions: [['label'=>'District','field'=>'district_id','sortable'=>true], ...]
     * @param int|null $page  current page number (optional)
     * @param int|null $perPage per page count (optional)
     * @param int|null $totalRows optional total count of groups (for display)
     * @param array|null $totals  optional totals to show in footer (assoc)
     * @param string|null $sortField current sort field name
     * @param string|null $sortDirection asc|desc
     */
    public $rows;
    public $columns;
    public $page;
    public $perPage;
    public $totalRows;
    public $totals;
    public $sortField;
    public $sortDirection;

    public function __construct($rows, $columns = [], $page = null, $perPage = null, $totalRows = null, $totals = null, $sortField = null, $sortDirection = null)
    {
        $this->rows = $rows;
        $this->columns = $columns;
        $this->page = $page;
        $this->perPage = $perPage;
        $this->totalRows = $totalRows;
        $this->totals = $totals;
        $this->sortField = $sortField;
        $this->sortDirection = $sortDirection;
    }

    public function render()
    {
        return view('components.common-table');
    }
}
