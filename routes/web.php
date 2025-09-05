<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    BookController, AuthorController, CategoryController,
    LoanController, ReportController
};

Route::get('/', fn()=>redirect()->route('books.public'))
    ->name('home');

// Listado público de libros (con búsqueda/paginación)
Route::get('/catalogo', function(\Illuminate\Http\Request $request){
    $q = $request->query('q');
    $books = \App\Models\Book::with('authors','category')
        ->when($q, fn($qr)=>$qr->where(fn($w)=>$w
            ->where('title','like',"%$q%")
            ->orWhere('isbn','like',"%$q%")))
        ->orderBy('title')
        ->paginate(10)->withQueryString();
    return view('public.catalog', compact('books','q'));
})->name('books.public');

Route::middleware(['auth','role:bibliotecario'])->group(function(){
    Route::resource('authors', AuthorController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('books', BookController::class)->except('show');
    Route::get('reportes/top-prestados', [ReportController::class,'topBorrowed'])
        ->name('reports.top-borrowed');
});

// Préstamos (crear: estudiante/docente)
Route::middleware(['auth','role:estudiante,docente'])->group(function(){
    Route::post('books/{book}/loans', [LoanController::class,'store'])
        ->name('loans.store');
});

// Devoluciones (registrar: bibliotecario)
Route::middleware(['auth','role:bibliotecario'])->group(function(){
    Route::post('loans/{loan}/return', [LoanController::class,'return'])
        ->name('loans.return');
});

require __DIR__.'/auth.php';
