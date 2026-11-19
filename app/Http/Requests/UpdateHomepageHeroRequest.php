<?php

namespace App\Http\Requests;

use Closure;
use Illuminate\Foundation\Http\FormRequest;

class UpdateHomepageHeroRequest extends FormRequest
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
            'mobile_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
            'web_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'url' => ['nullable', 'string', 'max:512', function (string $attribute, mixed $value, Closure $fail) {
                if (! is_string($value)) {
                    return;
                }

                $scheme = parse_url($value, PHP_URL_SCHEME);
                $isInternalPath = preg_match('#^/(?!/)#', $value) === 1;
                $isWebUrl = filter_var($value, FILTER_VALIDATE_URL) !== false
                    && in_array(mb_strtolower((string) $scheme), ['http', 'https'], true);

                if (! $isInternalPath && ! $isWebUrl) {
                    $fail('The banner link must be an internal path beginning with / or a complete HTTP/HTTPS URL.');
                }
            }],
        ];
    }
}
