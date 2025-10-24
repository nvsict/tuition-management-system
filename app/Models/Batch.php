<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'time_slot'];

    /**
     * Get the students associated with this batch.
     */
    public function students()
    {
        return $this->hasMany(Student::class);
    }
}