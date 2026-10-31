<?php
namespace App\Http\Requests;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:3', 'max:150'],
            'request_type' => ['required', Rule::in(array_keys(Project::TYPES))],
            'problem' => ['required', 'string', 'min:10', 'max:2000'],
            'description' => ['required', 'string', 'min:10', 'max:5000'],
            'timeline' => ['nullable', 'string', 'max:150'],
            'budget' => ['nullable', 'string', 'max:150'],
            'contact_name' => [$this->user() ? 'nullable' : 'required', 'string', 'max:255'],
            'contact_email' => [$this->user() ? 'nullable' : 'required', 'email', 'max:255'],
            'contact_organization' => ['nullable', 'string', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:40'],
            'confidential' => ['boolean'],
            'consent' => ['accepted'],
            'website' => ['nullable', 'max:0'], // honeypot: must stay empty
        ];
    }

    protected function prepareForValidation(): void { $this->merge(['confidential' => $this->boolean('confidential')]); }
}
