<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DomainImportExpired extends Model
{
    use HasFactory;

    public $table = 'domain_import_expired';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'load_count',
        'load_content',
    ];



}
