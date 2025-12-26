<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProjectDocumentUploadRequest extends FormRequest
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
            'document' => [
                'required',
                'file',
                'max:10240', // 10MB
                'mimes:pdf,doc,docx,xls,xlsx',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'document.mimes' => 'Only PDF, DOC, DOCX, XLS, and XLSX files are allowed.',
            'document.max' => 'The document must not exceed 10MB.',
        ];
    }
}
