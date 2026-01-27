<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('lb_scheme.pan_details', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('application_id')->unique();
            $table->unsignedBigInteger('beneficiary_id')->unique();

            $table->string('name', 50);
            $table->string('pan_no', 12)->unique();
            $table->text('address')->nullable();
            $table->integer('issue_from');
            $table->string('is_expire', 5)->default(1);
            $table->date('issue_date');


            $table->timestamps();

            $table->foreign('issue_from', 'issues_from_fk')->references('lgd_code')->on('public.districts')->cascadeOnDelete();
            $table->foreign('issue_date')->references('application_id')->on('lb_scheme.beneficiary_personals')->cascadeOnDelete();

            $table->foreign('application_id', 'application_id_fk')
                ->references('application_id')
                ->on('lb_scheme.unique_app_ben_ids')
                ->cascadeOnDelete();

            $table->foreign('beneficiary_id', 'beneficiary_id_fk')
                ->references('beneficiary_id')
                ->on('lb_scheme.unique_app_ben_ids')
                ->cascadeOnDelete();


        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lb_scheme.pan_details');
    }
};