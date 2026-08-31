<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * PartnershipRequest
 * 
 * It represent an organisation expressing interest in a Project. Sits between
 * Project and Partnership: a request only becomes a formal Partnership 
 * once the project owner accepts it.
 */

class PartnershipRequest extends Model
{
    use HasFactory;

    protected $table = 'partnership_requests';

    protected $fillable = [
        'project_id',
        'organisation_id',
        'message',
        'status',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_REJECTED = 'rejected';

    /**
     * The project request was submitted against 
     */
    public function project()
    {
        return $this->belongTo(Project::class);
    }

    /**
     * The organisation that submitted this request
     */
    public function requestingOrganisation()
    {
        return $this->belongsTo(Organisation::class, 'organisation_id');
    }

    /**
     * Create a new PartnershipRequest representing an organisation 
     * expressing interest in a project. This is the single, explicit
     * entry point for that action - called from partnershipRequestController::store(),
     *  and internally by SecurityManagerService::handleMitigationTwo() for the
     * 'submit_partnership_request' action
     */

    public static function expressInterest(int $projectId, int $organisationId, string $message): self
    {
        return self::create([
            'project_id'      => $projectId,
            'organisation_id' => $organisationId,
            'message'         => $message,
            'status'          => self::STATUS_PENDING,
        ]);
    }

    /**
     * The resulting Partnership, if this request was accepted.
     */
    public function partnership()
    {
        return $this->hasOne(Partnership::class);
    }
 
    /**
     * Accepts this request and creates a formal Partnership record.
     * Called from PartnershipRequestController::accept().
     */
    public function accept(): Partnership
    {
        $this->update(['status' => self::STATUS_ACCEPTED]);
 
        return Partnership::create([
            'project_id' => $this->project_id,
            'organisation_id' => $this->organisation_id,
        ]);
    }
 
    public function reject(): void
    {
        $this->update(['status' => self::STATUS_REJECTED]);
    }

}