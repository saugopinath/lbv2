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
        $modelPath = app_path("Models/{$model}.php");

        // 1️⃣ Create model + migration
        if (!class_exists("App\\Models\\{$model}")) {
            Artisan::call("make:model {$model} -m");
        }

        // 2️⃣ Write migration (lb_scheme connection)
        $this->writeMigration($table, $fields);

        // 3️⃣ Update model (schema.table + fillable)
        $this->updateModel($modelPath, $table, $fields);

        // 4️⃣ Run migration
        Artisan::call('migrate', ['--force' => true]);
    }

    /**
     * Get schema name from connection config
     */
    private function getSchema(): string
    {
        return config('database.connections.lb_scheme.schema', 'public');
    }

    /**
     * Write migration file
     */
    private function writeMigration(string $table, array $fields): void
    {
        $migration = collect(File::files(database_path('migrations')))
            ->sortByDesc(fn($f) => $f->getCTime())
            ->first();
        $schema = $this->getSchema();
        $columns = '';

        foreach ($fields as $field) {
            $line = "\$table->{$field['column_type']}('{$field['db_column']}')";

            if ($field['nullable']) {
                $line .= '->nullable()';
            }

            if (!empty($field['default_value'])) {
                $line .= "->default('{$field['default_value']}')";
            }

            if ($field['key_type'] === 'unique') {
                $line .= '->unique()';
            }

            $columns .= "            {$line};\n";
        }

        $content = <<<PHP
            <?php

            use Illuminate\Database\Migrations\Migration;
            use Illuminate\Database\Schema\Blueprint;
            use Illuminate\Support\Facades\Schema;

            return new class extends Migration {
                public function up(): void
                {
                    Schema::create('{$schema}.{$table}', function (Blueprint \$table) {
                        \$table->id();
            {$columns}
                        \$table->timestamps();
                    });
                }

                public function down(): void
                {
                    Schema::dropIfExists('{$schema}.{$table}');
                }
            };
            PHP;

        File::put($migration->getPathname(), $content);
    }

    /**
     * Update model table + fillable
     */
    private function updateModel(string $modelPath, string $table, array $fields): void
    {
        if (!File::exists($modelPath)) {
            return;
        }

        $schema = $this->getSchema();

        $fillable = collect($fields)
            ->pluck('db_column')
            ->unique()
            ->values()
            ->map(fn($f) => "        '{$f}',")
            ->implode("\n");

        $tableBlock = "    protected \$table = '{$schema}.{$table}';\n\n";

        $fillableBlock = <<<PHP
    protected \$fillable = [
{$fillable}
    ];
PHP;

        $content = File::get($modelPath);

        /* ---------- TABLE ---------- */
        if (preg_match('/protected \$table\s*=/', $content)) {
            $content = preg_replace(
                '/protected \$table\s*=\s*[\'"][^\'"]+[\'"]\s*;/m',
                trim($tableBlock),
                $content
            );
        } else {
            $content = preg_replace(
                '/class\s+\w+\s+extends\s+Model\s*\{/m',
                "$0\n{$tableBlock}",
                $content
            );
        }

        /* ---------- FILLABLE ---------- */
        if (preg_match('/protected \$fillable\s*=\s*\[[\s\S]*?\];/m', $content)) {
            $content = preg_replace(
                '/protected \$fillable\s*=\s*\[[\s\S]*?\];/m',
                trim($fillableBlock),
                $content
            );
        } else {
            $content = preg_replace(
                '/class\s+\w+\s+extends\s+Model\s*\{/m',
                "$0\n{$fillableBlock}",
                $content
            );
        }

        File::put($modelPath, $content);
    }
}
