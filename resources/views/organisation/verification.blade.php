<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Organisation Verification') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                {{-- Flash Messages --}}
                @if (session('success'))
                    <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
                        {{ session('error') }}
                    </div>
                @endif

                <h3 class="text-lg font-bold mb-4">Upload Verification Document</h3>
                <p class="text-gray-600 mb-6">Please upload your business registration or NGO authorization document
                    (PDF, JPG, PNG — Max 5MB).</p>

                <form action="{{ route('organisation.verification.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <input type="file" name="document" class="border border-gray-300 p-2 rounded w-full" required>
                        @error('document')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Submit Document
                    </button>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>