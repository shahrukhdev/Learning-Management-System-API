<?php

namespace App\Http\Requests\Api\V1\Hifz;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class GetStudentHifzSessionsRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'student_id' => $this->route('studentId'),
        ]);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => [
                'required',
                'integer',
                'exists:students,id',
            ],
            'date' => [
                'nullable',
                'date_format:Y-m-d',
            ],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'The given data was invalid.',
            'errors' => $validator->errors(),
        ], 422));
    }
}
