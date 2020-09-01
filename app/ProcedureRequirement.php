<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProcedureRequirement extends Model
{
    use SoftDeletes;

    protected $fillable = ['cost', 'procedure_id', 'requirement_id'];
}
