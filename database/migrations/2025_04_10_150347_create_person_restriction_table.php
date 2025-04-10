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
        {
            Schema::create('person_restriction', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_credential');
                $table->unsignedBigInteger('id_person');
                $table->unsignedBigInteger('id_type_user');
                $table->timestamps();
    
                $table->foreign('id_credential')->references('id')->on('credential');
                $table->foreign('id_person')->references('id')->on('person');
                $table->foreign('id_type_user')->references('id')->on('type_user');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('person_restriction');
    }
};
