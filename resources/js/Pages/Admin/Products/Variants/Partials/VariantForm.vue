<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

interface Product {
    id: number;
    name: string;
}

interface VariantData {
    id: number;
    name: string;
    sku: string;
    barcode: string | null;
    price: string;
    sale_price: string | null;
    cost_price: string | null;
    stock: number;
    reserved_stock: number;
    low_stock_threshold: number;
    attributes: Record<string, string>;
    is_default: boolean;
    is_active: boolean;
}

interface AttributeRow {
    id: number;
    name: string;
    value: string;
}

const props = defineProps<{
    product: Product;
    variant?: VariantData;
}>();

const isEditing = Boolean(props.variant);

let nextAttributeId = 1;

const attributeRows = ref<AttributeRow[]>(
    Object.entries(props.variant?.attributes ?? {}).map(
        ([name, value]) => ({
            id: nextAttributeId++,
            name,
            value,
        }),
    ),
);

const form = useForm({
    name: props.variant?.name ?? '',
    sku: props.variant?.sku ?? '',
    barcode: props.variant?.barcode ?? '',
    price: props.variant?.price ?? '',
    sale_price: props.variant?.sale_price ?? '',
    cost_price: props.variant?.cost_price ?? '',
    stock: props.variant?.stock ?? 0,
    low_stock_threshold:
        props.variant?.low_stock_threshold ?? 5,
    attributes: {} as Record<string, string>,
    is_default: props.variant?.is_default ?? false,
    is_active: props.variant?.is_active ?? true,
});

function addAttribute(): void {
    if (attributeRows.value.length >= 20) {
        return;
    }

    attributeRows.value.push({
        id: nextAttributeId++,
        name: '',
        value: '',
    });
}

function removeAttribute(id: number): void {
    attributeRows.value = attributeRows.value.filter(
        (attribute) => attribute.id !== id,
    );
}

function submit(): void {
    form.attributes = Object.fromEntries(
        attributeRows.value
            .map((attribute) => [
                attribute.name.trim(),
                attribute.value.trim(),
            ])
            .filter(([name, value]) => name !== '' && value !== ''),
    );

    if (isEditing && props.variant) {
        form.put(
            `/admin/products/${props.product.id}/variants/${props.variant.id}`,
            {
                preserveScroll: true,
            },
        );

        return;
    }

    form.post(
        `/admin/products/${props.product.id}/variants`,
        {
            preserveScroll: true,
        },
    );
}
</script>

<template>
    <form class="space-y-6" @submit.prevent="submit">
        <section
            class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm"
        >
            <div class="mb-6">
                <h2 class="text-lg font-bold text-slate-900">
                    Identificação
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Dados que diferenciam esta variante.
                </p>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <div>
                    <label
                        for="name"
                        class="mb-2 block text-sm font-semibold text-slate-700"
                    >
                        Nome da variante
                    </label>

                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        placeholder="Ex.: Preto / 16 GB"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
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
            </div>
        </section>

        <section
            class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm"
        >
            <div class="mb-6">
                <h2 class="text-lg font-bold text-slate-900">
                    Preços e estoque
                </h2>
            </div>

            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-5">
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
                        class="w-full rounded-lg border border-slate-300 px-4 py-3"
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
                        class="w-full rounded-lg border border-slate-300 px-4 py-3"
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
                        class="w-full rounded-lg border border-slate-300 px-4 py-3"
                    />
                </div>

                <div>
                    <label
                        for="stock"
                        class="mb-2 block text-sm font-semibold text-slate-700"
                    >
                        Estoque total
                    </label>

                    <input
                        id="stock"
                        v-model.number="form.stock"
                        type="number"
                        min="0"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3"
                    />

                    <p
                        v-if="variant?.reserved_stock"
                        class="mt-2 text-xs text-amber-700"
                    >
                        {{ variant.reserved_stock }} unidade(s) reservada(s).
                    </p>
                </div>

                <div>
                    <label
                        for="low_stock_threshold"
                        class="mb-2 block text-sm font-semibold text-slate-700"
                    >
                        Limite baixo
                    </label>

                    <input
                        id="low_stock_threshold"
                        v-model.number="form.low_stock_threshold"
                        type="number"
                        min="0"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3"
                    />
                </div>
            </div>
        </section>

        <section
            class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm"
        >
            <div
                class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <h2 class="text-lg font-bold text-slate-900">
                        Atributos
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Exemplo: Cor = Preto, Memória = 16 GB.
                    </p>
                </div>

                <button
                    type="button"
                    :disabled="attributeRows.length >= 20"
                    class="rounded-lg border border-blue-300 px-4 py-2 text-sm font-bold text-blue-700 hover:bg-blue-50 disabled:opacity-50"
                    @click="addAttribute"
                >
                    Adicionar atributo
                </button>
            </div>

            <div
                v-if="attributeRows.length > 0"
                class="space-y-3"
            >
                <div
                    v-for="attribute in attributeRows"
                    :key="attribute.id"
                    class="grid gap-3 sm:grid-cols-[1fr_1fr_auto]"
                >
                    <input
                        v-model="attribute.name"
                        type="text"
                        maxlength="255"
                        placeholder="Nome, ex.: Cor"
                        class="rounded-lg border border-slate-300 px-4 py-3"
                    />

                    <input
                        v-model="attribute.value"
                        type="text"
                        maxlength="255"
                        placeholder="Valor, ex.: Preto"
                        class="rounded-lg border border-slate-300 px-4 py-3"
                    />

                    <button
                        type="button"
                        class="rounded-lg border border-red-200 px-4 py-3 text-sm font-bold text-red-600 hover:bg-red-50"
                        @click="removeAttribute(attribute.id)"
                    >
                        Remover
                    </button>
                </div>
            </div>

            <p
                v-else
                class="rounded-lg bg-slate-50 px-5 py-8 text-center text-sm text-slate-500"
            >
                Nenhum atributo adicionado.
            </p>

            <p
                v-if="form.errors.attributes"
                class="mt-3 text-sm text-red-600"
            >
                {{ form.errors.attributes }}
            </p>
        </section>

        <section
            class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm"
        >
            <div class="space-y-4">
                <label class="flex cursor-pointer items-start gap-3">
                    <input
                        v-model="form.is_default"
                        type="checkbox"
                        class="mt-0.5 h-5 w-5 rounded border-slate-300 text-blue-600"
                    />

                    <span>
                        <span class="block text-sm font-semibold text-slate-700">
                            Variante padrão
                        </span>

                        <span class="block text-sm text-slate-500">
                            Será selecionada inicialmente na página do produto.
                        </span>
                    </span>
                </label>

                <p
                    v-if="form.errors.is_default"
                    class="text-sm text-red-600"
                >
                    {{ form.errors.is_default }}
                </p>

                <label class="flex cursor-pointer items-start gap-3">
                    <input
                        v-model="form.is_active"
                        type="checkbox"
                        class="mt-0.5 h-5 w-5 rounded border-slate-300 text-blue-600"
                    />

                    <span>
                        <span class="block text-sm font-semibold text-slate-700">
                            Variante ativa
                        </span>

                        <span class="block text-sm text-slate-500">
                            Variantes inativas não estarão disponíveis para compra.
                        </span>
                    </span>
                </label>
            </div>
        </section>

        <div class="flex justify-end gap-3">
            <Link
                :href="`/admin/products/${product.id}/variants`"
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
                          : 'Cadastrar variante'
                }}
            </button>
        </div>
    </form>
</template>
