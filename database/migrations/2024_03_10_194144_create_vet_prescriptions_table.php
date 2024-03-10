<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vet_prescriptions', function (Blueprint $table) {
            $table->id();
            $table->text('diagnosis');
            $table->text('observations');
            $table->text('indications');
            $table->unsignedBigInteger('vet_id');
            $table->unsignedBigInteger('vet_appointment_id');
            $table->foreign('vet_id')->references('id')->on('users');
            $table->foreign('vet_appointment_id')->references('id')->on('vet_appointments');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vet_prescriptions');
    }
};
