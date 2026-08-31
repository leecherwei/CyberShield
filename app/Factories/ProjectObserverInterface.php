<?php

namespace App\Factories;


use App\Models\Project;

/**
 * ProjectObserverInterface
 * 
 * demonstrate the understanding of the actual design pattern 
 */

interface ProjectObserverInterface
{
    /**
     * Called by the Subject whenever a project-related event occurs
     * 
     */
    public function update(Project $project): void;
}
