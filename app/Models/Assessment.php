<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function submission()
    {
        return $this->hasOne(Submission::class, 'assessment_id', 'id');
    }

    public function question()
    {
        return $this->hasMany(Question::class);
    }
}
