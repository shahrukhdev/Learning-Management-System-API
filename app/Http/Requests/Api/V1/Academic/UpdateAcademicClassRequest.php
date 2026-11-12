<?php

namespace App\Http\Requests\Api\V1\Academic;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateAcademicClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $classId = $this->route('id');

        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('academic_classes', 'code')->ignore($classId),
            ],
            'description' => ['nullable', 'string'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['sometimes', 'boolean'],
            'course_ids' => ['sometimes', 'array'],
            'course_ids.*' => [
                'integer',
                'distinct',
                Rule::exists('courses', 'id'),
            ],
            'teacher_ids' => ['sometimes', 'array'],
            'teacher_ids.*' => [
                'integer',
                'distinct',
                Rule::exists('teachers', 'id')->whereNull('deleted_at'),
            ],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}