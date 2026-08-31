<?php

namespace App\Factories;

use App\Models\AuditLog;
use App\Models\Project; 

/**
 * Reacts to a new Project being posted by writing a compliance/audit trail
 * entry - supports CyberShield's stated "comprehensive audit logging" and show 
 * the Observer pattern scaling to more than one independnt reaction
 * to same event without touching Project-posting logic itself.
 */

class AuditLogObserver implements ProjectObserverInterface
{
    public function update(Project $project): void
    {
        AuditLog::create([
            'organisation_id' => $project->organisation_id,
            'action'     => 'project_posted',
            'entity_type' => 'Project',
            'entity_id'   => $project->id,
            'details'     => "Project '{$project->title}' was posted.",
            'created_at'  => now(),
        ]);
    }
}