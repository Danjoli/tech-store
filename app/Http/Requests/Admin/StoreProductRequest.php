<?php

namespace App\Http\Requests\Admin;

use App\Enums\ProductStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
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
            'brand_id' => [
                'nullable',
                'integer',
                Rule::exists('brands', 'id')
                    ->whereNull('deleted_at'),
            ],
            'category_id' => [
                'required',
                'integer',
                Rule::exists('categories', 'id')
                    ->whereNull('deleted_at'),
            ],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'name'),
            ],
            'short_description' => [
                'nullable',
                'string',
                'max:500',
            ],
            'description' => [
                'nullable',
                'string',
                'max:30000',
            ],
            'status' => [
                'required',
                Rule::enum(ProductStatus::class),
            ],
            'is_featured' => [
                'required',
                'boolean',
            ],
            'warranty_months' => [
                'nullable',
                'integer',
                'min:0',
                'max:65535',
            ],
            'weight' => [
                'nullable',
                'numeric',
                'min:0',
                'max:9999999.999',
            ],
            'height' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
            'width' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
            'length' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
            'seo_title' => [
                'nullable',
                'string',
                'max:255',
            ],
            'seo_description' => [
                'nullable',
                'string',
                'max:1000',
            ],

            // Variante padrão
            'variant_name' => [
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
            'variant_is_active' => [
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
            'category_id.required' => 'Selecione uma categoria.',
            'category_id.exists' => 'Selecione uma categoria válida.',
            'brand_id.exists' => 'Selecione uma marca válida.',
            'name.required' => 'Informe o nome do produto.',
            'name.unique' => 'Já existe um produto com esse nome.',
            'status.required' => 'Selecione a situação do produto.',
            'variant_name.required' => 'Informe o nome da variante.',
            'sku.required' => 'Informe o SKU.',
            'sku.unique' => 'Este SKU já está sendo utilizado.',
            'barcode.unique' => 'Este código de barras já está sendo utilizado.',
            'price.required' => 'Informe o preço do produto.',
            'price.min' => 'O preço deve ser maior que zero.',
            'sale_price.lt' => 'O preço promocional deve ser menor que o preço normal.',
            'stock.required' => 'Informe a quantidade em estoque.',
            'low_stock_threshold.required' => 'Informe o limite de estoque baixo.',
        ];
    }
}
