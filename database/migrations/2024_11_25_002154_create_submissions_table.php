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
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->references('id')->on('assessments')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('student_id')->references('id')->on('users')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->integer('score')->default(0);
            $table->tinyInteger('retake_count', unsigned: true);
            $table->dateTime('submitted_at')->default(now());
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
