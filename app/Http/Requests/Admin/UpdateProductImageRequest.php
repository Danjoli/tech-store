<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductImageRequest extends FormRequest
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
            'product_variant_id' => [
                'nullable',
                'integer',
                Rule::exists('product_variants', 'id'),
            ],
            'alt_text' => [
                'nullable',
                'string',
                'max:255',
            ],
            'sort_order' => [
                'required',
                'integer',
                'min:0',
                'max:65535',
            ],
            'is_primary' => [
                'required',
                'boolean',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'product_variant_id.exists' => 'Selecione uma variante válida.',
            'alt_text.max' => 'O texto alternativo deve possuir no máximo 255 caracteres.',
            'sort_order.required' => 'Informe a ordem da imagem.',
            'sort_order.min' => 'A ordem não pode ser negativa.',
        ];
    }
}
