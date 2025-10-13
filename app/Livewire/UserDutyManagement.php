<?php

namespace App\Livewire;

use App\Models\User;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class UserDutyManagement extends DataTableComponent
{
   
    protected $model = User::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id')
             ->setPaginationEnabled()
             ->setPerPage(10)
             ->setSearchEnabled()
             ->setSearchLive();
    }

    public function columns(): array
    {
        return [
             Column::make("Display Name", "name")->searchable()->sortable(),
            Column::make("Role", "role")->searchable(),
            Column::make("Mobile Number", "mobile")->searchable(),
            Column::make("Email", "email")->searchable(),
            Column::make("Location", "location")->searchable(),
            Column::make("Active?", "is_active")->searchable(),
        ];
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.user-management-table');
    }
}
