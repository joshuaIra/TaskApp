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
        'rule_type',
        'interval',
        'weekdays_only',
    ];

    protected $casts = [
        'weekdays_only' => 'boolean',
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
