<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organisation extends Model
{
    protected $fillable = ['name', 'registration_number', 'status', 'sdg_focus'];

    public function users()
    {
        return $this->hasMany(User::class); // Organisation 1 -> User 1..*
    }

    public function verifications()
    {
        return $this->hasMany(OrganisationVerification::class);
    }
}