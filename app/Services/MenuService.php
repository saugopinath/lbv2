<?php

namespace App\Services;

use App\Models\Menu;

class MenuService
{
    public function getUserMenus()
    {
        return Menu::whereNull('parent_id')

            ->where('is_active', true)

            ->with(['children' => function ($q) {

                $q->where('is_active', true)
                  ->orderBy('menu_rank');

            }])

            ->orderBy('menu_rank')

            ->get();
    }
}