<?php

namespace App\Http\Controllers;

use App\Http\Requests\{BookStoreRequest,BookUpdateRequest};
use App\Models\{Book,Author,Category};
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function __construct() {
        $this->middleware(['auth','role:bibliotecario']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) 
    {
        $q = $request->query('q');
        $books = Book::with(['authors','category'])
            ->when($q, fn($qr)=>$qr->where(fn($w)=>$w
                ->where('title','like',"%$q%")
                ->orWhere('isbn','like',"%$q%")
            ))
            ->orderBy('title')
            ->paginate(10)
            ->withQueryString();

        return view('books.index', compact('books','q'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('books.create', [
            'authors'=>Author::orderBy('name')->get(),
            'categories'=>Category::orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validated();
        $data['stock_available'] = $data['stock_total'];
        $book = Book::create($data);
        $book->authors()->sync($data['authors']);
        return redirect()->route('books.index')->with('ok','Libro creado');
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Book $book)
    {
        return view('books.edit', [
            'book'=>$book->load('authors'),
            'authors'=>Author::orderBy('name')->get(),
            'categories'=>Category::orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Book $book)
    {
        $prevTotal = $book->stock_total;
        $data = $request->validated();
        // Ajustar stock_available si cambia el total
        if ($data['stock_total'] != $prevTotal) {
            $diff = $data['stock_total'] - $prevTotal;
            $book->stock_available = max(0, $book->stock_available + $diff);
        }
        $book->fill($data)->save();
        $book->authors()->sync($data['authors']);
        return redirect()->route('books.index')->with('ok','Libro actualizado');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        if ($book->loans()->where('status','prestado')->exists()) {
            return back()->with('error','No se puede eliminar: tiene préstamos activos');
        }
        $book->delete();
        return redirect()->route('books.index')->with('ok','Libro eliminado');
    }
}
