<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        DB::statement('CREATE SCHEMA IF NOT EXISTS jnmp');
        Schema::create('jnmp.jnmp_data', function (Blueprint $table) {

            $table->integer('slno')->nullable();
            $table->integer('applicationid')->nullable();
            $table->string('genderdesc')->nullable();
            $table->string('deceased_agetypedesc')->nullable();
            $table->integer('deceased_age')->nullable();
            $table->string('deceased_firstname')->nullable();
            $table->string('deceased_middlename')->nullable();
            $table->string('deceased_lastname')->nullable();
            $table->string('deceasedfullname')->nullable();
            $table->integer('deceased_idprooftyp')->nullable();
            $table->string('deceased_idprooftypname')->nullable();
            $table->integer('deceasedkhadyosathicategoryid')->nullable();
            $table->string('deceasedkhadyosathicatdesc')->nullable();
            $table->string('deceased_idproofnumber')->nullable();
            $table->string('present_districtname')->nullable();
            $table->string('present_isblockorulbdesc')->nullable();
            $table->string('present_blockmunicipalitydesc')->nullable();
            $table->integer('present_pin')->nullable();
            $table->string('present_grampanchayatdesc')->nullable();
            $table->string('present_villagetowndesc')->nullable();
            $table->string('certificateno')->nullable();
            $table->string('reportingdate')->nullable();
            $table->string('dateofdeath')->nullable();
            $table->timestamp('fetching_time')->nullable();
            $table->smallInteger('running_id')->nullable();
            $table->smallInteger('is_details_callback')->nullable();
            $table->timestampTz('details_callback_at')->nullable();
            $table->date('from_date')->nullable();
            $table->date('to_date')->nullable();
            $table->integer('jnm_id')->nullable();
            $table->string('aadhaar_hash')->nullable();
            $table->integer('lb_application_id')->nullable();
            $table->smallInteger('migrated_to_jb')->nullable();
            $table->timestampTz('marking_application_at')->nullable();
            $table->smallInteger('migrated_to_payment')->nullable();

            // $table->foreign('applicationid', 'applicationid_fk')->references('sourceable_id')->on('lb_scheme.beneficiary_common_lists')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('jnmp.jnmp_data');
    }
};
