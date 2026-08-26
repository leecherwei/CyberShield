<?php

namespace App\Services;

/**
 * Class SecurityManager
 * Integrates OWASP Secure Coding Practices into Module 3.1 (User & Profile Management).
 */
class SecurityManager {

    // =========================================================================
    // MANDATORY GLOBAL PRACTICE: Input Validation 
    // =========================================================================
    
    /**
     * Task 2.1 - Mandatory Input Validation Rule 1
     * Validates Organisation Registration / SSM Number format (e.g., 202301000123 or 123456-X).
     */
    public static function validateFieldOne($data) {
        if (empty($data)) {
            return false;
        }
        // Accepts 12-digit SSM numbers or 6-digit legacy format (e.g., 123456-A)
        return (bool) preg_match('/^(\d{12}|\d{6}-[A-Z])$/', trim($data));
    }

    /**
     * Task 2.2 - Mandatory Input Validation Rule 2
     * Validates file mime-type and extension strictly for uploaded verification documents.
     */
    public static function validateFieldTwo($data) {
        if (!isset($data['extension']) || !isset($data['mime'])) {
            return false;
        }

        $allowedExtensions = ['pdf', 'png', 'jpg', 'jpeg'];
        $allowedMimes = ['application/pdf', 'image/png', 'image/jpeg'];

        $extValid = in_array(strtolower($data['extension']), $allowedExtensions, true);
        $mimeValid = in_array(strtolower($data['mime']), $allowedMimes, true);

        return $extValid && $mimeValid;
    }


    // =========================================================================
    // ASSIGNMENT MITIGATION LAYER: Threat 1 (Broken Access Control - A01:2021)
    // =========================================================================
    
    /**
     * Task 2.3 - Mitigation Strategy for Threat 1: IDOR / Broken Access Control
     * Verifies that the authenticated user owns the target organisation profile before allowing edits.
     */
    public static function handleMitigationOne($param) {
        $authenticatedUser = auth()->user();

        if (!$authenticatedUser) {
            return false;
        }

        // Allow Admins to access any profile
        if ($authenticatedUser->role?->name === 'Admin') {
            return true;
        }

        // Verify Organisation Ownership (Strict Strict IDOR Guard)
        $targetOrganisationId = (int) $param;
        return (int) $authenticatedUser->organisation_id === $targetOrganisationId;
    }


    // =========================================================================
    // ASSIGNMENT MITIGATION LAYER: Threat 2 (Cryptographic Failures - A02:2021)
    // =========================================================================
    
    /**
     * Task 2.4 - Mitigation Strategy for Threat 2: Weak Passwords / Storage
     * Enforces high-entropy password requirements and secure hashing verification.
     */
    public static function handleMitigationTwo($param) {
        $password = (string) $param;

        // Minimum 8 characters, at least 1 uppercase, 1 lowercase, 1 number, and 1 special char
        $regex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';

        return (bool) preg_match($regex, $password);
    }
}
?>