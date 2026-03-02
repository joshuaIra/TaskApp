<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->index('status', 'tasks_status_idx');
            $table->index('priority', 'tasks_priority_idx');
            $table->index('due_at', 'tasks_due_at_idx');
            $table->index('creator_id', 'tasks_creator_id_idx');
            $table->index('deleted_at', 'tasks_deleted_at_idx');
            $table->index(['status', 'priority', 'due_at'], 'tasks_status_priority_due_at_idx');
        });

        Schema::table('task_user', function (Blueprint $table) {
            $table->index('user_id', 'task_user_user_id_idx');
            $table->index('task_id', 'task_user_task_id_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_user', function (Blueprint $table) {
            $table->dropIndex('task_user_user_id_idx');
            $table->dropIndex('task_user_task_id_idx');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex('tasks_status_idx');
            $table->dropIndex('tasks_priority_idx');
            $table->dropIndex('tasks_due_at_idx');
            $table->dropIndex('tasks_creator_id_idx');
            $table->dropIndex('tasks_deleted_at_idx');
            $table->dropIndex('tasks_status_priority_due_at_idx');
        });
    }
};
