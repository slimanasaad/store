<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelHasRole extends Model
{
    use HasFactory;

    protected $table = 'model_has_roles';

    public $timestamps = false; // هذا الجدول عادة لا يحتوي على created_at و updated_at

    protected $fillable = [
        'role_id',
        'model_type',
        'model_id',
        'center_id'
    ];

        
        
        
        
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
    public function model()
    {
        return $this->belongsTo(User::class, 'model_id');
    }
    public function center()
    {
        return $this->belongsTo(Center::class, 'center_id');
    }

}
