<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Organisation;
use App\Models\PartnershipRequest;
use App\Models\Project;

class NotificationService
{
    public function notifyProjectMatch(Organisation $organisation, Project $project): Notification
    {
        return Notification::create([
            'organisation_id' => $organisation->id,
            'type' => 'project_match',
            'project_id' => $project->id,
            'message' => "A new project, '{$project->title}', may match your organisation.",
            'is_read' => false,
        ]);
    }

    public function notifyPartnershipInterest(PartnershipRequest $request): Notification
    {
        $request->loadMissing('project');

        return Notification::create([
            'organisation_id' => $request->project->organisation_id,
            'type' => 'partnership_interest',
            'project_id' => $request->project_id,
            'message' => "Your project received a new partnership interest.",
            'is_read' => false,
        ]);
    }
}
