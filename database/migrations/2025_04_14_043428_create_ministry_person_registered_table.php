<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ministry_person_registered', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_credential');
            $table->unsignedBigInteger('id_ministry_cycle');
            $table->unsignedBigInteger('id_person');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ministry_person_registered');
    }

};
