<?php

namespace App\Http\Requests\Admin;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
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
        /** @var Category $category */
        $category = $this->route('category');

        return [
            'parent_id' => [
                'nullable',
                'integer',
                Rule::notIn([$category->id]),
                Rule::exists('categories', 'id')
                    ->whereNull('parent_id')
                    ->whereNull('deleted_at'),
            ],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')->ignore($category),
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
                'max:2048',
            ],
            'remove_image' => [
                'sometimes',
                'boolean',
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
            'parent_id.not_in' => 'Uma categoria não pode ser pai dela mesma.',
            'parent_id.exists' => 'Selecione uma categoria principal válida.',
            'name.required' => 'Informe o nome da categoria.',
            'name.unique' => 'Já existe uma categoria com esse nome.',
            'image.image' => 'O arquivo deve ser uma imagem.',
            'image.mimes' => 'A imagem deve ser JPG, PNG ou WebP.',
            'image.max' => 'A imagem deve possuir no máximo 2 MB.',
            'sort_order.required' => 'Informe a ordem de exibição.',
            'sort_order.min' => 'A ordem não pode ser negativa.',
        ];
    }
}
