<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Project\ProjectController;
use App\Http\Controllers\Project\TaskController;
use App\Http\Controllers\Project\CommentController;
use App\Http\Controllers\Project\AttachmentController;

// Rotas de projetos
Route::middleware(['auth'])->group(function () {
    Route::prefix('projects')->group(function () {
        Route::get('/', [ProjectController::class, 'index'])->name('projects.index');
        Route::post('/', [ProjectController::class, 'store'])->name('projects.store');
        Route::get('/{id}', [ProjectController::class, 'show'])->name('projects.show');
        Route::put('/{id}', [ProjectController::class, 'update'])->name('projects.update');
        Route::delete('/{id}', [ProjectController::class, 'destroy'])->name('projects.destroy');
        Route::patch('/{id}/archive', [ProjectController::class, 'archive'])->name('projects.archive');
        Route::get('/status/{status}', [ProjectController::class, 'byStatus'])->name('projects.by-status');
        Route::get('/{id}/metrics', [ProjectController::class, 'metrics'])->name('projects.metrics');
        
        // Rotas de tarefas dentro do contexto de projeto
        Route::get('/{project_id}/tasks', [TaskController::class, 'index'])->name('projects.tasks.index');
        Route::get('/{project_id}/tasks/status/{status}', [TaskController::class, 'byStatus'])->name('projects.tasks.by-status');
        Route::get('/{project_id}/tasks/priority/{priority}', [TaskController::class, 'byPriority'])->name('projects.tasks.by-priority');
    });

    // Rotas de tarefas
    Route::prefix('tasks')->group(function () {
        Route::post('/', [TaskController::class, 'store'])->name('tasks.store');
        Route::get('/{id}', [TaskController::class, 'show'])->name('tasks.show');
        Route::put('/{id}', [TaskController::class, 'update'])->name('tasks.update');
        Route::delete('/{id}', [TaskController::class, 'destroy'])->name('tasks.destroy');
        Route::patch('/{id}/complete', [TaskController::class, 'complete'])->name('tasks.complete');
        Route::post('/{id}/log-hours', [TaskController::class, 'logHours'])->name('tasks.log-hours');
        Route::get('/user/{user_id}', [TaskController::class, 'byUser'])->name('tasks.by-user');
        
        // Rotas de dependências
        Route::post('/{task_id}/dependencies/{dependency_id}', [TaskController::class, 'addDependency'])->name('tasks.dependencies.add');
        Route::delete('/{task_id}/dependencies/{dependency_id}', [TaskController::class, 'removeDependency'])->name('tasks.dependencies.remove');
        
        // Rotas de comentários no contexto de tarefa
        Route::get('/{task_id}/comments', [CommentController::class, 'index'])->name('tasks.comments.index');
        
        // Rotas de anexos no contexto de tarefa
        Route::get('/{task_id}/attachments', [AttachmentController::class, 'index'])->name('tasks.attachments.index');
    });

    // Rotas de comentários
    Route::prefix('comments')->group(function () {
        Route::post('/', [CommentController::class, 'store'])->name('comments.store');
        Route::get('/{id}', [CommentController::class, 'show'])->name('comments.show');
        Route::put('/{id}', [CommentController::class, 'update'])->name('comments.update');
        Route::delete('/{id}', [CommentController::class, 'destroy'])->name('comments.destroy');
    });

    // Rotas de anexos
    Route::prefix('attachments')->group(function () {
        Route::post('/', [AttachmentController::class, 'store'])->name('attachments.store');
        Route::get('/{id}', [AttachmentController::class, 'show'])->name('attachments.show');
        Route::patch('/{id}/status', [AttachmentController::class, 'updateStatus'])->name('attachments.update-status');
        Route::delete('/{id}', [AttachmentController::class, 'destroy'])->name('attachments.destroy');
    });
});
