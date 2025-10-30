<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'price',
        'currency',    
        'center_id',
        'quantity',
            
    ];

        
    public function product_image()
    {
        return $this->hasMany(Productimage::class);
    }
        
    public function center()
    {
        return $this->belongsTo(Center::class, 'center_id');
    }
       

}
