<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('email_config', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_credential');
            $table->unsignedBigInteger('id_ministry');
            $table->unsignedBigInteger('id_type_email_config');

            $table->string('banner_url')->nullable();
            $table->string('events')->nullable();
            $table->string('client_name')->nullable();

            $table->integer('active')->default(1);
            $table->timestamps();

            $table->foreign('id_credential')->references('id')->on('credential')->onDelete('cascade');
            $table->foreign('id_ministry')->references('id')->on('ministry')->onDelete('cascade');
            $table->foreign('id_type_email_config')->references('id')->on('type_email_config')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_config');
    }
};
