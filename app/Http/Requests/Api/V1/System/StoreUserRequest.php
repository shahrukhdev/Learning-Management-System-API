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
        ];
    }

    public function messages(): array
    {
        return [
            'class_id.required' => 'The class field is required for students.',
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