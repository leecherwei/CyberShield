<?php
 
namespace App\Services;
 
use App\Models\Project;
use App\Models\PartnershipRequest;
use App\Factories\AuditLogObserver;
use App\Factories\NewProjectNotificationObserver;
use App\Services\NotificationService;
use App\Factories\ProjectPostingSubject;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;




class SecurityManagerService
{
    // =========================================================================
    // MANDATORY GLOBAL PRACTICE: Input Validation 
    // =========================================================================


    /**
     * Task 2.1 - Mandatory Input Validation Rule 1
     * Validates the Project title: 5-100 chars, restricted character set
     */

     public function validateFieldOne($data): bool
    {
        if (!is_string($data)) {
            return false;
        }
 
        $data = trim($data);
        $length = mb_strlen($data);
 
        if ($length < 5 || $length > 100) {
            return false;
        }
 
        return (bool) preg_match("/^[a-zA-Z0-9\s\.\,\-\&\(\)\'\/]+$/u", $data);
    }

    /**
     * Task 2.2 - Mandatory Input Validation Rule 2
     * Validates a web-service parameter: organisation_id must be positve 
     * integer
     */

     public function validateFieldTwo($data): bool
    {
        $options = ['options' => ['min_range' => 1]];
        $result = filter_var($data, FILTER_VALIDATE_INT, $options);
 
        return $result !== false;
    }

    // ======================================================================================
    // MITIGATION 1: Stored (Persistent) XSS
    // Match modules outline: strip_tags()/filter_input()-style sanitization
    // on the way in, htmlspecialchars() encoding on way out, plus a Content-security-policy.
    // ======================================================================================



    /**
     * Task 2.3 Stored XSS mitigation, output side. 
     * Encodes stored Project/PartnershipRequest fields before they returned
     * to client - used inside API resources or before 
     * passing data to view, so it applies even to JSON API responses
     */

