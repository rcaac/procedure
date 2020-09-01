<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetailChargeAssignment extends Model
{
    use SoftDeletes;

    protected $table ='detail_charge_assignments';

    protected $fillable = ['startDate', 'finalDate', 'detail', 'charge_assignment_id'];
}
