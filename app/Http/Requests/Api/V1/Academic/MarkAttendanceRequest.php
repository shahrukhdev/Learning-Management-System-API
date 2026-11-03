<?php

namespace App\Http\Requests\Api\V1\Academic;

use App\Enums\AttendanceStatus;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class MarkAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'academic_class_id' => [
                'required',
                'integer',
                'exists:academic_classes,id',
            ],
            'attendance_date' => [
                'required',
                'date_format:Y-m-d',
            ],
            'attendances' => [
                'required',
                'array',
                'min:1',
            ],
            'attendances.*.student_id' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('academic_class_student', 'student_id')
                    ->where('academic_class_id', $this->input('academic_class_id')),
            ],
            'attendances.*.status' => [
                'required',
                'string',
                Rule::in(array_column(AttendanceStatus::cases(), 'value')),
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