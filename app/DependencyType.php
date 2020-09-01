<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DependencyType extends Model
{
    protected $table ='dependency_types';

    protected $fillable = ['description'];
}
