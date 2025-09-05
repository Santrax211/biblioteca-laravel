<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Models\{Book,Loan};


class LoanController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    // Estudiantes/Docentes crean su préstamo
    public function store(Request $request, Book $book) {
        $user = Auth::user();
        if (!$user->isRole('estudiante','docente')) abort(403);

        $request->validate([
            'days'=>['required','integer','min:1','max:30']
        ]);

        if ($book->stock_available < 1) {
            return back()->with('error','Sin stock disponible');
        }

        $loan = Loan::create([
            'user_id'=>$user->id,
            'book_id'=>$book->id,
            'loaned_at'=>now(),
            'due_at'=>now()->addDays($request->days),
            'status'=>'prestado'
        ]);

        $book->decrement('stock_available');

        return back()->with('ok','Préstamo registrado');
    }

    // Bibliotecario registra devolución
    public function return(Request $request, Loan $loan) {
        if (!Auth::user()->isRole('bibliotecario')) abort(403);
        if ($loan->status !== 'prestado') return back()->with('error','Ya devuelto');

        $loan->update([
            'returned_at'=>now(),
            'status'=>'devuelto'
        ]);

        $loan->book()->update(['stock_available'=>\DB::raw('stock_available + 1')]);

        return back()->with('ok','Devolución registrada');
    }
}
