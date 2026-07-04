<?php

namespace App\Http\Requests;

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
            'mobile_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:3072|dimensions:width=1080,height=1350',
            'web_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120|dimensions:width=1920,height=700',
        ];
    }
}
