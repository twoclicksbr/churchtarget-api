<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_content', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_credential');
            $table->unsignedBigInteger('id_type_email');
            $table->string('subject');
            $table->string('banner_url')->nullable();
            $table->longText('body');
            $table->integer('active')->default(1);
            $table->timestamps();
        
            // 🔗 Relações
            $table->foreign('id_credential')->references('id')->on('credential')->onDelete('cascade');
            $table->foreign('id_type_email')->references('id')->on('type_email')->onDelete('cascade');
        });
        
    }

    public function down(): void
    {
        Schema::dropIfExists('email_content');
    }
};
