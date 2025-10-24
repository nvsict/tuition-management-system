<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'date',
        'status', // 'present' or 'absent'
    ];

    protected $casts = [
        'date' => 'date',
    ];
    protected $table = 'attendance'; // <-- ADD THIS LINE
    /**
     * Get the student that this attendance record belongs to.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}