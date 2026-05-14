<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ben_payment_details', function (Blueprint $table) {
            $table->id();

            $table->integer('dist_code')->nullable();
            $table->integer('ben_id')->nullable();

            foreach ([
                'apr', 'may', 'jun', 'jul',
                'aug', 'sep', 'oct', 'nov',
                'dec', 'jan', 'feb', 'mar',
            ] as $month) {
                $table->decimal("{$month}_lot_no", 15, 2)->nullable();
                $table->string("{$month}_lot_type", 20)->nullable();
                $table->string("{$month}_lot_status", 20)->nullable();
            }

            $table->smallInteger('start_yymm')->nullable();
            $table->string('fin_year', 10)->nullable();

            $table->decimal('openning_due_amt', 15, 2)->nullable();
            $table->smallInteger('openning_due_count')->nullable();

            $table->decimal('present_amt', 15, 2)->nullable();
            $table->smallInteger('present_count')->nullable();

            $table->string('last_accno', 30)->nullable();
            $table->string('last_ifsc', 20)->nullable();
            $table->string('ben_status', 20)->nullable();
            $table->string('ben_name', 150)->nullable();

            $table->string('caste', 50)->nullable();
            $table->string('acc_validated', 10)->nullable();
            $table->integer('local_body_code')->nullable();

            $table->timestamps(); // created_at, updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ben_payment_details');
    }
};
