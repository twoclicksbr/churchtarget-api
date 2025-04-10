<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('person_user', function (Blueprint $table) {
            $table->dropUnique(['email']); // remove índice antigo
            $table->unique(['id_credential', 'email']); // cria índice composto
        });
    }

    public function down(): void
    {
        Schema::table('person_user', function (Blueprint $table) {
            $table->dropUnique(['id_credential', 'email']);
            $table->unique('email');
        });
    }

};
