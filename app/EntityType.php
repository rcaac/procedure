<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EntityType extends Model
{
    protected $table = 'entity_types';

    protected $fillable = ['id', 'type'];
}
