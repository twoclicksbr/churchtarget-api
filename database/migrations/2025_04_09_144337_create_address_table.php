<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('address', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_credential');
            $table->string('route');                       // Origem do registro (ex: person, church)
            $table->unsignedBigInteger('id_parent');       // ID do registro vinculado
            $table->unsignedBigInteger('id_type_address'); // Tipo de endereço

            $table->string('cep');
            $table->string('logradouro');
            $table->string('numero');
            $table->string('complemento')->nullable();
            $table->string('bairro');
            $table->string('localidade');
            $table->string('uf');

            $table->integer('active')->default(1);
            $table->timestamps();

            // Foreign Keys
            $table->foreign('id_credential')->references('id')->on('credential');
            $table->foreign('id_type_address')->references('id')->on('type_address');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('address');
    }
};
