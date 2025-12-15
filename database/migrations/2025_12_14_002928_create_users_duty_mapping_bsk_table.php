<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('CREATE SCHEMA IF NOT EXISTS bsk');
        Schema::create('bsk.users_duty_mapping_bsk', function (Blueprint $table) {
            $table->id();

            $table->string('name', 191);
            $table->string('email', 191);
            $table->string('mobile_no', 10)->nullable();

            $table->smallInteger('is_active')->default(1);

            $table->string('bsk_name', 191)->nullable();
            $table->string('bsk_code', 191)->nullable();
            $table->string('ohr_code', 191)->nullable();
            $table->string('deo_code', 191)->nullable();

            $table->integer('district_id')->nullable();
            $table->string('district_name', 191)->nullable();

            $table->char('is_rural', 1)->nullable();

            $table->integer('sub_division_id')->nullable();
            $table->string('sub_district_name', 191)->nullable();

            $table->integer('block_id')->nullable();
            $table->string('block_name', 191)->nullable();

            $table->timestamp('deleted_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->string('password', 191)->default('123456');
            $table->string('remember_token', 100)->nullable();

            $table->integer('agent_id')->nullable();
            $table->integer('id_from_bsk')->nullable();

            $table->string('ticket_no', 50)->nullable();
            $table->string('username', 200)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bsk.users_duty_mapping_bsk');
    }
};
