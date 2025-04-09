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
        Schema::create('contact', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_credential');
            $table->string('route');                         // Origem (ex: person, church)
            $table->unsignedBigInteger('id_parent');         // ID do registro vinculado
            $table->unsignedBigInteger('id_type_contact');   // Tipo de contato
            $table->string('value');                         // Ex: número, email, @instagram
            $table->integer('active')->default(1);
            $table->timestamps();
    
            // Foreign Keys
            $table->foreign('id_credential')->references('id')->on('credential');
            $table->foreign('id_type_contact')->references('id')->on('type_contact');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact');
    }
};
