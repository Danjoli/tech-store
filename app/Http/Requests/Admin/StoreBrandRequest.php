<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBrandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('brands', 'name'),
            ],
            'description' => [
                'nullable',
                'string',
                'max:5000',
            ],
            'website_url' => [
                'nullable',
                'url:http,https',
                'max:2048',
            ],
            'logo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'dimensions:max_width=3000,max_height=3000',
                'max:2048',
            ],
            'is_active' => [
                'required',
                'boolean',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Informe o nome da marca.',
            'name.unique' => 'Já existe uma marca com esse nome.',
            'website_url.url' => 'Informe uma URL válida.',
            'logo.image' => 'O logo deve ser uma imagem.',
            'logo.mimes' => 'O logo deve ser JPG, PNG ou WebP.',
            'logo.dimensions' => 'O logo deve possuir no máximo 3000 por 3000 pixels.',
            'logo.max' => 'O logo deve possuir no máximo 2 MB.',
        ];
    }
}
