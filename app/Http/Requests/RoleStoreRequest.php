<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoleStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:roles,slug'],
            'description' => ['nullable', 'string', 'max:1000'],
            'permissions' => ['required', 'array'],
            'permissions.*' => ['string', Rule::in($this->validPermissions())],
            'is_default' => ['boolean'],
        ];
    }

    /**
     * Get all valid permission strings from config.
     *
     * @return array<int, string>
     */
    private function validPermissions(): array
    {
        $permissions = [];
        foreach (config('permissions.modules') as $module => $config) {
            foreach ($config['permissions'] as $action) {
                $permissions[] = "{$module}.{$action}";
            }
        }

        return $permissions;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'permissions.required' => 'At least one permission must be selected.',
        ];
    }
}
