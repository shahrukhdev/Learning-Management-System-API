<?php

namespace App\Http\Requests\Api\V1\System;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
            ],
            'phone_no' => [
                'nullable',
                'string',
                'max:20',
            ],
            'is_active' => [
                'sometimes',
                'boolean',
            ],
            'roles' => [
                'required',
                'array',
                'min:1',
            ],
            'roles.*' => [
                'integer',
                'distinct',
                Rule::exists('roles', 'id')
                    ->where('guard_name', 'web'),
            ],
            'class_id' => [
                Rule::requiredIf(fn () => $this->hasStudentRole()),
                'nullable',
                'integer',
                Rule::exists('academic_classes', 'id'),
            ],
            'year' => [
                Rule::requiredIf(fn () => $this->hasStudentRole()),
                'nullable',
                'integer',
                'min:2000',
                'max:' . (now()->year + 1),
            ],
            'class_ids' => [
                Rule::requiredIf(fn () => $this->hasTeacherRole()),
                'nullable',
                'array',
                'min:1',
            ],
            'class_ids.*' => [
                'integer',
                'distinct',
                Rule::exists('academic_classes', 'id'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'class_id.required' => 'The class field is required for students.',
            'year.required' => 'The year field is required for students.',
            'year.min' => 'The year must be 2000 or later.',
            'year.max' => 'The year cannot be later than next year.',
            'class_ids.required' => 'At least one class is required for teachers.',
            'class_ids.min' => 'At least one class is required for teachers.',
        ];
    }

    private function hasStudentRole(): bool
    {
        $roleIds = array_filter((array) $this->input('roles', []), 'is_numeric');

        return !empty($roleIds) && Role::query()
            ->whereIn('id', $roleIds)
            ->where('name', 'Student')
            ->where('guard_name', 'web')
            ->exists();
    }
    
    private function hasTeacherRole(): bool
    {
        $roleIds = array_filter((array) $this->input('roles', []), 'is_numeric');

        return !empty($roleIds) && Role::query()
            ->whereIn('id', $roleIds)
            ->where('name', 'Teacher')
            ->where('guard_name', 'web')
            ->exists();
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