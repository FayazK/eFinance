<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoleUpdateRequest extends FormRequest
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
        // Web route binds `{role}` (a Role model); the /api/v1/roles/{id} route binds a raw
        // `{id}`. Fall back so the unique-slug rule ignores THIS role on both surfaces (else a
        // same-slug PUT on the API route 422s against itself).
        $roleId = $this->route('role')?->id ?? $this->route('id') ?? $this->route('role');

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('roles', 'slug')->ignore($roleId),
            ],
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
