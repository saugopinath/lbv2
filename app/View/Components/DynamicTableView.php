<?php

namespace App\View\Components;

use Illuminate\View\Component;

class DynamicTableView extends Component
{
    public string $header;
    public array $helper;
    public array $columns;
    public array $data;

    public array $rows;
    public array $totals;
    public string $baseLabel;

    public function __construct(
        string $header = 'Report',
        array $helper = [],
        array $columns = [],
        array $data = [],
    ) {
        $this->header = $header;
        $this->helper = $helper;
        $this->columns = $this->normalizeColumns($columns);
        $this->data = $data;

        // rows normalized
        $this->rows = array_map(fn($r) => (array)$r, $data);

        // compute totals only for numeric columns
        $this->totals = $this->computeTotals();
        $this->baseLabel = $this->getFormattedModeLabel();
    }
     public function getFormattedModeLabel(): string
    {
        $mode = $this->helper['mode'] ?? null;
        if (!$mode) {
            return 'Report';
        }
        // Base mode labels with emojis
        $modeLabels = [
            'block_subdivision' => 'Block / Subdivision',
            'district' => 'District',
            'block' => 'Block',
            'subdivision' => 'Subdivision',
            'panchayat' => 'Panchayat',
            'municipality' => 'Municipality',
            'ward' => 'Ward',
            ];

        // Get base label
        $baseLabel = $modeLabels[$mode] ?? $mode;
        return  $baseLabel ;
    }

    private function normalizeColumns(array $cols): array
    {
        return array_map(function ($c) {
            return array_merge([
                'key' => $c['key'] ?? null,
                'label' => $c['label'] ?? ucfirst($c['key']),
                'align' => $c['align'] ?? 'left',
                'type' => $c['type'] ?? 'text',
                'show_total' => $c['show_total'] ?? false,
            ], $c);
        }, $cols);
    }

    private function computeTotals(): array
    {
        $totals = [];
        foreach ($this->columns as $col) {
            if (!empty($col['show_total'])) {
                $key = $col['key'];
                $totals[$key] = array_sum(array_map(fn($r) => $r[$key] ?? 0, $this->rows));
            }
        }
        return $totals;
    }

    public function render()
    {
        return view('components.dynamic-table-view');
    }
}
