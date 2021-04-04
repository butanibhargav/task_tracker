<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [TaskController::class,'index']);
Route::resource('task',TaskController::class);
// complete task
Route::get('complete_task/{id}',[TaskController::class,'toggle_complete'])->name('complete_task');
// add subtask
Route::get('create_subtask/{id}',[TaskController::class,'create_subtask'])->name('create.subtask');
Route::post('create_subtask/{id}',[TaskController::class,'create_subtask'])->name('create.subtask');
// clone task
Route::get('clone/{task}',[TaskController::class,'clone'])->name('task.clone');
// track task time
Route::post('track',[TaskController::class,'track'])->name('track.task');
