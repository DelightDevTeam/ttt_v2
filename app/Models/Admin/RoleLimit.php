<?php

namespace App\Models\Admin;

use App\Models\Admin\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleLimit extends Model
{
    use HasFactory;

    protected $fillable = ['role_id', 'limit'];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }
}
