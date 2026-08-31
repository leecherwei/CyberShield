<?php

namespace App\Factories;

use App\Modles\Project;

/**
 * Concrete Subject in Observer pattern for the project & partnership
 * posting module
 * 
 * Module Outline requirement:
 * "When a new project is posted, automatically notify all verified 
 * organisations."
 * 
 * Usage (see SecurityManagerService::handleMitigationTwo, action 'post_Project');
 * 
 *   $subject = new ProjectPostingSubject();
 *   $subject->attach(new NewProjectNotificationObserver());
 *   $subject->attach(new AuditLogObserver());
 *   $subject->notify($project); // fires both observers
 * 
 * 
 */

class ProjectPostingSubject implements ProjectSubjectInterface
{
    /** @var ProjectObserverInterface[] */
    private array $observers = [];

    public function attach(ProjectObserverInterface $observer): void
    {
        $this->observers[] = $observer;
    }

    public function detach(ProjectObserverInterface $observer): void
    {
        $this->observers = array_filter(
            $this->observers,
            fn(ProjectObserverInterface $o) => $o !== $observer
        );
    }



    /**
     *  Nofify attached observer that project event occured
     *  Each observer decides independently what to do
     *  That decoupling is the whole point of the pattern 
     */

   public function notify(Project $project): void
    {
        foreach ($this->observers as $observer) {
            $observer->update($project);
        }
    }
}
