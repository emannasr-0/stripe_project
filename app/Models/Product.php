<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
         'price',
          'quantity',
          'user_id'
        ];

   public function user(): BelongsTo  
       {  
        return $this->belongsTo(User::class);  
         } 
    use HasFactory;
}
