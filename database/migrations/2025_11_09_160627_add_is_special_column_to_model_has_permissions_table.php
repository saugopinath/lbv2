<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
      public function up()
    {
        Schema::table('model_has_permissions', function (Blueprint $table) {
            $table->string('is_special', 1)
                  ->default('1')   // default value = 1 (special)
                  ->after('model_id');
        });
    }

    public function down()
    {
        Schema::table('model_has_permissions', function (Blueprint $table) {
            $table->dropColumn('is_special');
        });
    }
};
