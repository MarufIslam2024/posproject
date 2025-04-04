<?php

namespace App\Models;
use App\Models\User;
use App\Models\Category;
use App\Models\InvoiceProduct;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //
    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'price',
        'unit',
        'image',
    ];
    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function invoiceProducts(){
        return $this->hasMany(InvoiceProduct::class);
    }
}
