<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'title','isbn','year','category_id','stock_total','stock_available'
    ];
    public function category(){ return $this->belongsTo(Category::class); }
    public function authors(){ return $this->belongsToMany(Author::class); }
    public function loans(){ return $this->hasMany(Loan::class); }
}
