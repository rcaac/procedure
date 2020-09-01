<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Entity extends Model
{
    use SoftDeletes;

    protected $fillable = ['code', 'description', 'direction', 'ruc', 'dependency', 'created_by', 'entity_type_id'];

    public function childrenEntities()
    {
        return $this->hasMany(Entity::class, 'dependency', 'code');
    }

    public function allChildrenEntities()
    {
        return $this->childrenEntities()->with('allChildrenEntities');
    }
}
