<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nik', 16)->nullable()->after('name');
            $table->string('nim', 14)->nullable()->after('nik');
            $table->string('phone_number', 20)->nullable()->after('nim');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nik', 'nim', 'phone_number']);
        });
    }
};
