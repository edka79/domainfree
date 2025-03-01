<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersNobody extends Model
{
    use HasFactory;

    protected $table = 'users_nobody';
    protected $primaryKey = 'id';
    public $timestamps = false;

}
