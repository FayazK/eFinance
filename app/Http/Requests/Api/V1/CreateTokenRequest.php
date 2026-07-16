<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateTokenRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'abilities' => ['sometimes', 'array'],
            'abilities.*' => ['string', Rule::in($this->availableAbilities())],
        ];
    }

    /**
     * The full token-ability vocabulary: every module.action plus the "*" wildcard.
     *
     * @return list<string>
     */
    protected function availableAbilities(): array
    {
        $abilities = ['*'];

        foreach (config('permissions.modules', []) as $module => $config) {
            foreach ($config['permissions'] as $action) {
                $abilities[] = "{$module}.{$action}";
            }
        }

        return $abilities;
    }
}
