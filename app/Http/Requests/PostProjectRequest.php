<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * PostProjectRequest
 */

class PostProjectRequest extends FormRequest
{
     public function authorize(): bool
    {
        // RBAC check happens via middleware/policy, not here.
        return true;
    }

    /**
     * Runs Before validation rules. Strips raw HTML/script tags from 
     * free-taxt fields at the earliest possible point - mirrros the module
     * outline's mentions of strip_tags()/filter_input()-style
     */

    protected function prepareForValidation(): void
    {
        $this->merge([
            'title'       => strip_tags((string) $this->input('title')),
            'description' => strip_tags((string) $this->input('description')),
        ]);
    }

    /**
     * Task 2.1 equivalent - Project title validation
     * Task 2.2 equivalent - organisation_id (web service parameter) validation
     */

    public function rules(): array
    {
        return [
            // Field One: Project title
            // 5-100 chars, restricted character set (mirrors validateFieldOne)
            'title' => [
                'required',
                'string',
                'min:5',
                'max:100',
                'regex:/^[a-zA-Z0-9\s\.\,\-\&\(\)\'\/]+$/u',
            ],
 
            'description' => [
                'required',
                'string',
                'max:2000',
            ],
 
            'industry' => [
                'nullable',
                'string',
                'max:100',
            ],
 
            'location' => [
                'nullable',
                'string',
                'max:150',
            ],
 
            // Field Two: organisation_id (web service parameter)
            // Must be a positive integer that actually exists in the DB
            
            'organisation_id' => [
                'required',
                'integer',
                'min:1',
                'exists:organisations,id',
            ],
        ];
    }
    /**
     * Custome message so validation failures are clear in API responses 
     * (useful since this is also a web service technologies deliverable)
     */

    public function message(): array
    {
        return [
            'title.regex' => 'Project title contains invalid characters.',
            'organisation_id.exists' => 'The specified organisation does not exist.',   
        ];
    }


}