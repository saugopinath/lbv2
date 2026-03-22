<?php
// app/Models/Menu.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

class Menu extends Model
{
    protected $fillable = [
        'name',
        'icon',
        'route',
        'url',
        'parent_id',
        'order',
        'is_active',
        'permission_key',
        'json_data'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'parent_id' => 'integer',
        'order' => 'integer',
        'json_data' => 'array'
    ];

    public function children(): HasMany
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('order');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'menu_role')
            ->withPivot('order', 'is_active')
            ->withTimestamps();
    }

    // Generate JSON data for this menu and its children
    public function generateJson()
    {
        $menuData = [
            'id' => $this->id,
            'name' => $this->name,
            'icon' => $this->icon,
            'route' => $this->route,
            'url' => $this->url,
            'permission_key' => $this->permission_key,
            'children' => []
        ];

        foreach ($this->children()->where('is_active', true)->orderBy('order')->get() as $child) {
            $menuData['children'][] = $child->generateJson();
        }

        return $menuData;
    }

    // Generate and save JSON for all menus
    public static function generateAllJson()
    {
        $menus = self::whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        $jsonData = [];
        foreach ($menus as $menu) {
            $jsonData[] = $menu->generateJson();
        }

        Storage::disk('local')->put(
            'menus.json',
            json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );


        return $jsonData;
    }
}
