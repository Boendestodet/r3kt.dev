<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required', 
                'string', 
                'min:3',
                'max:255', 
                'regex:/^[a-zA-Z0-9\s\-_]+$/',
                'unique:projects,name,NULL,id,user_id,' . auth()->id()
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'settings' => ['nullable', 'array'],
            'settings.stack' => ['nullable', 'string', 'in:nextjs,vite-react,vite-vue,sveltekit,astro,nuxt3,backend,game-dev,traditional'],
            'settings.ai_model' => ['nullable', 'string', 'in:Claude Code,OpenAI GPT-4,Gemini 1.5 Pro,Cursor CLI'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Project name is required.',
            'name.min' => 'Project name must be at least 3 characters long.',
            'name.max' => 'Project name must not exceed 255 characters.',
            'name.regex' => 'Project name can only contain letters, numbers, spaces, hyphens, and underscores.',
            'name.unique' => 'A project with this name already exists. Please choose a different name.',
            'description.max' => 'Description must not exceed 1000 characters.',
            'settings.stack.in' => 'Invalid stack selection. Please choose a valid framework.',
            'settings.ai_model.in' => 'Invalid AI model selection. Please choose a valid model.',
        ];
    }
}
