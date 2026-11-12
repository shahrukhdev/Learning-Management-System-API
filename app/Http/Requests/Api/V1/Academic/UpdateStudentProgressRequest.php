<?php

namespace App\Http\Requests\Api\V1\Academic;

use App\Enums\ProgressGrade;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateStudentProgressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'course_id' => ['sometimes', 'required', 'integer', 'exists:courses,id'],
            'lesson' => ['sometimes', 'required', 'string', 'max:255'],
            'grade' => ['sometimes', 'required', 'string', Rule::enum(ProgressGrade::class)],
            'notes' => ['sometimes', 'nullable', 'string', 'max:5000'],
            'progress_date' => ['sometimes', 'required', 'date_format:Y-m-d', 'before_or_equal:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'course_id.required' => 'The subject field is required.',
            'course_id.exists' => 'The selected subject is invalid.',
            'lesson.required' => 'The lesson / topic field is required.',
            'grade.enum' => 'The selected grade is invalid.',
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
