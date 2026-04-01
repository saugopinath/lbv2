<?php

namespace App\Services;

use App\Exports\DynamicTableExport;
use Maatwebsite\Excel\Facades\Excel;

class TableExportService
{
    public function export($component, $fileName = 'export.xlsx')
    {
        $columns = collect($component->columns())
            ->filter(fn ($col) => $col->getTitle() !== 'Actions')
            ->values();
       
        $rows = $component->getRows();

        $data = $rows->map(function ($row) use ($columns) {

            $rowData = [];

            foreach ($columns as $column) {

                $title = $column->getTitle();

                $labelCallback = $column->getLabelCallback();

                if ($labelCallback) {

                    $value = call_user_func(
                        $labelCallback,
                        $row,
                        $column
                    );

                } else {

                    $field = $column->getField();

                    $value = $row->$field ?? 'N/A';
                }

                $rowData[$title] = strip_tags($value);
            }

            return $rowData;
        });

        $headings = $columns
            ->map(fn ($col) => $col->getTitle())
            ->toArray();

        $fileNameWithTime =
            pathinfo($fileName, PATHINFO_FILENAME)
            .'_'.now()->format('Y_m_d_H_i_s')
            .'.xlsx';

        return Excel::download(
            new DynamicTableExport(
                $data,
                $headings
            ),
            $fileNameWithTime
        );
    }
}
