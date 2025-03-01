<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DomainImportRu extends Model
{
    use HasFactory;

    public $table = 'domain_import_ru';
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false;

}
