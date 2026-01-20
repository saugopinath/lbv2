<?php

namespace App\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class DynamicModelMigrationService
{
    public function generate(string $tabName, array $fields): void
    {
        $model = Str::studly(Str::singular($tabName));
        $table = Str::snake($tabName);
        // Create model + migration together
        if (!class_exists("App\\Models\\{$model}")) {
            Artisan::call("make:model {$model} -m");
        }
        // Write migration schema
        $this->writeMigration($table, $fields);
        // Run migration
        // Artisan::call('migrate');
    }


    private function writeMigration(string $table, array $fields): void
    {
        $migration = collect(File::files(database_path('migrations')))
            ->sortByDesc(fn($f) => $f->getCTime())
            ->first();

        $schema = '';

        foreach ($fields as $field) {
            $type = $field['column_type'];
            $name = $field['db_column'];

            $line = "\$table->{$type}('{$name}')";

            if ($field['nullable']) {
                $line .= '->nullable()';
            }

            if (!empty($field['default_value'])) {
                $line .= "->default('{$field['default_value']}')";
            }

            if ($field['key_type'] === 'unique') {
                $line .= '->unique()';
            }

            $schema .= "            {$line};\n";
        }

        $content = <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('{$table}', function (Blueprint \$table) {
            \$table->id();
{$schema}
            \$table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('{$table}');
    }
};
PHP;

        File::put($migration->getPathname(), $content);
    }
}
