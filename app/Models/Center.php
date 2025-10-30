<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Center extends Model
{
    use HasFactory;

    public function users()
    {
        return $this->hasMany(User::class);
    }

    protected $fillable = [
        'name',
        'location',
        'owner_id',

    ];
        
        
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
        
    public function center_image()
    {
        return $this->hasMany(Centerimage::class);
    }

}
