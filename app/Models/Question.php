<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    public $timestamp = false;

    public $table = 'questions';

    protected $fillable = [

        'assessment_id',
        'question_text',
        'type'
    ];

    public function assessment()
    {
        return $this->belongsTo(Assessment::class, 'assessment_id', 'id');
    }

    public function answer()
    {
        return $this->hasOne(Answer::class);
    }
}
