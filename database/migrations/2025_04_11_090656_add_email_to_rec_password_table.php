<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('rec_password', function (Blueprint $table) {
            $table->string('email')->after('id_person_user');
        });
    }

    public function down()
    {
        Schema::table('rec_password', function (Blueprint $table) {
            $table->dropColumn('email');
        });
    }
};
