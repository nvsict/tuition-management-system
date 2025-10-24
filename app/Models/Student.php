<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'roll_no',
        'class',
        'phone',
        'batch_id', // <-- ADD THIS
        'fees',
        'join_date',
    ];

    protected $casts = [
        'join_date' => 'date',
        'fees' => 'decimal:2',
    ];

    /**
     * Get all of the attendance records for the student.
     */
    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }
    public function batch()
{
    return $this->belongsTo(Batch::class);
}
/**
 * Get all of the transactions for the student.
 */
public function transactions()
{
    return $this->hasMany(Transaction::class)->orderBy('date', 'desc');
}
}