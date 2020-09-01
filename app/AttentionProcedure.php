<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttentionProcedure extends Model
{
    use SoftDeletes;

    protected $fillable = ['dependency_id', 'procedure_id', 'attention_type_id'];
}
