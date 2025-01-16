<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'question_text',
        'answers',
        'correct_option',
        'carry_marks',
    ];

    protected $casts = [
        'answers' => 'array',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
}
