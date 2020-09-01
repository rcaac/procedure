<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttentionType extends Model
{
    use SoftDeletes;

    protected $fillable = ['description', 'resource_detail'];
}
