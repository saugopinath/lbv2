<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lb_scheme.beneficiary_common_lists', function (Blueprint $table) {
            $table->boolean('is_bsk')->default(false)->after('is_reject');
        });
    }

    public function down(): void
    {
        Schema::table('lb_scheme.beneficiary_common_lists', function (Blueprint $table) {
            $table->dropColumn('is_bsk');
        });
    }
};
