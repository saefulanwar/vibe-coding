<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create course_batches table
        Schema::create('course_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->string('name');
            $table->integer('moodle_group_id')->nullable();
            $table->integer('quota');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->dateTime('registration_end_date');
            $table->timestamps();
        });

        // 2. Modify orders table: remove course_id and add course_batch_id
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
            $table->dropColumn('course_id');
            $table->foreignId('course_batch_id')->after('user_id')->constrained('course_batches')->cascadeOnDelete();
        });

        // 3. Modify enrollments table: remove course_id and add course_batch_id
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
            $table->dropColumn('course_id');
            $table->foreignId('course_batch_id')->after('user_id')->constrained('course_batches')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Reverse changes on enrollments table
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropForeign(['course_batch_id']);
            $table->dropColumn('course_batch_id');
            $table->foreignId('course_id')->after('user_id')->constrained('courses')->cascadeOnDelete();
        });

        // 2. Reverse changes on orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['course_batch_id']);
            $table->dropColumn('course_batch_id');
            $table->foreignId('course_id')->after('user_id')->constrained('courses')->cascadeOnDelete();
        });

        // 3. Drop course_batches table
        Schema::dropIfExists('course_batches');
    }
};
