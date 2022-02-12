<?php
 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserDataController;
use App\Http\Controllers\SearchController;
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

Auth::routes();
 
Route::get('/', function () {
    return view('welcome');
});
 

Route::get('/projects', function () {
    return view('projects');
});

Route::apiResource('projects', ProjectController::class);

Route::get('projects/get', [ProjectController::class, 'index']);

Route::get('/all', [ProjectController::class, 'showall']);

Route::get('user', [UserDataController::class, 'index1']);

Route::post('/store', [UserDataController::class, 'store']);

Route::get('user/getuser', [UserDataController::class, 'data']);

Route::get('show/{id}', [ProjectController::class, 'show']);

Route::post('update/{id}', [ProjectController::class, 'update']);

Route::delete('delete/{id}', [ProjectController::class, 'destroy']);

Route::get('/search',[SearchController::class, 'search'])->name('search');

Route::get('/searchData',[ProjectController::class,'search'])->name('searchData');

Route::get('/userDetails',[ProjectController::class,'userDetails'])->name('userDetails');

//Route::resource('/userData','UserDataController');
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


