<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Person extends Model
{
    use SoftDeletes;

    protected $table = 'persons';

    protected $fillable = ['firstName', 'lastName', 'dni', 'phone', 'email', 'direction', 'identification_document_id'];

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function identificationDocument()
    {
        return $this->hasOne(IdentificationDocument::class);
    }

    public function chargeAssignments()
    {
        return $this->hasMany(ChargeAssignment::class);
    }
}
