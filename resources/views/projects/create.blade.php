@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Post a New Project</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- PostProjectRequest handles all validation server-side;
         this form does not attempt client-side security enforcement. --}}
    <form method="POST" action="{{ route('projects.store') }}">
        @csrf

        <label for="title">Project Title</label>
        <input type="text" name="title" id="title" value="{{ old('title') }}" required minlength="5" maxlength="100">

        <label for="description">Description</label>
        <textarea name="description" id="description" rows="6" required maxlength="2000">{{ old('description') }}</textarea>

        <label for="industry">Industry</label>
        <input type="text" name="industry" id="industry" value="{{ old('industry') }}">

        <label for="location">Location</label>
        <input type="text" name="location" id="location" value="{{ old('location') }}">

        <input type="hidden" name="organisation_id" value="{{ auth()->user()->organisation_id }}">

        <button type="submit">Post Project</button>
    </form>
</div>
@endsection