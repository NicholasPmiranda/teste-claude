<?php

namespace App\Services\Project;

use App\Models\Project;
use Illuminate\Support\Facades\Auth;

class ProjectService
{
    public static function criar(string $name, ?string $description = null, ?string $start_date = null, ?string $end_date = null): Project
    {
        $project = new Project();
        $project->name = $name;
        $project->description = $description;
        $project->start_date = $start_date;
        $project->end_date = $end_date;
        $project->status = 'pending';
        $project->user_id = Auth::id();
        $project->save();
        
        return $project;
    }
    
    public static function atualizar(int $id, array $dados): bool
    {
        $project = Project::find($id);
        
        if (!$project) {
            return false;
        }
        
        return $project->update($dados);
    }
    
    public static function buscarPorId(int $id): ?Project
    {
        return Project::with(['tasks'])->find($id);
    }
    
    public static function listarTodos(): array
    {
        return Project::orderBy('created_at', 'desc')->get()->toArray();
    }
    
    public static function listarPorStatus(string $status): array
    {
        return Project::where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }
    
    public static function arquivar(int $id): bool
    {
        $project = Project::find($id);
        
        if (!$project) {
            return false;
        }
        
        $project->status = 'archived';
        return $project->save();
    }
    
    public static function excluir(int $id): bool
    {
        $project = Project::find($id);
        
        if (!$project) {
            return false;
        }
        
        return $project->delete();
    }
    
    public static function obterMetricas(int $id): array
    {
        $project = Project::with(['tasks'])->find($id);
        
        if (!$project) {
            return [];
        }
        
        $total_tasks = $project->tasks->count();
        $completed_tasks = $project->tasks->where('status', 'completed')->count();
        $in_progress_tasks = $project->tasks->where('status', 'in_progress')->count();
        $pending_tasks = $project->tasks->where('status', 'pending')->count();
        $blocked_tasks = $project->tasks->where('status', 'blocked')->count();
        
        $completion_percentage = $total_tasks > 0 ? round(($completed_tasks / $total_tasks) * 100) : 0;
        
        return [
            'total_tasks' => $total_tasks,
            'completed_tasks' => $completed_tasks,
            'in_progress_tasks' => $in_progress_tasks,
            'pending_tasks' => $pending_tasks,
            'blocked_tasks' => $blocked_tasks,
            'completion_percentage' => $completion_percentage
        ];
    }
}
