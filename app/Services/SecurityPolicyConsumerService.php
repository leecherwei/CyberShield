<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class SecurityPolicyConsumerService
{
    /**
     * Consume the Security Policy API provided by Lim Tze Bing's Admin Module.
     *
     * @return array
     */
    public function fetchCurrentPolicy(): array
    {
        $requestId = Str::uuid()->toString();

        try {
            $response = Http::timeout(5)->post('http://127.0.0.1:8000/api/v1/admin/security-policy', [
                'requestID'    => $requestId,
                'sourceModule' => 'ProfileManagement',
                'timeStamp'    => now()->format('Y-m-d H:i:s'),
            ]);

            // Check if the request failed or returned a non-200 HTTP status code
            if ($response->failed()) {
                Log::error("Failed to fetch security policy from remote module", [
                    'requestID'   => $requestId,
                    'status_code' => $response->status(),
                    'body'        => $response->body(),
                ]);

                return $this->getFallbackPolicy();
            }

            $responseData = $response->json();

            // Validate that the remote service followed the standard IFA response payload structure
            if (!isset($responseData['status']) || $responseData['status'] !== 'S') {
                Log::warning("Remote security policy web service returned non-success IFA status", [
                    'requestID' => $requestId,
                    'response'  => $responseData,
                ]);

                return $this->getFallbackPolicy();
            }

            return $responseData;

        } catch (Throwable $e) {
            // Catch connection timeouts, DNS failures, or unexpected network exceptions
            Log::error("Exception occurred while consuming security policy service", [
                'requestID' => $requestId,
                'error'     => $e->getMessage(),
            ]);

            return $this->getFallbackPolicy();
        }
    }

    /**
     * Safe default fallback policy structure used when the external web service fails.
     *
     * @return array
     */
    private function getFallbackPolicy(): array
    {
        return [
            'status'     => 'F',
            'message'    => 'Using safe default security policy due to service failure.',
            'allowed'    => true,
            'review'     => true,
            'isFallback' => true,
        ];
    }
}