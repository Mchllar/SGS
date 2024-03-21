<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupervisorAllocation extends Model
{
    protected $fillable = [
        'start_date',
        'end_date',
        'notes',
        'contract',
        'student_id',
        'supervisor_id',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

}
