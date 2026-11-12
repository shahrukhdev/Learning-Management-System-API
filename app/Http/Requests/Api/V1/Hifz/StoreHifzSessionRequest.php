<?php

namespace App\Http\Requests\Api\V1\Hifz;

use App\Models\Surah;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class StoreHifzSessionRequest extends FormRequest
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
            'session_date' => [
                'required',
                'date_format:Y-m-d',
            ],
            'notes' => [
                'nullable',
                'string',
            ],

            'old_sabaq' => [
                'sometimes',
                'array',
            ],
            'old_sabaq.*.from_surah_id' => [
                'required',
                'integer',
                'exists:surahs,id',
            ],
            'old_sabaq.*.to_surah_id' => [
                'required',
                'integer',
                'exists:surahs,id',
            ],
            'old_sabaq.*.page' => [
                'nullable',
                'integer',
                'min:0',
            ],
            'old_sabaq.*.status' => [
                'nullable',
                Rule::in(['fluent', 'mistakes', 'repeat', 'needs-revision']),
            ],
            'old_sabaq.*.strength' => [
                'nullable',
                Rule::in(['strong', 'good', 'needs-work', 'weak']),
            ],
            'old_sabaq.*.mistakes' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'manzil' => [
                'sometimes',
                'array',
            ],
            'manzil.*.from_surah_id' => [
                'required',
                'integer',
                'exists:surahs,id',
            ],
            'manzil.*.to_surah_id' => [
                'required',
                'integer',
                'exists:surahs,id',
            ],
            'manzil.*.status' => [
                'nullable',
                Rule::in(['fluent', 'mistakes', 'repeat', 'needs-revision']),
            ],
            'manzil.*.strength' => [
                'nullable',
                Rule::in(['strong', 'good', 'needs-work', 'weak']),
            ],
            'manzil.*.mistakes' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'new_sabaq' => [
                'sometimes',
                'array',
            ],
            'new_sabaq.*.from_surah_id' => [
                'required',
                'integer',
                'exists:surahs,id',
            ],
            'new_sabaq.*.from_ayah' => [
                'required',
                'integer',
                'min:1',
            ],
            'new_sabaq.*.to_surah_id' => [
                'required',
                'integer',
                'exists:surahs,id',
            ],
            'new_sabaq.*.to_ayah' => [
                'required',
                'integer',
                'min:1',
            ],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $oldSabaq = $this->input('old_sabaq', []);
            $manzil = $this->input('manzil', []);
            $newSabaq = $this->input('new_sabaq', []);

            $oldSabaq = is_array($oldSabaq) ? $oldSabaq : [];
            $manzil = is_array($manzil) ? $manzil : [];
            $newSabaq = is_array($newSabaq) ? $newSabaq : [];

            if (
                count($oldSabaq) === 0 &&
                count($manzil) === 0 &&
                count($newSabaq) === 0
            ) {
                $validator->errors()->add(
                    'old_sabaq',
                    'At least one Old Sabaq, Manzil, or New Sabaq entry is required.'
                );
            }

            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $surahIds = [];

            foreach ([$oldSabaq, $manzil, $newSabaq] as $entries) {
                foreach ($entries as $entry) {
                    $surahIds[] = $entry['from_surah_id'];
                    $surahIds[] = $entry['to_surah_id'];
                }
            }

            $surahs = Surah::query()
                ->whereIn('id', array_unique($surahIds))
                ->get(['id', 'number', 'ayahs'])
                ->keyBy('id');

            $this->validateSurahRanges($validator, $surahs, 'old_sabaq');
            $this->validateSurahRanges($validator, $surahs, 'manzil');
            $this->validateNewSabaqRanges($validator, $surahs);
        });
    }

    private function validateSurahRanges($validator, $surahs, string $section): void
    {
        foreach ($this->input($section, []) as $index => $entry) {
            $fromSurah = $surahs->get($entry['from_surah_id']);
            $toSurah = $surahs->get($entry['to_surah_id']);

            if ($fromSurah && $toSurah && $toSurah->number < $fromSurah->number) {
                $validator->errors()->add(
                    "{$section}.{$index}.to_surah_id",
                    'The ending Surah cannot be before the starting Surah.'
                );
            }
        }
    }

    private function validateNewSabaqRanges($validator, $surahs): void
    {
        foreach ($this->input('new_sabaq', []) as $index => $entry) {
            $fromSurah = $surahs->get($entry['from_surah_id']);
            $toSurah = $surahs->get($entry['to_surah_id']);

            if (! $fromSurah || ! $toSurah) {
                continue;
            }

            if ($entry['from_ayah'] > $fromSurah->ayahs) {
                $validator->errors()->add(
                    "new_sabaq.{$index}.from_ayah",
                    "The starting Ayah cannot exceed {$fromSurah->ayahs}."
                );
            }

            if ($entry['to_ayah'] > $toSurah->ayahs) {
                $validator->errors()->add(
                    "new_sabaq.{$index}.to_ayah",
                    "The ending Ayah cannot exceed {$toSurah->ayahs}."
                );
            }

            if (
                $toSurah->number < $fromSurah->number ||
                (
                    $toSurah->number === $fromSurah->number &&
                    $entry['to_ayah'] < $entry['from_ayah']
                )
            ) {
                $validator->errors()->add(
                    "new_sabaq.{$index}.to_ayah",
                    'The ending position cannot be before the starting position.'
                );
            }
        }
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
