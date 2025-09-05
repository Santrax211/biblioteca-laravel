<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = ['user_id','book_id','loaned_at','due_at','returned_at','status'];
    protected $casts = ['loaned_at'=>'datetime','due_at'=>'datetime','returned_at'=>'datetime'];
    public function user(){ return $this->belongsTo(User::class); }
    public function book(){ return $this->belongsTo(Book::class); }
}
