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
        Schema::create('person', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_credential');
            $table->string('name');
            $table->date('birthdate')->nullable();
            $table->unsignedBigInteger('id_type_gender');
            $table->unsignedBigInteger('id_type_group');
            $table->integer('active')->default(1);
            $table->timestamps();

            // Índices e chaves estrangeiras
            $table->foreign('id_credential')->references('id')->on('credential');
            $table->foreign('id_type_gender')->references('id')->on('type_gender');
            $table->foreign('id_type_group')->references('id')->on('type_group');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('person');
    }
};
