<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('credential', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('token');
            $table->integer('active')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credential');
    }
};
