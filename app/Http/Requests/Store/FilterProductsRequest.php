<?php

namespace App\Http\Requests\Store;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FilterProductsRequest extends FormRequest
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
            'search' => [
                'nullable',
                'string',
                'max:100',
            ],
            'category' => [
                'nullable',
                'string',
                'max:255',
            ],
            'brand' => [
                'nullable',
                'string',
                'max:255',
            ],
            'min_price' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'max_price' => [
                'nullable',
                'numeric',
                'min:0',
                'gte:min_price',
            ],
            'featured' => [
                'nullable',
                'boolean',
            ],
            'on_sale' => [
                'nullable',
                'boolean',
            ],
            'in_stock' => [
                'nullable',
                'boolean',
            ],
            'sort' => [
                'nullable',
                Rule::in([
                    'newest',
                    'price_asc',
                    'price_desc',
                    'name_asc',
                    'name_desc',
                ]),
            ],
            'page' => [
                'nullable',
                'integer',
                'min:1',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'search.max' => 'A busca deve possuir no máximo 100 caracteres.',
            'min_price.min' => 'O preço mínimo não pode ser negativo.',
            'max_price.min' => 'O preço máximo não pode ser negativo.',
            'max_price.gte' => 'O preço máximo deve ser maior ou igual ao preço mínimo.',
            'sort.in' => 'Selecione uma ordenação válida.',
        ];
    }
}
