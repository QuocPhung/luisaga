<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name']; // thêm nếu bạn cho phép nhập từ form

    public function users()
    {
        return $this->belongsToMany(User::class, 'role_user'); // Pivot table
    }
    public function scopeActive($query)
    {
        return $query->where('start_date', '<=', now())
                    ->where('end_date', '>=', now())
                    ->where('status', true);
    }

}
