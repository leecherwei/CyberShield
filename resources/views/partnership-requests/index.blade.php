@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <p class="mb-2 text-sm font-semibold uppercase tracking-wide text-indigo-600">Project Review</p>
            <h1 class="text-3xl font-semibold text-gray-900">Partnership Interests</h1>
            <p class="mt-2 text-sm text-gray-600">Project: <span class="font-medium text-gray-900">{{ $project->title }}</span></p>
        </div>
        <a href="{{ route('projects.show', $project) }}"
           class="inline-flex h-11 w-40 shrink-0 items-center justify-center rounded-md border border-gray-300 bg-white px-4 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            &larr; Back to Project
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

    @forelse ($requests as $partnershipRequest)
        <article class="mb-4 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">{{ $partnershipRequest->requestingOrganisation->name ?? 'Organisation' }}</h2>
                    <p class="mt-3 whitespace-pre-line text-sm leading-6 text-gray-700">{{ $partnershipRequest->message }}</p>
                </div>
                <span class="inline-flex h-8 items-center rounded-full bg-gray-100 px-3 text-xs font-semibold uppercase tracking-wide text-gray-700">
                    {{ ucfirst($partnershipRequest->status) }}
                </span>
            </div>

            @if ($partnershipRequest->status === 'pending')
                <div class="mt-5 flex flex-col gap-3 border-t border-gray-100 pt-5 sm:flex-row">
                <form method="POST" action="{{ route('partnership-requests.accept', $partnershipRequest) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                            class="inline-flex h-11 w-full items-center justify-center rounded-md bg-emerald-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 sm:w-32">
                        Accept
                    </button>
                </form>
                <form method="POST" action="{{ route('partnership-requests.reject', $partnershipRequest) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                            class="inline-flex h-11 w-full items-center justify-center rounded-md border border-red-300 bg-white px-4 text-sm font-semibold text-red-700 shadow-sm transition hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:w-32">
                        Reject
                    </button>
                </form>
                </div>
            @endif
        </article>
    @empty
        <div class="rounded-lg border border-dashed border-gray-300 bg-white px-6 py-10 text-center text-sm text-gray-500">
            No partnership interests have been submitted yet.
        </div>
    @endforelse
</div>
@endsection
