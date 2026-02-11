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
        Schema::create('workflowstep_rolemappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scheme_id')
                ->constrained('schemes')
                ->cascadeOnDelete();
            $table->foreignId('workflow_step_id')
                ->constrained('workflow_steps')
                ->cascadeOnDelete();
            $table->bigInteger('rank')->nullable();
            $table->foreignId('role_id')
                ->constrained('roles')
                ->cascadeOnDelete();
            $table->bigInteger('same_label_role_id');
            $table->bigInteger('next_label_role_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflowstep_rolemappings');
    }
};
