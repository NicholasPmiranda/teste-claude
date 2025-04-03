<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Services\Project\TaskService;
use App\Http\Requests\Project\TaskRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TaskController extends Controller
{
    public function index(int $project_id): Response
    {
        $tasks = TaskService::listarPorProjeto($project_id);
        return Inertia::render('Project/Task/Index', [
            'project_id' => $project_id,
            'tasks' => $tasks
        ]);
    }
    
    public function store(TaskRequest $request)
    {
        $validated = $request->validated();
        
        $task = TaskService::criar(
            $validated['title'],
            $validated['project_id'],
            $validated['description'] ?? null,
            $validated['start_date'] ?? null,
            $validated['due_date'] ?? null,
            $validated['priority'] ?? 'medium',
            $validated['estimated_hours'] ?? null,
            $validated['tags'] ?? null
        );
        
        return redirect()->route('projects.tasks.index', $validated['project_id'])->with('success', 'Tarefa criada com sucesso');
    }
    
    public function show(int $id): Response
    {
        $task = TaskService::buscarPorId($id);
        
        if (!$task) {
            return Inertia::render('Error', [
                'status' => 404,
                'message' => 'Tarefa não encontrada'
            ]);
        }
        
        return Inertia::render('Project/Task/Show', [
            'task' => $task
        ]);
    }
    
    public function update(TaskRequest $request, int $id)
    {
        $validated = $request->validated();
        
        $success = TaskService::atualizar($id, $validated);
        
        if (!$success) {
            return redirect()->back()->with('error', 'Tarefa não encontrada');
        }
        
        return redirect()->route('tasks.show', $id)->with('success', 'Tarefa atualizada com sucesso');
    }
    
    public function destroy(int $id)
    {
        $task = TaskService::buscarPorId($id);
        $project_id = $task ? $task['project_id'] : null;
        
        $success = TaskService::excluir($id);
        
        if (!$success) {
            return redirect()->back()->with('error', 'Tarefa não encontrada');
        }
        
        return redirect()->route('projects.tasks.index', $project_id)->with('success', 'Tarefa excluída com sucesso');
    }
    
    public function complete(int $id)
    {
        $success = TaskService::marcarComoCompleta($id);
        
        if (!$success) {
            return redirect()->back()->with('error', 'Tarefa não encontrada');
        }
        
        return redirect()->route('tasks.show', $id)->with('success', 'Tarefa marcada como completa');
    }
    
    public function byStatus(int $project_id, string $status): Response
    {
        $tasks = TaskService::listarPorStatus($project_id, $status);
        return Inertia::render('Project/Task/ByStatus', [
            'project_id' => $project_id,
            'status' => $status,
            'tasks' => $tasks
        ]);
    }
    
    public function byPriority(int $project_id, string $priority): Response
    {
        $tasks = TaskService::listarPorPrioridade($project_id, $priority);
        return Inertia::render('Project/Task/ByPriority', [
            'project_id' => $project_id,
            'priority' => $priority,
            'tasks' => $tasks
        ]);
    }
    
    public function byUser(int $user_id): Response
    {
        $tasks = TaskService::listarPorUsuario($user_id);
        return Inertia::render('Project/Task/ByUser', [
            'user_id' => $user_id,
            'tasks' => $tasks
        ]);
    }
    
    public function addDependency(int $task_id, int $dependency_id)
    {
        $success = TaskService::adicionarDependencia($task_id, $dependency_id);
        
        if (!$success) {
            return redirect()->back()->with('error', 'Tarefa não encontrada');
        }
        
        return redirect()->route('tasks.show', $task_id)->with('success', 'Dependência adicionada com sucesso');
    }
    
    public function removeDependency(int $task_id, int $dependency_id)
    {
        $success = TaskService::removerDependencia($task_id, $dependency_id);
        
        if (!$success) {
            return redirect()->back()->with('error', 'Tarefa não encontrada');
        }
        
        return redirect()->route('tasks.show', $task_id)->with('success', 'Dependência removida com sucesso');
    }
    
    public function logHours(Request $request, int $id)
    {
        $validated = $request->validate([
            'hours' => 'required|numeric|min:0.1'
        ]);
        
        $success = TaskService::registrarHorasTrabalhadas($id, $validated['hours']);
        
        if (!$success) {
            return redirect()->back()->with('error', 'Tarefa não encontrada');
        }
        
        return redirect()->route('tasks.show', $id)->with('success', 'Horas registradas com sucesso');
    }
}
