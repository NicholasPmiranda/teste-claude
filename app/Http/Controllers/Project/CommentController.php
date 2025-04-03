<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Services\Project\CommentService;
use App\Http\Requests\Project\CommentRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    public function index(int $task_id): JsonResponse
    {
        $comments = CommentService::listarPorTarefa($task_id);
        return response()->json($comments);
    }
    
    public function store(CommentRequest $request)
    {
        $validated = $request->validated();
        
        $comment = CommentService::criar(
            $validated['content'],
            $validated['task_id'],
            $validated['parent_id'] ?? null,
            $validated['mentioned_users'] ?? null
        );
        
        return redirect()->route('tasks.show', $validated['task_id'])->with('success', 'Comentário adicionado com sucesso');
    }
    
    public function show(int $id): JsonResponse
    {
        $comment = CommentService::buscarPorId($id);
        
        if (!$comment) {
            return response()->json(['message' => 'Comentário não encontrado'], 404);
        }
        
        return response()->json($comment);
    }
    
    public function update(CommentRequest $request, int $id)
    {
        $validated = $request->validated();
        $comment = CommentService::buscarPorId($id);
        $task_id = $comment ? $comment['task_id'] : null;
        
        $success = CommentService::atualizar(
            $id, 
            $validated['content'],
            $validated['mentioned_users'] ?? null
        );
        
        if (!$success) {
            return redirect()->back()->with('error', 'Comentário não encontrado');
        }
        
        return redirect()->route('tasks.show', $task_id)->with('success', 'Comentário atualizado com sucesso');
    }
    
    public function destroy(int $id)
    {
        $comment = CommentService::buscarPorId($id);
        $task_id = $comment ? $comment['task_id'] : null;
        
        $success = CommentService::excluir($id);
        
        if (!$success) {
            return redirect()->back()->with('error', 'Comentário não encontrado');
        }
        
        return redirect()->route('tasks.show', $task_id)->with('success', 'Comentário excluído com sucesso');
    }
}
