<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('pension.personal_identification_number(_s)')) {
            Schema::table('pension.personal_identification_number(_s)', function (Blueprint $table) {
                if (!Schema::hasColumn('pension.personal_identification_number(_s)', 'krishak_bondhu_id')) {
                    $table->text('krishak_bondhu_id')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('pension.personal_identification_number(_s)')) {
            Schema::table('pension.personal_identification_number(_s)', function (Blueprint $table) {
                if (Schema::hasColumn('pension.personal_identification_number(_s)', 'krishak_bondhu_id')) {
                    $table->dropColumn('krishak_bondhu_id');
                }
            });
        }
    }
};
