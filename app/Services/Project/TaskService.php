<?php

namespace App\Services\Project;

use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class TaskService
{
    public static function criar(
        string $title,
        int $project_id,
        ?string $description = null,
        ?string $start_date = null,
        ?string $due_date = null,
        ?string $priority = 'medium',
        ?float $estimated_hours = null,
        ?array $tags = null
    ): Task {
        $task = new Task();
        $task->title = $title;
        $task->description = $description;
        $task->project_id = $project_id;
        $task->user_id = Auth::id();
        $task->status = 'pending';
        $task->start_date = $start_date;
        $task->due_date = $due_date;
        $task->priority = $priority;
        $task->estimated_hours = $estimated_hours;
        $task->tags = $tags;
        $task->save();
        
        return $task;
    }
    
    public static function atualizar(int $id, array $dados): bool
    {
        $task = Task::find($id);
        
        if (!$task) {
            return false;
        }
        
        return $task->update($dados);
    }
    
    public static function buscarPorId(int $id): ?Task
    {
        return Task::with(['comments', 'attachments', 'dependencies', 'user'])->find($id);
    }
    
    public static function listarPorProjeto(int $project_id): array
    {
        return Task::where('project_id', $project_id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }
    
    public static function listarPorStatus(int $project_id, string $status): array
    {
        return Task::where('project_id', $project_id)
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }
    
    public static function listarPorPrioridade(int $project_id, string $priority): array
    {
        return Task::where('project_id', $project_id)
            ->where('priority', $priority)
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }
    
    public static function listarPorUsuario(int $user_id): array
    {
        return Task::where('user_id', $user_id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }
    
    public static function marcarComoCompleta(int $id): bool
    {
        $task = Task::find($id);
        
        if (!$task) {
            return false;
        }
        
        $task->status = 'completed';
        $task->completed_date = now();
        return $task->save();
    }
    
    public static function adicionarDependencia(int $task_id, int $dependency_id): bool
    {
        $task = Task::find($task_id);
        
        if (!$task) {
            return false;
        }
        
        $task->dependencies()->attach($dependency_id);
        return true;
    }
    
    public static function removerDependencia(int $task_id, int $dependency_id): bool
    {
        $task = Task::find($task_id);
        
        if (!$task) {
            return false;
        }
        
        $task->dependencies()->detach($dependency_id);
        return true;
    }
    
    public static function registrarHorasTrabalhadas(int $id, float $hours): bool
    {
        $task = Task::find($id);
        
        if (!$task) {
            return false;
        }
        
        $task->actual_hours = $task->actual_hours + $hours;
        return $task->save();
    }
    
    public static function excluir(int $id): bool
    {
        $task = Task::find($id);
        
        if (!$task) {
            return false;
        }
        
        return $task->delete();
    }
}
