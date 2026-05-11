<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('pension.personal_identification_number(_s)', function (Blueprint $table) {

        $table->id();
        $table->unsignedBigInteger('scheme_id');
          $table->unsignedBigInteger('application_id')->unique();
$table->unsignedBigInteger('beneficiary_id')->unique();
                      $table->integer('digital_ration_card_number')->nullable();
            $table->integer('card_number')->nullable();
            $table->integer('aadhaar_number')->nullable();
            $table->text('pan')->nullable();
            $table->text('e_p_i_c/_voter_id_no')->nullable();
            $table->text('a_h_l_t_i_n')->nullable();
            $table->text('b_p_l_seq_no')->nullable();
            $table->text('b_p_l_id_no')->nullable();
            $table->text('b_p_l_total_score')->nullable();

        $table->jsonb('other_details')->nullable();
        $table->timestamps();
          

        $table->foreign('application_id', 'application_id_fk')
                ->references('application_id')
                ->on('pension.unique_app_ben_ids')
                ->cascadeOnDelete();

        // $table->foreign('beneficiary_id', 'beneficiary_id_fk')
        //         ->references('beneficiary_id')
        //         ->on('pension.unique_app_ben_ids')
        //         ->cascadeOnDelete();
        $table->foreign('scheme_id', 'scheme_id_fk')
                ->references('id')
                ->on('public.schemes')
                ->cascadeOnDelete();
        
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pension.personal_identification_number(_s)');
    }
};