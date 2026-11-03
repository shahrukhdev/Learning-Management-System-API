<?php

namespace App\Http\Requests\Api\V1\Hifz;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class StoreStudentSurahProgressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'surah_id' => [
                'required',
                'integer',
                'exists:surahs,id',
                Rule::unique('student_surah_progress', 'surah_id')
                    ->where('student_id', $this->route('studentId')),
            ],
            'status' => ['required', Rule::in(['memorised', 'progress'])],
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