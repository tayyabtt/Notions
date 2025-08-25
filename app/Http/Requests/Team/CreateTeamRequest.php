<?php

namespace App\Http\Requests\Team;

use Illuminate\Foundation\Http\FormRequest;

class CreateTeamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Team name is required.',
            'name.max' => 'Team name cannot exceed 255 characters.',
            'description.max' => 'Team description cannot exceed 1000 characters.',
        ];
    }
}