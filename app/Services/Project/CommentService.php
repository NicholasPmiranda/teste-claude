<?php

namespace App\Services\Project;

use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class CommentService
{
    public static function criar(string $content, int $task_id, ?int $parent_id = null, ?array $mentioned_users = null): Comment
    {
        return Comment::create([
            'content' => $content,
            'task_id' => $task_id,
            'user_id' => Auth::id(),
            'parent_id' => $parent_id,
            'mentioned_users' => $mentioned_users
        ]);
    }
    
    public static function atualizar(int $id, string $content, ?array $mentioned_users = null): bool
    {
        $comment = Comment::find($id);
        
        if (!$comment) {
            return false;
        }
        
        // Salvar histórico de edição
        $edit_history = $comment->edit_history ?? [];
        $edit_history[] = [
            'content' => $comment->content,
            'edited_at' => now()->toDateTimeString(),
            'edited_by' => Auth::id()
        ];
        
        $comment->content = $content;
        $comment->mentioned_users = $mentioned_users;
        $comment->edit_history = $edit_history;
        
        return $comment->save();
    }
    
    public static function buscarPorId(int $id): ?Comment
    {
        return Comment::with(['user', 'replies', 'replies.user'])->find($id);
    }
    
    public static function listarPorTarefa(int $task_id): array
    {
        return Comment::where('task_id', $task_id)
            ->whereNull('parent_id')
            ->with(['user', 'replies', 'replies.user'])
            ->orderBy('created_at', 'asc')
            ->get()
            ->toArray();
    }
    
    public static function excluir(int $id): bool
    {
        $comment = Comment::find($id);
        
        if (!$comment) {
            return false;
        }
        
        return $comment->delete();
    }
}
