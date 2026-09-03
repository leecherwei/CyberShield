@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ $project->title }}</h1>

    <p><strong>Status:</strong> {{ ucfirst($project->status) }}</p>
    <p><strong>Industry:</strong> {{ $project->industry ?? '—' }}</p>
    <p><strong>Location:</strong> {{ $project->location ?? '—' }}</p>
    <p><strong>Posted by:</strong> {{ $project->organisation->name ?? '—' }}</p>

    <div class="description">
        {{-- Output-encoded via SecurityManagerService::handleMitigationOne()
             before this data reaches the view; Blade's {{ }} adds a
             second auto-escaping layer. --}}
        {{ $project->description }}
    </div>

    @if (auth()->user()->organisation_id === $project->organisation_id)
        <a href="{{ route('projects.edit', $project) }}">Edit Project</a>
        <a href="{{ route('partnership-requests.index', $project) }}">Review Partnership Interests</a>
    @endif

    <hr>

    <h2>Express Interest in this Project</h2>
    <form method="POST" action="{{ route('partnership-requests.store') }}">
        @csrf
        <input type="hidden" name="project_id" value="{{ $project->id }}">
        <textarea name="message" rows="4" placeholder="Introduce your organisation and interest..." required></textarea>
        <button type="submit">Submit Partnership Interest</button>
    </form>

    <a href="{{ route('projects.index') }}">&larr; Back to listings</a>
</div>
@endsection