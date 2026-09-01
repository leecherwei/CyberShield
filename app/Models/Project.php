<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * Project 
 * 
 * Represent a sustainable-development project/partnership listing posted by
 * an Organiasation. 
 */

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'projects';

    protected $fillable = [
        'title',
        'description',
        'industry',
        'location',
        'status',
        'organisation_id',
        'created by'
    ];

    protected $cast = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    //Status constants - mirrors module outlines listing states
    public const STATUS_DRAFT ='draft';
    public const STATUS_Published ='published';
    public const STATUS_CLOSED ='closed';
    public const STATUS_ARCHIVED ='archived';

    /**
     * The organisation that post this project
     */
    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    /**
     * The user that created this project listing
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * All partnership request (expressions of interest) submitted
     * against this project
     */

    public function partnershipRequests()
    {
        return $this->hasMany(PartnershipRequest::class);
    }
    
}