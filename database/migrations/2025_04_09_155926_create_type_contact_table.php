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
        Schema::create('type_contact', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_credential');
            $table->string('name')->unique();
            $table->string('input_type'); // Ex: tel, text, number, email, etc.
            $table->string('mask'); // Ex: (99) 9999-9999 ou (99) 99999-9999
            $table->integer('active')->default(1);
            $table->timestamps();
    
            // Foreign key
            $table->foreign('id_credential')->references('id')->on('credential');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('type_contact');
    }
};
