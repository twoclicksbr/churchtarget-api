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
        Schema::create('document', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_credential');
            $table->unsignedBigInteger('id_person');
            $table->unsignedBigInteger('id_type_document');
            $table->string('value');
            $table->integer('active')->default(1);
            $table->timestamps();
    
            $table->foreign('id_credential')->references('id')->on('credential')->onDelete('cascade');
            $table->foreign('id_person')->references('id')->on('person')->onDelete('cascade');
            $table->foreign('id_type_document')->references('id')->on('type_document')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document');
    }
};
