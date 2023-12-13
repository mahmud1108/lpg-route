<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    use HasFactory;

    public $incrementing = false;

    protected $primaryKey = 'admin_id';

    protected $guarded = 'admin_id';

    public function location()
    {
        $this->hasMany(Location::class, 'admin_id', 'admin_id');
    }
}
