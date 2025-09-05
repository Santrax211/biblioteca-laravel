<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function __construct() {
        $this->middleware(['auth','role:bibliotecario']);
    }

    public function topBorrowed() {
        $rows = Book::select('books.id','books.title',
                DB::raw('COUNT(loans.id) AS total_prestamos'))
            ->leftJoin('loans','loans.book_id','=','books.id')
            ->groupBy('books.id','books.title')
            ->orderByDesc('total_prestamos')
            ->limit(10)
            ->get();

        return view('reports.top_borrowed', compact('rows'));
    }
}
