<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Session;

class ChargeAssignment extends Model
{
    use SoftDeletes;

    protected $table ='charge_assignments';

    protected $fillable = ['charge', 'type', 'person_id', 'charge_state_id', 'role_id', 'dependency_id'];

    public function details()
    {
        return $this->hasMany(DetailChargeAssignment::class);
    }

    static function charge()
    {
        return  ChargeAssignment::where('dependency_id', Session::get('dependency_id'))
            ->where('person_id', auth()->user()->person->id)
            ->where('charge_state_id', 1)->value('role_id');
    }
}
