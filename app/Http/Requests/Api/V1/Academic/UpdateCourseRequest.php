<?php

namespace App\Http\Requests\Api\V1\Academic;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $courseId = $this->route('id');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'code' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('courses', 'code')->ignore($courseId),
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'duration' => [
                'nullable',
                'integer',
                'min:1',
            ],
            'is_active' => [
                'sometimes',
                'boolean',
            ],
            'academic_class_ids' => [
                'sometimes',
                'array',
            ],
            'academic_class_ids.*' => [
                'integer',
                'distinct',
                Rule::exists('academic_classes', 'id'),
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