<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductVariantRequest extends FormRequest
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
            ],
            'sku' => [
                'required',
                'string',
                'max:255',
                Rule::unique('product_variants', 'sku'),
            ],
            'barcode' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('product_variants', 'barcode'),
            ],
            'price' => [
                'required',
                'numeric',
                'min:0.01',
                'max:9999999999.99',
            ],
            'sale_price' => [
                'nullable',
                'numeric',
                'min:0.01',
                'lt:price',
            ],
            'cost_price' => [
                'nullable',
                'numeric',
                'min:0',
                'max:9999999999.99',
            ],
            'stock' => [
                'required',
                'integer',
                'min:0',
                'max:4294967295',
            ],
            'low_stock_threshold' => [
                'required',
                'integer',
                'min:0',
                'max:4294967295',
            ],
            'attributes' => [
                'nullable',
                'array',
                'max:20',
            ],
            'attributes.*' => [
                'nullable',
                'string',
                'max:255',
            ],
            'is_default' => [
                'required',
                'boolean',
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
            'name.required' => 'Informe o nome da variante.',
            'sku.required' => 'Informe o SKU.',
            'sku.unique' => 'Este SKU já está sendo utilizado.',
            'barcode.unique' => 'Este código de barras já está sendo utilizado.',
            'price.required' => 'Informe o preço.',
            'price.min' => 'O preço deve ser maior que zero.',
            'sale_price.lt' => 'O preço promocional deve ser menor que o preço normal.',
            'stock.required' => 'Informe a quantidade em estoque.',
            'low_stock_threshold.required' => 'Informe o limite de estoque baixo.',
            'attributes.max' => 'A variante pode possuir no máximo 20 atributos.',
        ];
    }
}
