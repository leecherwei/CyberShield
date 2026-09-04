@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-3xl font-semibold text-gray-900">Post a New Project</h1>
        <a href="{{ route('projects.index') }}"
           class="inline-flex h-11 w-36 items-center justify-center rounded-md border border-gray-300 bg-white px-4 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            &larr; Go Back
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-6 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- PostProjectRequest handles all validation server-side;
         this form does not attempt client-side security enforcement. --}}
    <form method="POST" action="{{ route('projects.store') }}" class="space-y-5 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        @csrf

        <div>
            <label for="title" class="mb-1 block text-sm font-medium text-gray-700">Project Title</label>
            <input type="text" name="title" id="title" value="{{ old('title') }}" required minlength="5" maxlength="100"
                   class="block h-11 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        <div>
            <label for="description" class="mb-1 block text-sm font-medium text-gray-700">Description</label>
            <textarea name="description" id="description" required maxlength="2000"
                      class="block h-11 w-full resize-none rounded-md border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
        </div>

        <div>
            <label for="industry" class="mb-1 block text-sm font-medium text-gray-700">Industry</label>
            <select name="industry" id="industry" required
                    class="block h-11 w-full rounded-md border-gray-300 bg-white text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="education" {{ old('industry') === 'education' ? 'selected' : '' }}>Education</option>
                <option value="healthcare" {{ old('industry') === 'healthcare' ? 'selected' : '' }}>Healthcare</option>
                <option value="environment" {{ old('industry') === 'environment' ? 'selected' : '' }}>Environment</option>
                <option value="others" {{ old('industry') === 'others' ? 'selected' : '' }}>Others</option>
            </select>
        </div>

        <div>
            <label for="location" class="mb-1 block text-sm font-medium text-gray-700">Location</label>
            <input type="text" name="location" id="location" value="{{ old('location') }}"
                   class="block h-11 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        <input type="hidden" name="organisation_id" value="{{ auth()->user()->organisation_id }}">

        <div class="flex flex-col gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:justify-end">
            <a href="{{ route('projects.index') }}"
               class="inline-flex h-11 w-full items-center justify-center rounded-md border border-gray-300 bg-white px-4 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-36">
                Cancel
            </a>
            <button type="submit"
                    class="inline-flex h-11 w-full items-center justify-center rounded-md bg-indigo-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-36">
                Post Project
            </button>
        </div>
    </form>
</div>
@endsection