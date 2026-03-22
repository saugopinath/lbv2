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
        Schema::table('workflowstep_rolemappings', function (Blueprint $table) {
            // ১. ফরেন কী ড্রপ করা (অটোমেটিক নাম অনুমানে)
            $table->dropForeign(['workflow_step_id']);
            
            // ২. কলাম টাইপ পরিবর্তন এবং ফ্লেক্সিবল করা
            $table->bigInteger('workflow_step_id')->change();
            $table->bigInteger('same_label_role_id')->nullable()->change();
            $table->bigInteger('next_label_role_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('workflowstep_rolemappings', function (Blueprint $table) {
            $table->foreign('workflow_step_id')->references('id')->on('workflow_steps');
        });
    }
};
