<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganisationVerification extends Model
{
    protected $fillable = [
        'organisation_id',
        'document_path',
        'status',
    ];
}
