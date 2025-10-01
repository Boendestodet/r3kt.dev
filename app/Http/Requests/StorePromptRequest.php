<?php

namespace App\Http\Requests;

use App\Services\InputSanitizationService;
use Illuminate\Foundation\Http\FormRequest;

class StorePromptRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $sanitizer = app(InputSanitizationService::class);
        
        $this->merge([
            'prompt' => $sanitizer->sanitizePrompt($this->input('prompt', '')),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'prompt' => ['required', 'string', 'min:10', 'max:2000'],
            'auto_start_container' => ['boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'prompt.required' => 'Please enter a prompt to generate your website.',
            'prompt.min' => 'Prompt must be at least 10 characters long.',
            'prompt.max' => 'Prompt must not exceed 2000 characters.',
        ];
    }
}
