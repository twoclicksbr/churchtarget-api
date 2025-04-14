<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ministry_person_registered', function (Blueprint $table) {
            $table->foreign('id_credential')->references('id')->on('credential')->onDelete('cascade');
            $table->foreign('id_ministry_cycle')->references('id')->on('ministry_cycle')->onDelete('cascade');
            $table->foreign('id_person')->references('id')->on('person')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('ministry_person_registered', function (Blueprint $table) {
            $table->dropForeign(['id_credential']);
            $table->dropForeign(['id_ministry_cycle']);
            $table->dropForeign(['id_person']);
        });
    }

};
