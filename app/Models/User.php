<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;
        
    public function roles()
    {
        return $this->morphToMany(
            Role::class,
            'model',
            'model_has_roles',
            'model_id',
            'role_id'
        )->withPivot('center_id'); // ← هذا مهم جداً
    }
            
        
        
        
public function centers()
{
    return $this->belongsToMany(
        \App\Models\Center::class,
        'model_has_roles',
        'model_id',    // مفتاح المستخدم
        'center_id'    // مفتاح المركز
    )->wherePivot('model_type', self::class) // تأكد أنه مستخدم وليس موديل آخر
     ->distinct(); // حتى لا تتكرر نفس المركز لو للمستخدم أكثر من دور فيه
}

    public function image()
    {
        return $this->belongsTo(Userimage::class, 'image_id');
    }
        
        
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'phone',
        'email',
        'password',
        'is_center_admin',
        'image_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
