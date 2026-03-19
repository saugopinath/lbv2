<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ১. মডিউল মাস্টার টেবিল
        Schema::create('dynamic_workflow_modules', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('scheme_id')->index();
            $table->string('module_code', 60)->unique(); // e.g. BANK_UPD
            $table->string('module_name', 150);
            $table->unsignedInteger('step_count')->default(1);
            $table->jsonb('allowed_fields')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });

        // ২. স্টেপের নাম সংরক্ষণের জন্য আলাদা মাস্টার টেবিল
        Schema::create('dynamic_workflow_labels', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('scheme_id')->index();
            $table->foreignId('module_id')->constrained('dynamic_workflow_modules');
            $table->string('label_name', 150);
            $table->timestamps();
        });

        // ৩. ডাইনামিক স্টেপ কনফিগারেশন টেবিল
        Schema::create('dynamic_workflow_steps', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('scheme_id')->index();
            $table->foreignId('module_id')->constrained('dynamic_workflow_modules')->cascadeOnDelete();
            $table->foreignId('label_id')->constrained('dynamic_workflow_labels')->comment('Reference to Label Table');
            $table->unsignedInteger('rank')->comment('Order: 10, 20, 30...');
            $table->foreignId('role_id')->constrained('roles');
            $table->string('action_type', 50)->nullable();
            $table->unsignedInteger('success_rank')->nullable();
            $table->unsignedInteger('revert_rank')->nullable();
            $table->boolean('is_final_step')->default(false);
            $table->timestamps();

            $table->unique(['module_id', 'rank'], 'dw_module_rank_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dynamic_workflow_steps');
        Schema::dropIfExists('dynamic_workflow_labels');
        Schema::dropIfExists('dynamic_workflow_modules');
    }
};
