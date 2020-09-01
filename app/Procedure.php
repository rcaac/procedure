<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Procedure extends Model
{
    use SoftDeletes;

    protected $fillable = ['module_id', 'procedure_qualification_id', 'description', 'note', 'legal_base', 'term'];
}
