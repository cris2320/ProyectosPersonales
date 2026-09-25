<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class LoginRequest extends FormRequest
{
    /** @return array<string, list<string>> */
    public function rules(): array
    {
        return [
            'usuario' => ['required', 'string', 'max:40'],
            'password' => ['required', 'string', 'max:200'],
            'codigo_mfa' => ['nullable', 'digits:6'],
            'dispositivo' => ['nullable', 'string', 'max:60'],
        ];
    }
}
