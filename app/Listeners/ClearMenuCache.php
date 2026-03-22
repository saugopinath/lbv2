<?php
// app/Listeners/ClearMenuCache.php
namespace App\Listeners;

use App\Services\MenuService;

class ClearMenuCache
{
    protected $menuService;
    
    public function __construct(MenuService $menuService)
    {
        $this->menuService = $menuService;
    }
    
    public function handle($event)
    {
        if (isset($event->user)) {
            $this->menuService->clearUserMenuCache($event->user->id);
        } else {
            $this->menuService->clearAllMenuCache();
        }
    }
}