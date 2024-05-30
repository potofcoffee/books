<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\DeweyClassController;
use App\Http\Controllers\LabelController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

Route::get('/', function () {
    return redirect('/dashboard');

    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', [BookController::class, 'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');


/// BOOKS

Route::match(['POST', 'GET'], '/search', [BookController::class, 'search'])->name('search');
Route::get('/book/{book}', [BookController::class, 'edit'])->name('book.edit');
Route::patch('/book/{book}', [BookController::class, 'update'])->name('book.update');
Route::get('/add/{isbn?}', [BookController::class, 'create'])->name('book.add');
Route::match(['GET', 'POST'], '/query/{isbn}', [BookController::class, 'query'])->name('query');
Route::post('/add', [BookController::class, 'store'])->name('book.store');
Route::delete('/book/{book}', [BookController::class, 'destroy'])->name('book.delete');


/// DEWEY

Route::get('/api/dewey/ddc/{deweyClass:class}', [DeweyClassController::class, 'apiShow'])->name('api.dewey.show');
Route::get('/api/dewey/tree', [DeweyClassController::class, 'apiTree'])->name('api.dewey.tree');
Route::get('/api/dewey/flat', [DeweyClassController::class, 'apiFlat'])->name('api.dewey.flat');
Route::get('/api/dewey/search/{q}', [DeweyClassController::class, 'apiSearch'])->name('api.dewey.search');

Route::get('/labels/form', [LabelController::class, 'form'])->name('labels.form');
Route::get('/labels/print/{skipLabels}', [LabelController::class, 'print'])->name('labels.print');
Route::post('/labels/printed/{skipLabels}', [LabelController::class, 'printed'])->name('labels.printed');


require __DIR__.'/auth.php';
