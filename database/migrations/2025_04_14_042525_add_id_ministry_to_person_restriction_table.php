<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('person_restriction', function (Blueprint $table) {
            $table->unsignedBigInteger('id_ministry')->after('id_person');
        });
    }

    public function down()
    {
        Schema::table('person_restriction', function (Blueprint $table) {
            $table->dropColumn('id_ministry');
        });
    }
};
