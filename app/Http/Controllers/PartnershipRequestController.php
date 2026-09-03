<?php

namespace App\Http\Controllers;

use App\Models\PartnershipRequest;
use App\Models\Project;
use App\Services\SecurityManagerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PartnershipRequestController extends Controller
{
    public function __construct(
        private readonly SecurityManagerService $securityManager
    ) {}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $project = Project::findOrFail($validated['project_id']);
        $organisationId = Auth::user()->organisation_id;

        if ($project->organisation_id === $organisationId) {
            return back()->withErrors(['error' => 'You cannot express interest in your own project.']);
        }

        if ($project->partnershipRequests()
            ->where('organisation_id', $organisationId)
            ->where('status', PartnershipRequest::STATUS_PENDING)
            ->exists()) {
            return back()->withErrors(['error' => 'Your organisation already has a pending request for this project.']);
        }

        $partnershipRequest = $this->securityManager->handleMitigationTwo(
            'submit_partnership_request',
            [
                'project_id' => $validated['project_id'],
                'organisation_id' => $organisationId,
                'message' => $validated['message'],
            ]
        );

        if (!$partnershipRequest instanceof PartnershipRequest) {
            return back()->withErrors([
                'error' => 'Unable to submit partnership interest. Please try again.',
            ]);
        }

        return back()->with('success', 'Partnership interest submitted successfully.');
    }

    public function index(Project $project)
    {
        $this->authorizeProjectOwner($project);

        $requests = $project->partnershipRequests()
            ->with('requestingOrganisation')
            ->latest()
            ->get();

        return view('partnership-requests.index', compact('project', 'requests'));
    }

    public function accept(PartnershipRequest $partnershipRequest)
    {
        $this->authorizeProjectOwner($partnershipRequest->project);

        if ($partnershipRequest->status !== PartnershipRequest::STATUS_PENDING) {
            return back()->withErrors(['error' => 'Only pending requests can be accepted.']);
        }

        DB::transaction(fn () => $partnershipRequest->accept());

        return back()->with('success', 'Partnership request accepted.');
    }

    public function reject(PartnershipRequest $partnershipRequest)
    {
        $this->authorizeProjectOwner($partnershipRequest->project);

        if ($partnershipRequest->status !== PartnershipRequest::STATUS_PENDING) {
            return back()->withErrors(['error' => 'Only pending requests can be rejected.']);
        }

        $partnershipRequest->reject();

        return back()->with('success', 'Partnership request rejected.');
    }

    private function authorizeProjectOwner(Project $project): void
    {
        abort_unless(
            Auth::user()->organisation_id === $project->organisation_id,
            403,
            'You do not have permission to manage requests for this project.'
        );
    }
}
