<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('self_decleration_basefields', function (Blueprint $table) {
            $table->integer('section_level_type')
                  ->nullable()
                  ->after('section_level_id');

            $table->boolean('is_under_section')
                  ->default(false)
                  ->after('section_level_type');
        });
    }

    public function down(): void
    {
        Schema::table('self_decleration_basefields', function (Blueprint $table) {
            $table->dropColumn([
                'section_level_type',
                'is_under_section',
            ]);
        });
    }
};
