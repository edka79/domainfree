<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expired extends Model
{
    use HasFactory;

    public $table = 'domain_import_expired';
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false;

}
