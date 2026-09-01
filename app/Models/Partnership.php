<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Partnership model represents a partnership formed after a PartnershipReuqest
 * is accepted. This is a MINIMAL base model - it only contains the fields
 * needed for Project $ partnerships Posting to function. Progress-tracking
 * fields belong to the active-partnership/milestone-tracking module and should be added
 * by whoever owns that module.
 */

class Partnership extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'organisation_id',
        'formed_at',
    ];

    protected $casts = [
        'formed_at' => 'datetime',
    ];

    /**
     * The project this partnership was formed around
     * 
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * The organisation whose PartnershipRequest was accepted.
     */
    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }


}