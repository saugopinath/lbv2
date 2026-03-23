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
        Schema::table('dynamic_workflow_requests', function (Blueprint $table) {
            $table->dropForeign(['current_step_id']);
            
            $table->foreign('current_step_id')
                ->references('id')
                ->on('workflowstep_rolemappings')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('dynamic_workflow_requests', function (Blueprint $table) {
            $table->dropForeign(['current_step_id']);
            $table->foreign('current_step_id')->references('id')->on('dynamic_workflow_steps');
        });
    }
};
