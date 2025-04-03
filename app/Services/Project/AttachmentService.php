<?php

namespace App\Services\Project;

use App\Models\Attachment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AttachmentService
{
    public static function upload(UploadedFile $file, int $task_id): Attachment
    {
        $original_filename = $file->getClientOriginalName();
        $filename = time() . '_' . $original_filename;
        $file_path = $file->storeAs('attachments/tasks/' . $task_id, $filename, 'public');
        
        $attachment = new Attachment();
        $attachment->filename = $filename;
        $attachment->original_filename = $original_filename;
        $attachment->file_path = $file_path;
        $attachment->mime_type = $file->getMimeType();
        $attachment->file_size = $file->getSize();
        $attachment->task_id = $task_id;
        $attachment->user_id = Auth::id();
        $attachment->status = 'pending';
        $attachment->version = self::getNextVersion($task_id, $original_filename);
        $attachment->save();
        
        return $attachment;
    }
    
    public static function getNextVersion(int $task_id, string $original_filename): int
    {
        $latest_version = Attachment::where('task_id', $task_id)
            ->where('original_filename', $original_filename)
            ->max('version');
            
        return $latest_version ? $latest_version + 1 : 1;
    }
    
    public static function atualizarStatus(int $id, string $status): bool
    {
        $attachment = Attachment::find($id);
        
        if (!$attachment) {
            return false;
        }
        
        $attachment->status = $status;
        return $attachment->save();
    }
    
    public static function buscarPorId(int $id): ?Attachment
    {
        return Attachment::with(['user'])->find($id);
    }
    
    public static function listarPorTarefa(int $task_id): array
    {
        return Attachment::where('task_id', $task_id)
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }
    
    public static function excluir(int $id): bool
    {
        $attachment = Attachment::find($id);
        
        if (!$attachment) {
            return false;
        }
        
        // Remover arquivo do storage
        Storage::disk('public')->delete($attachment->file_path);
        
        return $attachment->delete();
    }
}
