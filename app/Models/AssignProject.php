<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignProject extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'employee_id',
        'assign_by',
        'assign_date',
    ];
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function employee()
    {
        return $this->belongsTo(User::class);
    }
}
