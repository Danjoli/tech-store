<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3';

interface Option {
    id: number;
    name: string;
}

interface StatusOption {
    value: string;
    label: string;
}

interface ProductData {
    id: number;
    brand_id: number | null;
    category_id: number;
    name: string;
    short_description: string | null;
    description: string | null;
    status: string;
    is_featured: boolean;
    warranty_months: number | null;
    weight: string | null;
    height: string | null;
    width: string | null;
    length: string | null;
    seo_title: string | null;
    seo_description: string | null;
    variant_name: string;
    sku: string;
    barcode: string | null;
    price: string;
    sale_price: string | null;
    cost_price: string | null;
    stock: number;
    low_stock_threshold: number;
    variant_is_active: boolean;
}

const props = defineProps<{
    product?: ProductData;
    brands: Option[];
    categories: Option[];
    statuses: StatusOption[];
}>();

const isEditing = Boolean(props.product);

const form = useForm({
    brand_id: props.product?.brand_id ?? null,
    category_id: props.product?.category_id ?? null,
    name: props.product?.name ?? '',
    short_description: props.product?.short_description ?? '',
    description: props.product?.description ?? '',
    status: props.product?.status ?? 'draft',
    is_featured: props.product?.is_featured ?? false,
    warranty_months: props.product?.warranty_months ?? null,
    weight: props.product?.weight ?? '',
    height: props.product?.height ?? '',
    width: props.product?.width ?? '',
    length: props.product?.length ?? '',
    seo_title: props.product?.seo_title ?? '',
    seo_description: props.product?.seo_description ?? '',

    variant_name: props.product?.variant_name ?? 'Padrão',
    sku: props.product?.sku ?? '',
    barcode: props.product?.barcode ?? '',
    price: props.product?.price ?? '',
    sale_price: props.product?.sale_price ?? '',
    cost_price: props.product?.cost_price ?? '',
    stock: props.product?.stock ?? 0,
    low_stock_threshold:
        props.product?.low_stock_threshold ?? 5,
    variant_is_active:
        props.product?.variant_is_active ?? true,
});

function submit(): void {
    if (isEditing && props.product) {
        form.put(`/admin/products/${props.product.id}`, {
            preserveScroll: true,
        });

        return;
    }

    form.post('/admin/products', {
        preserveScroll: true,
    });
}
</script>

