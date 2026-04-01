<?php

namespace App\Services;

use App\Exports\DynamicTableExport;
use Maatwebsite\Excel\Facades\Excel;

class TableExportService
{
    public function export($component, $fileName = 'export.xlsx')
    {
        // Skip Actions column
        $columns = collect($component->columns())
            ->filter(fn ($col) => $col->getTitle() !== 'Actions')
            ->map(fn ($col) => [
                'title' => $col->getTitle(),
                'field' => $col->getField(),
            ])
            ->values();

        $rows = $component->builder()->get();

        $data = $rows->map(function ($row) use ($columns) {

            $rowData = [];

            foreach ($columns as $col) {

                $field = $col['field'];
                $title = $col['title'];

                $rowData[$title] = $row->$field ?? 'N/A';
            }

            return $rowData;
        });

        $fileNameWithTime = pathinfo($fileName, PATHINFO_FILENAME).'_'.now()->format('Y_m_d_H_i_s').'.xlsx';

        return Excel::download(
            new DynamicTableExport(
                $data,
                $columns->pluck('title')->toArray()
            ),
            $fileNameWithTime
        );
    }
}
