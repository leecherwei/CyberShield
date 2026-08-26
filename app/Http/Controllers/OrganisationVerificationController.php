<?php

namespace App\Http\Controllers;

use App\Models\Organisation;
use App\Services\SecurityManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrganisationVerificationController extends Controller
{
    public function store(Request $request)
    {
        $user = auth()->user();

        // 1. Authorization & Edge Case Guard
        if (!$user || !$user->organisation_id) {
            return back()->with('error', 'You must belong to an organisation to submit verifications.');
        }

        // 2. Strict Input Validation (Laravel Built-in)
        $request->validate([
            'document' => ['required', 'file', 'mimes:pdf,jpg,png', 'max:5120'], // Max 5MB
        ]);

        try {
            $file = $request->file('document');

            // 3. Mandatory OWASP Input Validation via SecurityManager (Task 2.2)
            $isValidFormat = SecurityManager::validateFieldTwo([
                'extension' => $file->getClientOriginalExtension(),
                'mime' => $file->getMimeType(),
            ]);

            if (!$isValidFormat) {
                return back()->with('error', 'Security check failed: Invalid file type or extension format.');
            }

            // 4. Store file in secure private storage (storage/app/private/verifications)
            $path = $file->store('verifications', 'local');

            // 5. Update Organisation record state
            Organisation::where('id', $user->organisation_id)->update([
                'status' => 'pending_verification'
            ]);

            return back()->with('success', 'Verification document submitted successfully for admin review.');

        } catch (\Exception $e) {
            Log::error('Verification Upload Failed: ' . $e->getMessage(), [
                'user_id' => $user->id ?? null,
                'organisation_id' => $user->organisation_id ?? null
            ]);

            return back()->with('error', 'Failed to upload verification document. Please try again.');
        }
    }
}