<?php

namespace App\Livewire\Frontend\Home;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use App\Models\Notification;
use Illuminate\Database\Eloquent\Builder;

class NotificationTable extends DataTableComponent
{
    protected $model = Notification::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setPerPageAccepted([6, 12, 24, 50, -1])
            ->setPerPage(6)
            ->setTheadAttributes([
                'class' => 'hidden', // hides headers so it looks like a list
            ])
            ->setTableAttributes([
                'class' => 'w-full',
            ])
            ->setThAttributes(function(Column $column) {
                return ['class' => 'hidden'];
            })
            ->setTdAttributes(function(Column $column, $row, $columnIndex, $rowIndex) {
                return [
                    'class' => 'p-0 border-0',
                ];
            });
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')->hideIf(true),
            
            Column::make('Notification')
                ->label(
                    fn($row, Column $column) => view('frontend.home.partials.notification_row', ['row' => $row])
                )
                ->html(),
                
            Column::make('Type', 'type')->searchable()->hideIf(true),
            Column::make('Title', 'title')->searchable()->hideIf(true),
            Column::make('Message', 'message')->searchable()->hideIf(true),
            Column::make('Scheme Name', 'scheme_name')->searchable()->hideIf(true),
            Column::make('Notified At', 'notified_at')->sortable()->hideIf(true),
        ];
    }

    public function filters(): array
    {
        return [
            SelectFilter::make('Filter by Type', 'type')
                ->options([
                    '' => 'All',
                    'important' => 'Important',
                    'scheme_update' => 'Scheme Updates',
                    'application_status' => 'Application Status',
                ])
                ->filter(function(Builder $builder, string $value) {
                    $builder->where('type', $value);
                }),
        ];
    }

    public function builder(): Builder
    {
        return Notification::query()->orderBy('notified_at', 'desc');
    }
}
