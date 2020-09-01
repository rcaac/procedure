<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttentionDetail extends Model
{
    use SoftDeletes;

    protected $fillable = ['detail', 'attention_procedure_id'];
}
