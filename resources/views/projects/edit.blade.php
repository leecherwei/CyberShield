@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Project</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('projects.update', $project) }}">
        @csrf
        @method('PUT')

        <label for="title">Project Title</label>
        <input type="text" name="title" id="title" value="{{ old('title', $project->title) }}" required minlength="5" maxlength="100">

        <label for="description">Description</label>
        <textarea name="description" id="description" rows="6" required maxlength="2000">{{ old('description', $project->description) }}</textarea>

        <label for="industry">Industry</label>
        <input type="text" name="industry" id="industry" value="{{ old('industry', $project->industry) }}">

        <label for="location">Location</label>
        <input type="text" name="location" id="location" value="{{ old('location', $project->location) }}">

        <input type="hidden" name="organisation_id" value="{{ $project->organisation_id }}">

        <button type="submit">Save Changes</button>
    </form>

    <hr>

    <h2>Listing Status</h2>
    <form method="POST" action="{{ route('projects.updateStatus', $project) }}">
        @csrf
        @method('PATCH')

        <select name="status">
            <option value="draft" {{ $project->status === 'draft' ? 'selected' : '' }}>Draft</option>
            <option value="published" {{ $project->status === 'published' ? 'selected' : '' }}>Published</option>
            <option value="closed" {{ $project->status === 'closed' ? 'selected' : '' }}>Closed</option>
            <option value="archived" {{ $project->status === 'archived' ? 'selected' : '' }}>Archived</option>
        </select>
        <button type="submit">Update Status</button>
    </form>

    <hr>

    <form method="POST" action="{{ route('projects.destroy', $project) }}"
          onsubmit="return confirm('Delete this project listing?');">
        @csrf
        @method('DELETE')
        <button type="submit">Delete Project</button>
    </form>
</div>
@endsection