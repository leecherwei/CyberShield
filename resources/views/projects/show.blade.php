@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <p class="mb-2 text-sm font-semibold uppercase tracking-wide text-indigo-600">Partnership Project</p>
            <h1 class="text-3xl font-semibold text-gray-900">{{ $project->title }}</h1>
        </div>
        <a href="{{ route('projects.index') }}"
           class="inline-flex h-11 w-36 shrink-0 items-center justify-center rounded-md border border-gray-300 bg-white px-4 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            &larr; Go Back
        </a>
    </div>

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <dl class="grid gap-5 border-b border-gray-200 p-6 sm:grid-cols-2">
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Status</dt>
                <dd class="mt-1 text-sm font-medium text-gray-900">{{ ucfirst($project->status) }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Industry</dt>
                <dd class="mt-1 text-sm font-medium text-gray-900">{{ $project->industry ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Location</dt>
                <dd class="mt-1 text-sm font-medium text-gray-900">{{ $project->location ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Posted By</dt>
                <dd class="mt-1 text-sm font-medium text-gray-900">{{ $project->organisation->name ?? '—' }}</dd>
            </div>
        </dl>

        <div class="p-6">
            <h2 class="mb-3 text-xl font-semibold text-gray-900">Project Description</h2>
        {{-- Output-encoded via SecurityManagerService::handleMitigationOne()
             before this data reaches the view; Blade's {{ }} adds a
             second auto-escaping layer. --}}
            <p class="whitespace-pre-line text-sm leading-6 text-gray-700">{{ $project->description }}</p>
        </div>
    </div>

    @if (auth()->user()->organisation_id === $project->organisation_id)
        <div class="mt-6 flex flex-col gap-3 sm:flex-row">
            <a href="{{ route('projects.edit', $project) }}"
               class="inline-flex h-11 w-full items-center justify-center rounded-md bg-indigo-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-40">
                Edit Project
            </a>
            <a href="{{ route('partnership-requests.index', $project) }}"
               class="inline-flex h-11 w-full items-center justify-center rounded-md border border-gray-300 bg-white px-4 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-56">
                Review Interests
            </a>
        </div>
    @endif

    <div class="mt-8 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <h2 class="mb-2 text-xl font-semibold text-gray-900">Express Interest in this Project</h2>
        <p class="mb-5 text-sm text-gray-600">Tell the project owner how your organisation can contribute.</p>
        <form method="POST" action="{{ route('partnership-requests.store') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="project_id" value="{{ $project->id }}">
        <div>
            <label for="message" class="mb-1 block text-sm font-medium text-gray-700">Message</label>
            <textarea name="message" id="message" rows="4" placeholder="Introduce your organisation and interest..." required
                      class="block min-h-28 w-full resize-y rounded-md border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
        </div>
        <button type="submit"
                class="inline-flex h-11 w-full items-center justify-center rounded-md bg-gray-900 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-700 focus:ring-offset-2 sm:w-56">
            Submit Partnership Interest
        </button>
        </form>
    </div>

    <a href="{{ route('projects.index') }}"
       class="mt-6 inline-flex h-11 w-36 items-center justify-center rounded-md border border-gray-300 bg-white px-4 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
        &larr; Go Back
    </a>
</div>
@endsection