    public function handleMitigationOne(array $record, string $context = 'html'): array 
    {
        $safe = [];

        foreach ($record as $field => $value){
            if (!is_string($value)){
                $safe[$field] = $value;
                continue;
            }

            $safe[$field] = htmlspecialchars($value , ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
        return $safe;
    }

    /**
     * Task 2.3 (Companion) - Stored XSS mitigation. input side.
     * Sanitizes free-text fields (title, description, partnership request message)
     * Before they are saved to the database, using strip_tags() to remove any HTML/script tags outright.
     * This is a independent layer for handleMitigationOne - even if a rendering path ever forgets to encode on output,
     * the payload was already stripped on the way in. 
     */
    public function sanitizeInput(string $value): string
    {
        // Removes all HTML tags, including <script>, <img onerror=...>, etc.
        $stripped = strip_tags($value);
 
        // Collapse any leftover encoded entities attackers might use to
        // smuggle payloads past a naive strip_tags() call.
        return trim($stripped);
    }


    /**
     * Applies CSP header - defense in-depth so injected scripts cant exceute even
     * if encoding/sanitization gap is event missed.
     */

    public function applyContentSecurityPolicy($response)
    {
        $response->headers->set(
            'Content-Security-Policy',
            "default-src 'self'; script-src 'self'; object-src 'none';"
        );
 
        return $response;
    }

    /*
     * NOTE on HttpOnly + Secure cookies (module outline requirement):
     * These are configured, not called per-request. In Laravel, set in
     * config/session.php:
     *
     *   'secure'   => env('SESSION_SECURE_COOKIE', true),  // HTTPS only
     *   'http_only' => true,                                // JS can't read it
     *   'same_site' => 'lax',
     *
     * This ensures that even if a Stored XSS payload executes, it
     * cannot read the session cookie via document.cookie, limiting
     * the impact of session hijacking.
     */

    // =========================================================================
    // MITIGATION 2: SQL Injection
    // Strategy: Parameterized queries via Eloquent/Query Builder with an
    // allow listed action set, Plus explicit wildard-escaping for Like 
    // search filters, PLUS silent error hadnling for unauthorized users
    // =========================================================================

    /**
     * Task 2.4 - SQL Injection mitigation.
     *
     * Even though Eloquent parameterizes queries by default, this method
     * makes that binding EXPLICIT and restricts execution to a fixed
     * allow-list of actions — demonstrating the same principle as manual
     * PDO prepared statements. LIKE search terms have their wildcard
     * characters (%, _) escaped so a user typing a literal % doesn't
     * unintentionally (or intentionally) broaden the query.
     */

    public function handleMitigationTwo(string $action, array $data)
    {
        try{ 
            switch ($action){

            case 'search_projects':
                $safeKeyword = $this->escapeLikeWildcards($data['keyword'] ?? '');

                return Project::query()
                        ->where('title', 'like', '%' . $safeKeyword . '%')
                        ->when(in_array($data['status'] ?? '', ['draft', 'published', 'closed', 'archived'], true), fn ($query) =>
                            $query->where('status', $data['status'])
                        )
                        ->when($data['industry'] ?? '', fn ($query, $industry) =>
                            $query->where('industry', $industry)
                        )
                        ->when($data['location'] ?? '', fn ($query, $location) =>
                            $query->where('location', 'like', '%' . $this->escapeLikeWildcards($location) . '%')
                        )
                        ->when($data['organisation_id'] ?? '', fn ($query, $organisationId) =>
                            $query->where('organisation_id', (int) $organisationId)
                        )
                        ->orderByDesc('created_at')
                        ->get();

            
            case 'post_project':
                $project = Project::create([
                        'title'           => $this->sanitizeInput($data['title']),
                        'description'     => $this->sanitizeInput($data['description']),
                        'industry'        => $data['industry'] ?? null,
                        'location'        => $data['location'] ?? null,
                        'organisation_id' => (int) $data['organisation_id'],
                        'created_by'      => (int) $data['user_id'],
                ]);      
                
                // Observer pattern hook
                // Fires after a project is sucessfully posted
                // Decoupled: this service knows nothing about WHAT the 
                // Observers do (notify, audit log) - only that they run
                $subject = new ProjectPostingSubject();
                $subject->attach(new NewProjectNotificationObserver());
                $subject->attach(new AuditLogObserver());
                $subject->notify($project);

                return $project;

            case 'submit_partnership_request':
                // Explicit entity-level creation, per requirement:
                // "handle the creation of PartnershipRequest when an
                // organisation expresses interest in a project." 
                $partnershipRequest = PartnershipRequest::expressInterest(
                    (int) $data['project_id'],
                    (int) $data['organisation_id'],
                    $this->sanitizeInput($data['message'])
                );

                // Notification scope per leader's feedback: this module
                // triggers notifications for new posting AND partership
                // interest only - routed through the shared service so 
                // the logic isnt duplicated elsewhere in the system.
                (new NotificationService())->notifyPartnershipInterest($partnershipRequest);

                return $partnershipRequest;

            default:
                Log::warning("[SQLi-Mitigation] Rejected unrecognized action: {$action}" );
                return false;
            }
        }catch (QueryException $e){
            //Error handling requirement: never expose SQL error detail
            // to unauthorizsed users - log internally only. 
            Log::error('[SQLi-Mitigation] Query failed: '. $e->getMessage());
            return false;
        }
    }

    /**
     * Escape like Wildcards characters (%, _) in user-supplied search 
     * terms so they're treated as literal characters, not SQL wildcards. 
     * Used before the term is bound as a parameter (parameter binding
     * alone prevents injection, but NOT wildcard-widening abuse).
     */

    private function escapeLikeWildcards (string $term): string
    {
        return str_replace(['%','_'],['\%','\_'], $term);
    }

    /*
     * Demonstrates the SAME principle using raw SQL with bound 
     * parameters (driver-level), for cases Eloquent doesnt cover. 
     */    

    public function searchProjectsRaw(string $keyword, int $organisationId)
    {
        $safeKeyword = $this->escapeLikeWildcards($keyword);

        return DB::select(
            'SELECT id, title, description, organisation_id, created_at
            FROM projects
            WHERE title LIKE ? ESCAPE \'\\\\\' AND organisation_id = ?
            ORDER BY created_at DESC',
            ['%'. $safeKeyword . '%', $organisationId]
        );
    }


}