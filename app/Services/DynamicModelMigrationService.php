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

        if (!class_exists("App\\Models\\{$model}")) {
            Artisan::call("make:model {$model} -m");
        }
        $this->writeMigration($table, $fields);
        $this->updateModel($modelPath, $table, $fields);
        Artisan::call('migrate', ['--force' => false]);
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
    // private function writeMigration(string $table, array $fields): void
    // {
    //     $migration = collect(File::files(database_path('migrations')))
    //         ->sortByDesc(fn($f) => $f->getCTime())
    //         ->first();
    //     $schema = $this->getSchema();
    //     $columns = '';
    //     foreach ($fields as $field) {
    //         $line = "\$table->{$field['column_type']}('{$field['column_name']}')";

    //         if ($field['nullable']) {
    //             $line .= '->nullable()';
    //         }
    //         if (!empty($field['default_value'])) {
    //             $line .= "->default('{$field['default_value']}')";
    //         }
    //         if ($field['key_type'] === 'unique') {
    //             $line .= '->unique()';
    //         }
    //         $columns .= "            {$line};\n";
    //     }
    //     $content = <<<PHP
    //         <?php
    //         use Illuminate\Database\Migrations\Migration;
    //         use Illuminate\Database\Schema\Blueprint;
    //         use Illuminate\Support\Facades\Schema;
    //         return new class extends Migration {
    //             public function up(): void
    //             {
    //                 Schema::create('{$schema}.{$table}', function (Blueprint \$table) {
    //                     \$table->id();
    //                     \$table->unsignedBigInteger('application_id')->unique();
    //                     \$table->unsignedBigInteger('beneficiary_id')->unique();
    //         {$columns}
    //                     \$table->timestamps();
    //                     \$table->foreign('application_id', 'application_id_fk')->references('application_id')->on('lb_scheme.unique_app_ben_ids');
    //                     \$table->foreign('beneficiary_id', 'beneficiary_id_fk')->references('beneficiary_id')->on('lb_scheme.unique_app_ben_ids');
    //                 });
    //             }
    //             public function down(): void
    //             {
    //                 Schema::dropIfExists('{$schema}.{$table}');
    //             }
    //         };
    //         PHP;

    //     File::put($migration->getPathname(), $content);
    // }

    private function writeMigration(string $table, array $fields): void
    {
        $migration = collect(File::files(database_path('migrations')))
            ->sortByDesc(fn($f) => $f->getCTime())
            ->first();

        $schema = $this->getSchema();

        $columns = '';
        $foreignKeys = [];
        $indexes = [];



        foreach ($fields as $field) {

            /* ---------------- COLUMN TYPE + LENGTH ---------------- */
            if ($field['column_type'] === 'string' && !empty($field['length'])) {
                $line = "\$table->string('{$field['column_name']}', {$field['length']})";
            } else {
                $line = "\$table->{$field['column_type']}('{$field['column_name']}')";
            }
            /* ---------------- NULLABLE ---------------- */
            if (!empty($field['nullable'])) {
                $line .= '->nullable()';
            }
            /* ---------------- DEFAULT ---------------- */
            if (
                array_key_exists('default_value', $field) &&
                $field['default_value'] !== null &&
                $field['default_value'] !== ''
            ) {
                $default = $field['default_value'];
                if (is_numeric($default)) {
                    $line .= "->default({$default})";
                } elseif (in_array($default, ['true', 'false'], true)) {
                    $line .= "->default(" . ($default === 'true' ? 'true' : 'false') . ")";
                } else {
                    $line .= "->default('" . addslashes($default) . "')";
                }
            }
            /* ---------------- UNIQUE / PRIMARY ---------------- */
            if (($field['key_type'] ?? null) === 'unique') {
                $line .= '->unique()';
            }
            // if (($field['key_type'] ?? null) === 'primary') {
            //     $line .= '->primary()';
            // }
            $columns .= "            {$line};\n";
            /* ---------------- FOREIGN KEY ---------------- */
            if (($field['key_type'] ?? null) === 'foreign') {
                $fkTable  = $field['fk_table'];
                $fkColumn = $field['fk_column'];
                if (!empty($field['key_name'])) {
                    $key_name = $field['key_name'];
                    $fk = "            \$table->foreign('{$field['column_name']}', '{$key_name}')" .
                        "->references('{$fkColumn}')" .
                        "->on('{$fkTable}')";
                } else {
                    $fk = "            \$table->foreign('{$field['column_name']}')" .
                        "->references('{$fkColumn}')" .
                        "->on('{$fkTable}')";
                }


                if (!empty($field['nullable'])) {
                    $fk .= "->nullOnDelete();";
                } else {
                    $fk .= "->cascadeOnDelete();";
                }
                $foreignKeys[] = $fk;
            }
            /* ---------------- INDEX ---------------- */
            if (($field['key_type'] ?? null) === 'index') {
                $key_name = $field['key_name'];
                if (!empty($field['key_name'])) {
                    $key_name = $field['key_name'];
                    $indexes[] = "            \$table->index('{$field['column_name']}', '{$key_name}');";
                } else {
                    $indexes[] = "            \$table->index('{$field['column_name']}');";
                }
            }
        }

        $foreignKeyBlock = implode("\n", $foreignKeys);
        $indexBlock = implode("\n", $indexes);

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

                        \$table->unsignedBigInteger('application_id')->unique();
                        \$table->unsignedBigInteger('beneficiary_id')->unique();

            {$columns}

                        \$table->timestamps();

            {$foreignKeyBlock}

                        \$table->foreign('application_id', 'application_id_fk')
                            ->references('application_id')
                            ->on('lb_scheme.unique_app_ben_ids')
                            ->cascadeOnDelete();

                        \$table->foreign('beneficiary_id', 'beneficiary_id_fk')
                            ->references('beneficiary_id')
                            ->on('lb_scheme.unique_app_ben_ids')
                            ->cascadeOnDelete();

            {$indexBlock}
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


    private function updateModel(string $modelPath, string $table, array $fields): void
    {
        if (!File::exists($modelPath)) {
            return;
        }
        $schema = $this->getSchema();
        $tableBlock = "    protected \$table = '{$schema}.{$table}';\n\n";
        $fillable = collect($fields)
            ->pluck('column_name')
            ->unique()
            ->values()
            ->map(fn($f) => "        '{$f}',")
            ->implode("\n");

        $fillableBlock = <<<PHP
            protected \$fillable = [{$fillable}];
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
