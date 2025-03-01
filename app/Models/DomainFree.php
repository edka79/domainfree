<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DomainFree extends Model
{
    use HasFactory;

    protected $table = 'domain_free';
    protected $primaryKey = 'id';
    public $timestamps = false;

}
