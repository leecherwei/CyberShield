<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostProjectRequest;
use App\Models\Project;
use App\Services\SecurityManagerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


/**
 * Central controller for project & partnership posting module 
 * File name and method set match the leader's specified technical 
 * components exactly: index, create, store, edit, update, destroy.
 *
 * Delegates validation to PostProjectRequest and both mitigation strategies
 */

class ProjectPostingController extends Controller
{
    public function __construct(
        private readonly SecurityManagerService $securityManager
    ){}

    /**
     * Gets/project
     * Listing/search page - supports filtering by industry and location as specific
     */

    public function index(Request $request)
    {
       $projects = $this->securityManager->handleMitigationTwo('search_projects', [
            'keyword'         => $request->input('keyword', ''),
            'industry'        => $request->input('industry', ''),
            'location'        => $request->input('location', ''),
            'organisation_id' => (int) $request->input('organisation_id', Auth::user()->organisation_id),
        ]);

        // handleMitigationTwo returns false if the query failed (caught
        // QueryException) — fall back to an empty collection so the view
        // doesn't crash trying to iterate over a boolean.
        if ($projects === false) {
            $projects = collect();
        }

        return view('projects.index', compact('projects'));
    }

    /**
     * Get /projects/create
     * Show the "post new project" form
     */

    public function create()
    {
        return view('projects.create');
    }

    /**
     * POST /projects
     * Validated via PostProjectRequest then persisted + Observer-triggered
     * via SecurityManagerService.
     */
    public function store(PostProjectRequest $request)
    {
        $validated = $request->validated();

        $project = $this->securityManager->handleMitigationTwo('post_project',[
            'title'           => $validated['title'],
            'description'     => $validated['description'],
            'industry'        => $validated['industry'] ?? null,
            'location'        => $validated['location'] ?? null,
            'organisation_id' => $validated['organisation_id'],
            'user_id'         => Auth::id(),
        ]);

        if (!$project){
            return back()->withErrors(['error'=> 'Unable to post project. Please try again']);
        }

        return redirect()->route('projects.index')
             ->with('success', 'Project posted successfully.'); 

    }

    /**
     * Get /projects/{project}
     * Show the project details page
     */
    public function show(Project $project)
    {
        return view('projects.show', compact('project'));
    }




    /**
     * Get /projects/{project}/edit
     * Restrited so only the posting organisation can edit
     */

    public function edit(Project $project)
    {
        $this ->authorizeOwnership($project);

        return view('projects.edit', compact('project'));
    }

    /**
     * Put/Patch /projects/{project}
     */

    public function update(PostProjectRequest $request, Project $project)
    {
        $this->authorizeOwnership($project);
 
        $validated = $request->validated();
 
        // Output-side XSS mitigation is applied at render time via
        // handleMitigationOne(); sanitizeInput() runs here on the way in.
        $project->update([
            'title'        => $this->securityManager->sanitizeInput($validated['title']),
            'description'  => $this->securityManager->sanitizeInput($validated['description']),
        ]);
 
        return redirect()->route('projects.index')
            ->with('success', 'Project updated successfully.');
    }

    /**
     * Delete /projects/{project}
     */
    public function destroy(Project $project)
    {
         $this->authorizeOwnership($project);
 
        $project->delete(); // soft delete, per Project model's SoftDeletes trait
 
        return redirect()->route('projects.index')
            ->with('success', 'Project deleted successfully.');
    }

    /**
     * Patch /projects/{project}/status
     * 
     * Manages LISTING status only (Draft, Published, Closed, Archived).
     */

    public function updateStatus(Request $request, Project $project)
    {
        $this->authorizeOwnership($project);
 
        $request->validate([
            'status' => 'required|in:draft,published,closed,archived',
        ]);
 
        $project->update(['status' => $request->input('status')]);
 
        return redirect()->route('projects.index')
            ->with('success', 'Project status updated.');
    }

    /**
     * Ownership check - only the posting organisation may edit/delete
     */
   private function authorizeOwnership(Project $project): void
    {
        if ($project->organisation_id !== Auth::user()->organisation_id) {
            abort(403, 'You do not have permission to modify this project.');
        }
    }
}
