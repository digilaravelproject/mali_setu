<?php

namespace App\Http\Requests;

use Closure;
use Illuminate\Foundation\Http\FormRequest;

class StoreHomepageHeroRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required','string','max:255'],
            'functionality_name' => ['nullable', 'string', 'max:120'],
            'event_name' => ['nullable', 'string', 'max:120'],
            'mobile_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:3072',
            'web_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'url' => ['nullable', 'string', 'max:512', function (string $attribute, mixed $value, Closure $fail) {
                if (! is_string($value)) {
                    return;
                }

                $isInternalPath = preg_match('#^/(?!/)#', $value) === 1;
                if (! $isInternalPath) {
                    $fail('The internal URL must begin with /.');
                }
            }],
            'external_url' => ['nullable', 'url:http,https', 'max:512'],
        ];
    }
}
