@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-3xl font-semibold text-gray-900">Browse Partnership Projects</h1>

        <a href="{{ route('projects.create') }}"
           class="inline-flex h-11 w-48 items-center justify-center rounded-md bg-indigo-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            Post a New Project
        </a>
    </div>

    @if (session('success'))
        <p class="mb-6 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</p>
    @endif

    @if ($errors->any())
        <ul class="mb-6 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    {{-- Search & filter form: status, industry, and location, per leader's spec.
         SDG filter removed — platform targets SDG 17 exclusively, so
         every project is implicitly SDG 17. --}}
    <form method="GET" action="{{ route('projects.index') }}" class="mb-8 grid gap-4 rounded-lg border border-gray-200 bg-white p-5 shadow-sm sm:grid-cols-2 lg:grid-cols-[minmax(0,2fr)_minmax(0,1fr)_minmax(0,1fr)_minmax(0,1fr)_minmax(0,1fr)_auto] lg:items-end">
        <div>
            <label for="keyword" class="mb-1 block text-sm font-medium text-gray-700">Keyword</label>
            <input type="text" name="keyword" id="keyword" placeholder="Search by title or keyword"
                   value="{{ request('keyword') }}"
                   class="block h-11 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        <div>
            <label for="organisation_id" class="mb-1 block text-sm font-medium text-gray-700">Organisation</label>
            <select name="organisation_id" id="organisation_id"
                    class="block h-11 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">All organisations</option>
                @foreach ($organisations as $organisation)
                    <option value="{{ $organisation->id }}" {{ (string) request('organisation_id') === (string) $organisation->id ? 'selected' : '' }}>
                        {{ $organisation->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="status" class="mb-1 block text-sm font-medium text-gray-700">Status</label>
            <select name="status" id="status"
                    class="block h-11 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">All statuses</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
            </select>
        </div>

        <div>
            <label for="industry" class="mb-1 block text-sm font-medium text-gray-700">Industry</label>
            <select name="industry" id="industry"
                    class="block h-11 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="education">Education</option>
                <option value="healthcare">Healthcare</option>
                <option value="environment">Environment</option>
                <option value="others">Others</option>
            </select>
        </div>

        <div>
            <label for="location" class="mb-1 block text-sm font-medium text-gray-700">Location</label>
            <input type="text" name="location" id="location" placeholder="Location"
                   value="{{ request('location') }}"
                   class="block h-11 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        <div class="flex items-end gap-3 lg:col-span-1">
            <button type="submit"
                    class="inline-flex h-11 min-w-0 flex-1 items-center justify-center rounded-md bg-gray-900 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-700 focus:ring-offset-2">
                Search
            </button>
            <a href="{{ route('projects.index') }}"
               class="inline-flex h-11 items-center justify-center rounded-md border border-gray-300 px-4 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Reset
            </a>
        </div>
    </form>

    {{-- Output-encoded via SecurityManagerService::handleMitigationOne()
         before this data ever reaches the view — XSS mitigation applied
         at the controller/service layer, not here. --}}
    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="project-list min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Title</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Status</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Posted By</th>
                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-600">Actions</th>
            </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
            @forelse ($projects as $project)
                <tr class="transition hover:bg-gray-50">
                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">{{ $project->title }}</td>
                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">{{ ucfirst($project->status) }}</td>
                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">{{ $project->organisation->name ?? '—' }}</td>
                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                        <a class="mr-4 font-semibold text-indigo-600 hover:text-indigo-800" href="{{ route('projects.show', $project) }}">View</a>
                        @if (auth()->user()->organisation_id === $project->organisation_id)
                            <a class="font-semibold text-gray-700 hover:text-gray-900" href="{{ route('projects.edit', $project) }}">Edit</a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-6 py-10 text-center text-sm text-gray-500">No projects found.</td></tr>
            @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection