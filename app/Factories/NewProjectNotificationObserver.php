<?php

namespace App\Factories;

use App\Models\Organisation;
use App\Models\Project;
use App\Services\NotificationService;

/**
 * Reacts to a new Project being posted by notifying all verified organisations.
 * 
 * This observer ONLY handles the "new project posting"
 * notification. Partnership-interest notification are handled 
 * separately by NotificationService::notifyPartnershipInterest()
 * called directly from SecurityManagerService. 
 */

class NewProjectNotificationObserver implements ProjectObserverInterface
{
    public function __construct(
        private readonly NotificationService $notificationService = new NotificationService()
        ){}

        public function update(Project $project): void 
        {
            $verifiedOrganisations = Organisation::query()
            ->where('id', '!=', $project->organisation_id) // don't notify the poster
            ->where('is_verified', true)                    // only verified organisations
            ->get();

            foreach ($verifiedOrganisations as $organisation){
                $this->notificationService->notifyProjectMatch($organisation, $project);
                
            }
        }
}