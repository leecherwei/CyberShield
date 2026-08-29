<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Organisation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OrganisationApiController extends Controller
{
    /**
     * Expose RESTful API for Organisation Profile Retrieval
     */
    public function getOrganisationProfile(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'organisationId' => 'required|integer|exists:organisations,id',
            'requestID'      => 'required|string',
            'timeStamp'      => 'required|date_format:Y-m-d H:i:s',
        ]);

        $organisation = Organisation::withCount('users')->find($validated['organisationId']);

        return response()->json([
            'status'             => 'S',
            'requestID'          => $validated['requestID'],
            'organisationName'   => $organisation->name,
            'verificationStatus' => $organisation->status,
            'memberCount'        => $organisation->users_count,
            'timeStamp'          => now()->format('Y-m-d H:i:s'),
        ], 200);
    }
}