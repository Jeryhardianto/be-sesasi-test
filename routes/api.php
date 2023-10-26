<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NoteController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::prefix('v1')->group(function () {
    // Auth
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login'])->name('login');
    Route::post('auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
   
    Route::group(['middleware' => ['auth:sanctum','role:admin']], function () {
            // Route untuk admin
            Route::get('/admin', function () {
                return response()->json(['message' => 'Admin Page', 'status' => true]);
            });
            // Auth
            Route::post('auth/registerbyadmin', [AuthController::class, 'registerByAdmin']);

            // Notes
            Route::get('/admin/notes', [NoteController::class, 'getAllNote']);
            Route::get('/admin/notes{id}', [NoteController::class, 'getNoteById']);
            Route::post('/admin/notes', [NoteController::class, 'store']);
            Route::put('/admin/notes{id}', [NoteController::class, 'update']);
            Route::delete('/admin/notes{id}', [NoteController::class, 'destroy']);

    });

    Route::group(['middleware' => ['auth:sanctum','role:editor']], function () {
        // Route untuk editor
        Route::get('/editor', function () {
            return response()->json(['message' => 'Editor Page', 'status' => true]);
        });
         // Notes
         Route::get('/editor/notes', [NoteController::class, 'getAllNote']);
         Route::get('/editor/notes/{id}', [NoteController::class, 'getNoteById']);
         Route::put('/editor/notes/{id}', [NoteController::class, 'update']);
       

    });

    Route::group(['middleware' => ['auth:sanctum','role:user']], function () {
        // Route untuk user
        Route::get('/user', function () {
            return response()->json(['message' => 'User Page', 'status' => true]);
        });

        // Notes
        Route::get('/notes', [NoteController::class, 'getAllNote']);
        Route::get('/notes/{id}', [NoteController::class, 'getNoteById']);
        Route::post('/notes', [NoteController::class, 'store']);
        Route::put('/notes/{id}', [NoteController::class, 'update']);
        Route::delete('/notes/{id}', [NoteController::class, 'destroy']);
    });

    
});

