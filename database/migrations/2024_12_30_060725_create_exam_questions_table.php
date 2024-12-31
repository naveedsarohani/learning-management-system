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
        Schema::create('exam_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->text('question_text');
            $table->json('answers');
            $table->string('correct_option');
            $table->float('carry_marks', 5, 2)->unsigned()->default(1.00);
            $table->timestamps();
        });
    }

    /**
     * Run the reversals.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_questions');
    }
};