<template>
    <form class="space-y-6" @submit.prevent="submit">
        <section
            class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm"
        >
            <div class="mb-6">
                <h2 class="text-lg font-bold text-slate-900">
                    Informações gerais
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Identificação e apresentação do produto.
                </p>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="lg:col-span-2">
                    <label
                        for="name"
                        class="mb-2 block text-sm font-semibold text-slate-700"
                    >
                        Nome do produto
                    </label>

                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        maxlength="255"
                        class="w-full rounded-lg border px-4 py-3 outline-none focus:ring-2"
                        :class="
                            form.errors.name
                                ? 'border-red-400 focus:ring-red-100'
                                : 'border-slate-300 focus:border-blue-500 focus:ring-blue-100'
                        "
                    />

                    <p
                        v-if="form.errors.name"
                        class="mt-2 text-sm text-red-600"
                    >
                        {{ form.errors.name }}
                    </p>
                </div>

                <div>
                    <label
                        for="category_id"
                        class="mb-2 block text-sm font-semibold text-slate-700"
                    >
                        Categoria
                    </label>

                    <select
                        id="category_id"
                        v-model="form.category_id"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    >
                        <option :value="null" disabled>
                            Selecione uma categoria
                        </option>

                        <option
                            v-for="category in categories"
                            :key="category.id"
                            :value="category.id"
                        >
                            {{ category.name }}
                        </option>
                    </select>

                    <p
                        v-if="form.errors.category_id"
                        class="mt-2 text-sm text-red-600"
                    >
                        {{ form.errors.category_id }}
                    </p>
                </div>

                <div>
                    <label
                        for="brand_id"
                        class="mb-2 block text-sm font-semibold text-slate-700"
                    >
                        Marca
                    </label>

                    <select
                        id="brand_id"
                        v-model="form.brand_id"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    >
                        <option :value="null">Sem marca</option>

                        <option
                            v-for="brand in brands"
                            :key="brand.id"
                            :value="brand.id"
                        >
                            {{ brand.name }}
                        </option>
                    </select>

                    <p
                        v-if="form.errors.brand_id"
                        class="mt-2 text-sm text-red-600"
                    >
                        {{ form.errors.brand_id }}
                    </p>
                </div>

                <div>
                    <label
                        for="status"
                        class="mb-2 block text-sm font-semibold text-slate-700"
                    >
                        Situação
                    </label>

                    <select
                        id="status"
                        v-model="form.status"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    >
                        <option
                            v-for="status in statuses"
                            :key="status.value"
                            :value="status.value"
                        >
                            {{ status.label }}
                        </option>
                    </select>

                    <p
                        v-if="form.errors.status"
                        class="mt-2 text-sm text-red-600"
                    >
                        {{ form.errors.status }}
                    </p>
                </div>

                <div class="flex items-end pb-3">
                    <label class="flex cursor-pointer items-center gap-3">
                        <input
                            v-model="form.is_featured"
                            type="checkbox"
                            class="h-5 w-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                        />

                        <span class="text-sm font-semibold text-slate-700">
                            Produto em destaque
                        </span>
                    </label>
                </div>

                <div class="lg:col-span-2">
                    <label
                        for="short_description"
                        class="mb-2 block text-sm font-semibold text-slate-700"
                    >
                        Descrição curta
                    </label>

                    <textarea
                        id="short_description"
                        v-model="form.short_description"
                        rows="3"
                        maxlength="500"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    />

                    <p
                        v-if="form.errors.short_description"
                        class="mt-2 text-sm text-red-600"
                    >
                        {{ form.errors.short_description }}
                    </p>
                </div>

                <div class="lg:col-span-2">
                    <label
                        for="description"
                        class="mb-2 block text-sm font-semibold text-slate-700"
                    >
                        Descrição completa
                    </label>

                    <textarea
                        id="description"
                        v-model="form.description"
                        rows="8"
                        maxlength="30000"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    />

                    <p
                        v-if="form.errors.description"
                        class="mt-2 text-sm text-red-600"
                    >
                        {{ form.errors.description }}
                    </p>
                </div>
            </div>
        </section>

        <section
            class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm"
        >
            <div class="mb-6">
                <h2 class="text-lg font-bold text-slate-900">
                    Variante padrão
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Preço, identificação e estoque inicial.
                </p>
            </div>

            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                <div>
                    <label
                        for="variant_name"
                        class="mb-2 block text-sm font-semibold text-slate-700"
                    >
                        Nome da variante
                    </label>

                    <input
                        id="variant_name"
                        v-model="form.variant_name"
                        type="text"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    />

                    <p
                        v-if="form.errors.variant_name"
                        class="mt-2 text-sm text-red-600"
                    >
                        {{ form.errors.variant_name }}
                    </p>
                </div>

                <div>
                    <label
                        for="sku"
                        class="mb-2 block text-sm font-semibold text-slate-700"
                    >
                        SKU
                    </label>

                    <input
                        id="sku"
                        v-model="form.sku"
                        type="text"
                        autocomplete="off"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 uppercase outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    />

                    <p
                        v-if="form.errors.sku"
                        class="mt-2 text-sm text-red-600"
                    >
                        {{ form.errors.sku }}
                    </p>
                </div>

                <div>
                    <label
                        for="barcode"
                        class="mb-2 block text-sm font-semibold text-slate-700"
                    >
                        Código de barras
                    </label>

                    <input
                        id="barcode"
                        v-model="form.barcode"
                        type="text"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    />

                    <p
                        v-if="form.errors.barcode"
                        class="mt-2 text-sm text-red-600"
                    >
                        {{ form.errors.barcode }}
                    </p>
                </div>

                <div>
                    <label
                        for="price"
                        class="mb-2 block text-sm font-semibold text-slate-700"
                    >
                        Preço
                    </label>

                    <input
                        id="price"
                        v-model="form.price"
                        type="number"
                        min="0.01"
                        step="0.01"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    />

                    <p
                        v-if="form.errors.price"
                        class="mt-2 text-sm text-red-600"
                    >
                        {{ form.errors.price }}
                    </p>
                </div>

                <div>
                    <label
                        for="sale_price"
                        class="mb-2 block text-sm font-semibold text-slate-700"
                    >
                        Preço promocional
                    </label>

                    <input
                        id="sale_price"
                        v-model="form.sale_price"
                        type="number"
                        min="0.01"
                        step="0.01"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    />

                    <p
                        v-if="form.errors.sale_price"
                        class="mt-2 text-sm text-red-600"
                    >
                        {{ form.errors.sale_price }}
                    </p>
                </div>

                <div>
                    <label
                        for="cost_price"
                        class="mb-2 block text-sm font-semibold text-slate-700"
                    >
                        Preço de custo
                    </label>

                    <input
                        id="cost_price"
                        v-model="form.cost_price"
                        type="number"
                        min="0"
                        step="0.01"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    />

                    <p
                        v-if="form.errors.cost_price"
                        class="mt-2 text-sm text-red-600"
                    >
                        {{ form.errors.cost_price }}
                    </p>
                </div>

                <div>
                    <label
                        for="stock"
                        class="mb-2 block text-sm font-semibold text-slate-700"
                    >
                        Estoque
                    </label>

                    <input
                        id="stock"
                        v-model.number="form.stock"
                        type="number"
                        min="0"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    />

                    <p
                        v-if="form.errors.stock"
                        class="mt-2 text-sm text-red-600"
                    >
                        {{ form.errors.stock }}
                    </p>
                </div>

                <div>
                    <label
                        for="low_stock_threshold"
                        class="mb-2 block text-sm font-semibold text-slate-700"
                    >
                        Limite de estoque baixo
                    </label>

                    <input
                        id="low_stock_threshold"
                        v-model.number="form.low_stock_threshold"
                        type="number"
                        min="0"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    />

                    <p
                        v-if="form.errors.low_stock_threshold"
                        class="mt-2 text-sm text-red-600"
                    >
                        {{ form.errors.low_stock_threshold }}
                    </p>
                </div>

                <div class="flex items-end pb-3">
                    <label class="flex cursor-pointer items-center gap-3">
                        <input
                            v-model="form.variant_is_active"
                            type="checkbox"
                            class="h-5 w-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                        />

                        <span class="text-sm font-semibold text-slate-700">
                            Variante ativa
                        </span>
                    </label>
                </div>
            </div>
        </section>

        <section
            class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm"
        >
            <div class="mb-6">
                <h2 class="text-lg font-bold text-slate-900">
                    Logística
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Informações usadas posteriormente no cálculo do frete.
                </p>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-5">
                <div>
                    <label
                        for="warranty_months"
                        class="mb-2 block text-sm font-semibold text-slate-700"
                    >
                        Garantia (meses)
                    </label>

                    <input
                        id="warranty_months"
                        v-model="form.warranty_months"
                        type="number"
                        min="0"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3"
                    />
                </div>

                <div>
                    <label
                        for="weight"
                        class="mb-2 block text-sm font-semibold text-slate-700"
                    >
                        Peso (kg)
                    </label>

                    <input
                        id="weight"
                        v-model="form.weight"
                        type="number"
                        min="0"
                        step="0.001"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3"
                    />
                </div>

                <div>
                    <label
                        for="height"
                        class="mb-2 block text-sm font-semibold text-slate-700"
                    >
                        Altura (cm)
                    </label>

                    <input
                        id="height"
                        v-model="form.height"
                        type="number"
                        min="0"
                        step="0.01"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3"
                    />
                </div>

                <div>
                    <label
                        for="width"
                        class="mb-2 block text-sm font-semibold text-slate-700"
                    >
                        Largura (cm)
                    </label>

                    <input
                        id="width"
                        v-model="form.width"
                        type="number"
                        min="0"
                        step="0.01"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3"
                    />
                </div>

                <div>
                    <label
                        for="length"
                        class="mb-2 block text-sm font-semibold text-slate-700"
                    >
                        Comprimento (cm)
                    </label>

                    <input
                        id="length"
                        v-model="form.length"
                        type="number"
                        min="0"
                        step="0.01"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3"
                    />
                </div>
            </div>
        </section>

        <section
            class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm"
        >
            <div class="mb-6">
                <h2 class="text-lg font-bold text-slate-900">
                    SEO
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Informações para mecanismos de busca.
                </p>
            </div>

            <div class="grid gap-6">
                <div>
                    <label
                        for="seo_title"
                        class="mb-2 block text-sm font-semibold text-slate-700"
                    >
                        Título SEO
                    </label>

                    <input
                        id="seo_title"
                        v-model="form.seo_title"
                        type="text"
                        maxlength="255"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3"
                    />
                </div>

                <div>
                    <label
                        for="seo_description"
                        class="mb-2 block text-sm font-semibold text-slate-700"
                    >
                        Descrição SEO
                    </label>

                    <textarea
                        id="seo_description"
                        v-model="form.seo_description"
                        rows="3"
                        maxlength="1000"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3"
                    />
                </div>
            </div>
        </section>

        <div class="flex justify-end gap-3">
            <Link
                href="/admin/products"
                class="rounded-lg border border-slate-300 px-5 py-3 text-sm font-bold text-slate-700 hover:bg-slate-100"
            >
                Cancelar
            </Link>

            <button
                type="submit"
                :disabled="form.processing"
                class="rounded-lg bg-blue-600 px-5 py-3 text-sm font-bold text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
            >
                {{
                    form.processing
                        ? 'Salvando...'
                        : isEditing
                          ? 'Salvar alterações'
                          : 'Cadastrar produto'
                }}
            </button>
        </div>
    </form>
</template>
