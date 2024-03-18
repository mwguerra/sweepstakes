<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class SweepstakesUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => $this->slug
                ? Str::slug($this->slug)
                : Str::slug($this->title),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'string|max:255',
            'slug' => 'string|max:255|unique:sweepstakes,slug,' . $this->sweepstakes->id,
            'description' => 'string',
            'draw_time' => 'date|after:now',
            'timezone' => 'string|timezone',
            'prize' => 'string',
            'keptFiles' => 'array',
            'keptFiles.*' => 'integer',
            'removedFiles' => 'array',
            'removedFiles.*' => 'integer',
            'newFiles' => 'array',
            'newFiles.*' => 'file|mimes:jpg,jpeg,png,pdf,xls,xlsx,xlsm,ppt,pptx,txt,md|max:2048',
        ];
    }
}
