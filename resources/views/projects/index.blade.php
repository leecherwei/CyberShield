@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Browse Partnership Projects</h1>

    {{-- Search & filter form: industry, location, per leader's spec.
         SDG filter removed — platform targets SDG 17 exclusively, so
         every project is implicitly SDG 17. --}}
    <form method="GET" action="{{ route('projects.index') }}" class="filters">
        <input type="text" name="keyword" placeholder="Search by title or keyword"
               value="{{ request('keyword') }}">

        <select name="industry">
            <option value="">All Industries</option>
            <option value="education">Education</option>
            <option value="healthcare">Healthcare</option>
            <option value="environment">Environment</option>
        </select>

        <input type="text" name="location" placeholder="Location"
               value="{{ request('location') }}">

        <button type="submit">Search</button>
    </form>

    {{-- Output-encoded via SecurityManagerService::handleMitigationOne()
         before this data ever reaches the view — XSS mitigation applied
         at the controller/service layer, not here. --}}
    <table class="project-list">
        <thead>
            <tr>
                <th>Title</th>
                <th>Status</th>
                <th>Posted By</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($projects as $project)
                <tr>
                    <td>{{ $project->title }}</td>
                    <td>{{ ucfirst($project->status) }}</td>
                    <td>{{ $project->organisation->name ?? '—' }}</td>
                    <td>
                        <a href="{{ route('projects.show', $project) }}">View</a>
                        @if (auth()->user()->organisation_id === $project->organisation_id)
                            <a href="{{ route('projects.edit', $project) }}">Edit</a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">No projects found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection