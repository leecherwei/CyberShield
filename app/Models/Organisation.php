<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organisation extends Model
{

    use HasFactory;

    protected $table = 'organisations';

    // protected $fillable = ['name', 'registration_number', 'status', 'sdg_focus'];
    protected $fillable = [
        'name',
        'type',
        'is_verified',
        'trust_score',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'trust_score' => 'float',
    ];

    public function users()
    {
        return $this->hasMany(User::class); // Organisation 1 -> User 1..*
    }


    public function projects()
    {
        return $this->hasMany(Project::class); // Organisation 1 -> Project 1..*
    }

    public function partnershipRequests()
    {
        return $this->hasMany(PartnershipRequest::class); // Organisation 1 -> PartnershipRequest 1..*
    }

    public function partnerships()
    {
        return $this->hasMany(Partnership::class);
    }

    public function partnershipsRequests()
    {
        return $this->hasMany(PartnershipRequest::class); // Organisation 1 -> Partnership 1..*
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class); // Organisation 1 -> Notification 1..*
    }


    public function verifications()
    {
        return $this->hasMany(OrganisationVerification::class);
    }  
        
}
