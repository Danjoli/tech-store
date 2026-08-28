<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCategoryRequest extends FormRequest
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
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('categories', 'id')
                    ->whereNull('parent_id')
                    ->whereNull('deleted_at'),
            ],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name'),
            ],
            'description' => [
                'nullable',
                'string',
                'max:5000',
            ],
            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'dimensions:max_width=3000,max_height=3000',
                'max:2048',
            ],
            'sort_order' => [
                'required',
                'integer',
                'min:0',
                'max:65535',
            ],
            'is_active' => [
                'required',
                'boolean',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'parent_id.exists' => 'Selecione uma categoria principal válida.',
            'name.required' => 'Informe o nome da categoria.',
            'name.unique' => 'Já existe uma categoria com esse nome.',
            'image.image' => 'O arquivo deve ser uma imagem.',
            'image.mimes' => 'A imagem deve ser JPG, PNG ou WebP.',
            'image.dimensions' => 'A imagem deve possuir no máximo 3000 por 3000 pixels.',
            'image.max' => 'A imagem deve possuir no máximo 2 MB.',
            'sort_order.required' => 'Informe a ordem de exibição.',
            'sort_order.min' => 'A ordem não pode ser negativa.',
        ];
    }
}
