<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;


    public $primaryKey = 'id';
    public $incrementing = true;

    public $timestamps = false;

    protected $fillable = [
        'area',
        'domain',
        'nobody_id',
        'data'
    ];
}
