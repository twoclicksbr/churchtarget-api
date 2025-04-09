<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Mudar para o nome da tabela 
    private string $table = 'obs';

    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_credential');
            $table->unsignedBigInteger('id_person');
            $table->string('route'); // exemplo: type_user, person
            $table->unsignedBigInteger('id_parent'); // ID do registro relacionado
            $table->text('content'); // conteúdo da observação
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->table);
    }
};
