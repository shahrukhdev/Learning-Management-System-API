<?php

namespace App\Http\Requests\Api\V1\Academic;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $studentId = $this->route('student');

        return [
            'user_id' => [
                'sometimes',
                'nullable',
                'integer',
                'exists:users,id',
                Rule::unique('students', 'user_id')->ignore($studentId),
            ],
            'admission_number' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                Rule::unique('students', 'admission_number')->ignore($studentId),
            ],
            'date_of_birth' => [
                'sometimes',
                'nullable',
                'date',
            ],
            'gender' => [
                'sometimes',
                'nullable',
                'string',
                'max:20',
            ],
            'phone' => [
                'sometimes',
                'nullable',
                'string',
                'max:30',
            ],
            'address' => [
                'sometimes',
                'nullable',
                'string',
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
