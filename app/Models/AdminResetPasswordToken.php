<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminResetPasswordToken extends Model
{
    use HasFactory;

    protected $guarded = 'id';
}
