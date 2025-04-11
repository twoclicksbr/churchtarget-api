<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('share', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_credential');
            $table->unsignedBigInteger('id_type_share');
            $table->unsignedBigInteger('id_type_gender');
            $table->unsignedBigInteger('id_type_participation');
            $table->unsignedBigInteger('id_person_leader');
            $table->string('link');
            $table->integer('active')->default(1);
            $table->timestamps();

            $table->foreign('id_credential')->references('id')->on('credential');
            $table->foreign('id_type_share')->references('id')->on('type_share');
            $table->foreign('id_type_gender')->references('id')->on('type_gender');
            $table->foreign('id_type_participation')->references('id')->on('type_participation');
            $table->foreign('id_person_leader')->references('id')->on('person');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('share');
    }
};
