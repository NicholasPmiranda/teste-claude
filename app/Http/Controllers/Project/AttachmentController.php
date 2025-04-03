<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Services\Project\AttachmentService;
use App\Http\Requests\Project\AttachmentRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AttachmentController extends Controller
{
    public function index(int $task_id): JsonResponse
    {
        $attachments = AttachmentService::listarPorTarefa($task_id);
        return response()->json($attachments);
    }
    
    public function store(AttachmentRequest $request)
    {
        $validated = $request->validated();
        
        $attachment = AttachmentService::upload(
            $validated['file'],
            $validated['task_id']
        );
        
        return redirect()->route('tasks.show', $validated['task_id'])->with('success', 'Anexo enviado com sucesso');
    }
    
    public function show(int $id): JsonResponse
    {
        $attachment = AttachmentService::buscarPorId($id);
        
        if (!$attachment) {
            return response()->json(['message' => 'Anexo nu00e3o encontrado'], 404);
        }
        
        return response()->json($attachment);
    }
    
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|string|in:pending,approved,rejected'
        ]);
        
        $success = AttachmentService::atualizarStatus($id, $validated['status']);
        
        if (!$success) {
            return response()->json(['message' => 'Anexo nu00e3o encontrado'], 404);
        }
        
        return response()->json(['message' => 'Status do anexo atualizado com sucesso']);
    }
    
    public function destroy(int $id): JsonResponse
    {
        $success = AttachmentService::excluir($id);
        
        if (!$success) {
            return response()->json(['message' => 'Anexo nu00e3o encontrado'], 404);
        }
        
        return response()->json(['message' => 'Anexo excluu00eddo com sucesso']);
    }
}
