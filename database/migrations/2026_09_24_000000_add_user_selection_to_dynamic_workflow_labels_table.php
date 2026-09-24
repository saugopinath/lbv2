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
        Schema::table('dynamic_workflow_labels', function (Blueprint $table) {
            $table->boolean('assign_specific_users')->default(false)->after('permissions');
            $table->json('user_ids')->nullable()->after('assign_specific_users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dynamic_workflow_labels', function (Blueprint $table) {
            $table->dropColumn(['assign_specific_users', 'user_ids']);
        });
    }
};
