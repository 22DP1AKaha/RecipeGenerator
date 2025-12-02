<?php

// app/Http/Requests/ProfileUpdateRequest.php
namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'vards' => ['required','string','max:255'],
            'email' => [
                'required','email','max:255',
                Rule::unique(User::class,'email')->ignore($this->user()->user_id,'user_id'),
            ],
            'dietas_ierobezojumi'   => ['nullable','array'],
            'dietas_ierobezojumi.*' => ['integer','exists:dietary_restrictions,id'],
            'alergijas'             => ['nullable','array'],
            'alergijas.*'           => ['integer','exists:allergies,id'],
        ];
    }
}
