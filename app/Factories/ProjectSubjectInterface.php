<?php

namespace App\Factories;

use App\Models\Project;


/**
 * This is the 'Subject' side of Observer pattern, Maintains a list of 
 * observers and notifies all of them when a relavant Project event happens - 
 * decoupling Project posting logic from what happen afterward
 */

interface ProjectSubjectInterface
{
    public function attach(ProjectObserverInterface $observer): void;

    public function detach(ProjectObserverInterface $observer): void;

    public function notify(Project $project): void;
}