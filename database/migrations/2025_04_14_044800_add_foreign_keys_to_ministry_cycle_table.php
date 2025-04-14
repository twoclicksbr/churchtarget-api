<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ministry_cycle', function (Blueprint $table) {
            $table->foreign('id_credential')->references('id')->on('credential')->onDelete('cascade');
            $table->foreign('id_ministry')->references('id')->on('ministry')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('ministry_cycle', function (Blueprint $table) {
            $table->dropForeign(['id_credential']);
            $table->dropForeign(['id_ministry']);
        });
    }

};
