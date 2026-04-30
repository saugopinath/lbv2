<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;

class FormOptionHelper
{
    public static function get(string $key): array
    {
        $options = Cache::rememberForever('form_options_json', function () {
            $path = public_path('js/form-options.json');

            if (! file_exists($path)) {
                return [];
            }

            return json_decode(file_get_contents($path), true);
        });

        return $options[$key] ?? [];
    }

    public static function label(string $key, $value, string $default = 'Unknown'): string
    {
        $options = self::get($key);

        return $options[(int) $value] ?? $default;
    }
}
