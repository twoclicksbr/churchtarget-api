<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('media_library', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_credential');
            $table->string('name');
            $table->string('url');
            $table->string('path');
            $table->string('type')->nullable(); // image, pdf, video etc
            $table->integer('size')->nullable(); // em bytes
            $table->integer('active')->default(1);
            $table->timestamps();

            $table->foreign('id_credential')->references('id')->on('credential')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_library');
    }
};
