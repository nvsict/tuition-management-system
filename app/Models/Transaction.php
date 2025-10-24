<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    // Note: The table name is 'transactions', which Laravel will find automatically.

    protected $fillable = [
        'student_id',
        'description',
        'amount',
        'date',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the student that this transaction belongs to.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}