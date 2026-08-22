<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductImagesRequest extends FormRequest
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
            'images' => [
                'required',
                'array',
                'min:1',
                'max:10',
            ],
            'images.*' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'images.required' => 'Selecione pelo menos uma imagem.',
            'images.max' => 'Envie no máximo 10 imagens por vez.',
            'images.*.image' => 'Todos os arquivos devem ser imagens.',
            'images.*.mimes' => 'As imagens devem ser JPG, PNG ou WebP.',
            'images.*.max' => 'Cada imagem deve possuir no máximo 4 MB.',
        ];
    }
}
