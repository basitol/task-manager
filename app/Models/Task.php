<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class Task extends Model
{
    //
    protected $fillable = [
        'task_name',
        'description',
        'status',
        'due_date',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusAttribute($value)
    {
        return ucfirst($value);
    }

    public function setStatusAttribute($value)
    {
        $this->attributes['status'] = strtolower($value);
    }

    public function getDueDateAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d');
    }

    public function setDueDateAttribute($value)
    {
        $this->attributes['due_date'] = Carbon::parse($value)->format('Y-m-d');
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    public function getTaskNameAttribute($value)
    {
        return ucfirst($value);
    }

    public function setTaskNameAttribute($value)
    {
        $this->attributes['task_name'] = strtolower($value);
    }

    public function getDescriptionAttribute($value)
    {
        return ucfirst($value);
    }

    public function setDescriptionAttribute($value)
    {
        $this->attributes['description'] = strtolower($value);
    }
}       
