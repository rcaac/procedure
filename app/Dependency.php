<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dependency extends Model
{
    use SoftDeletes;

    protected $fillable = ['code', 'description', 'abbreviation', 'dependency', 'entity_id', 'dependency_type_id', 'start_procedure'];

    public function childrenDependencies()
    {
        return $this->hasMany(Dependency::class, 'dependency', 'code');
    }

    public function allChildrenDependencies()
    {
        return $this->childrenDependencies()->with('allChildrenDependencies');
    }

}
