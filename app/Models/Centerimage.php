<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Centerimage extends Model
{
    use HasFactory;
    protected $fillable = [
        'url',
        'center_id',
    ];
        
        
        
    public function center()
    {
        return $this->belongsTo(Center::class, 'center_id');
    }
}
