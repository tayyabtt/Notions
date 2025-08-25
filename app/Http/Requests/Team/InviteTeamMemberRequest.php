<?php

namespace App\Http\Requests\Team;

use Illuminate\Foundation\Http\FormRequest;

class InviteTeamMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:255'],
            'role' => ['nullable', 'in:admin,member'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'role.in' => 'Role must be either admin or member.',
        ];
    }
}