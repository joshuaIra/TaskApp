<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReminderRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'user_id',
        'frequency',
        'every_n',
        'weekdays_only',
        'time_of_day',
        'active',
    ];

    protected $casts = [
        'weekdays_only' => 'boolean',
        'every_n' => 'integer',
        'active' => 'boolean',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
