<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Services\Project\ProjectService;
use App\Http\Requests\Project\ProjectRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function index(): Response
    {
        $projects = ProjectService::listarTodos();
        return Inertia::render('Project/Index', [
            'projects' => $projects
        ]);
    }
    
    public function store(ProjectRequest $request): JsonResponse
    {
        $validated = $request->validated();
        
        $project = ProjectService::criar(
            $validated['name'],
            $validated['description'] ?? null,
            $validated['start_date'] ?? null,
            $validated['end_date'] ?? null
        );
        
        return response()->json($project, 201);
    }
    
    public function show(int $id): Response
    {
        $project = ProjectService::buscarPorId($id);
        
        if (!$project) {
            return Inertia::render('Error', [
                'status' => 404,
                'message' => 'Projeto não encontrado'
            ]);
        }
        
        return Inertia::render('Project/Show', [
            'project' => $project
        ]);
    }
    
    public function update(ProjectRequest $request, int $id): JsonResponse
    {
        $validated = $request->validated();
        
        $success = ProjectService::atualizar($id, $validated);
        
        if (!$success) {
            return response()->json(['message' => 'Projeto não encontrado'], 404);
        }
        
        return response()->json(['message' => 'Projeto atualizado com sucesso']);
    }
    
    public function destroy(int $id): JsonResponse
    {
        $success = ProjectService::excluir($id);
        
        if (!$success) {
            return response()->json(['message' => 'Projeto não encontrado'], 404);
        }
        
        return response()->json(['message' => 'Projeto excluído com sucesso']);
    }
    
    public function archive(int $id): JsonResponse
    {
        $success = ProjectService::arquivar($id);
        
        if (!$success) {
            return response()->json(['message' => 'Projeto não encontrado'], 404);
        }
        
        return response()->json(['message' => 'Projeto arquivado com sucesso']);
    }
    
    public function byStatus(string $status): JsonResponse
    {
        $projects = ProjectService::listarPorStatus($status);
        return response()->json($projects);
    }
    
    public function metrics(int $id): JsonResponse
    {
        $metrics = ProjectService::obterMetricas($id);
        
        if (empty($metrics)) {
            return response()->json(['message' => 'Projeto não encontrado'], 404);
        }
        
        return response()->json($metrics);
    }
}
