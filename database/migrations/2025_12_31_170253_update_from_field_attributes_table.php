<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('from_field_attributes', function (Blueprint $table) {
            $table->bigInteger('dependent_on')->nullable();
            $table->jsonb('dependent_on_values')->nullable();
             $table->string('field_class', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('from_field_attributes', function (Blueprint $table) {
            $table->dropColumn('dependent_on');
            $table->dropColumn('dependent_on_values');
            $table->dropColumn('field_class');
        });
    }
};
