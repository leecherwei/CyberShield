@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Partnership Interests</h1>
    <p>Project: {{ $project->title }}</p>

    @if (session('success'))
        <p>{{ session('success') }}</p>
    @endif

    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    @forelse ($requests as $partnershipRequest)
        <article>
            <h2>{{ $partnershipRequest->requestingOrganisation->name ?? 'Organisation' }}</h2>
            <p>{{ $partnershipRequest->message }}</p>
            <p>Status: {{ ucfirst($partnershipRequest->status) }}</p>

            @if ($partnershipRequest->status === 'pending')
                <form method="POST" action="{{ route('partnership-requests.accept', $partnershipRequest) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit">Accept</button>
                </form>
                <form method="POST" action="{{ route('partnership-requests.reject', $partnershipRequest) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit">Reject</button>
                </form>
            @endif
        </article>
    @empty
        <p>No partnership interests have been submitted yet.</p>
    @endforelse

    <a href="{{ route('projects.show', $project) }}">Back to project</a>
</div>
@endsection
