<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('type_email_config', function (Blueprint $table) {
            $table->string('template')->nullable()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('type_email_config', function (Blueprint $table) {
            $table->dropColumn('template');
        });
    }
};
