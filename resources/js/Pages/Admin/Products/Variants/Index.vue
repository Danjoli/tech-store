<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

interface Product {
    id: number;
    name: string;
}

interface Variant {
    id: number;
    name: string;
    sku: string;
    barcode: string | null;
    price: string;
    sale_price: string | null;
    stock: number;
    reserved_stock: number;
    available_stock: number;
    low_stock_threshold: number;
    is_low_stock: boolean;
    attributes: Record<string, string>;
    is_default: boolean;
    is_active: boolean;
}

const props = defineProps<{
    product: Product;
    variants: Variant[];
}>();

function formatCurrency(value: string | null): string {
    if (value === null) {
        return '—';
    }

    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    }).format(Number(value));
}

function formatAttributes(
    attributes: Record<string, string>,
): string {
    const entries = Object.entries(attributes);

    if (entries.length === 0) {
        return 'Sem atributos';
    }

    return entries
        .map(([name, value]) => `${name}: ${value}`)
        .join(' · ');
}

function deleteVariant(variant: Variant): void {
    if (
        ! confirm(
            `Deseja realmente excluir a variante "${variant.name}"?`,
        )
    ) {
        return;
    }

    router.delete(
        `/admin/products/${props.product.id}/variants/${variant.id}`,
        {
            preserveScroll: true,
        },
    );
}
</script>

<template>
    <Head :title="`Variantes de ${product.name}`" />

    <AdminLayout>
        <div
            class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <div>
                <Link
                    :href="`/admin/products/${product.id}/edit`"
                    class="text-sm font-bold text-blue-600 hover:text-blue-800"
                >
                    ← Voltar para o produto
                </Link>

                <p class="mt-6 text-sm font-semibold text-blue-600">
                    Catálogo
                </p>

                <h1 class="mt-1 text-3xl font-bold text-slate-900">
                    Variantes
                </h1>

                <p class="mt-2 text-slate-600">
                    Gerencie preços e estoques de {{ product.name }}.
                </p>
            </div>

            <Link
                :href="`/admin/products/${product.id}/variants/create`"
                class="inline-flex justify-center rounded-lg bg-blue-600 px-5 py-3 text-sm font-bold text-white hover:bg-blue-700"
            >
                Nova variante
            </Link>
        </div>

        <section
            class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm"
        >
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th
                                class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500"
                            >
                                Variante
                            </th>

                            <th
                                class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500"
                            >
                                Preço
                            </th>

                            <th
                                class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500"
                            >
                                Estoque
                            </th>

                            <th
                                class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500"
                            >
                                Situação
                            </th>

                            <th
                                class="px-6 py-4 text-right text-xs font-bold uppercase text-slate-500"
                            >
                                Ações
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-200">
                        <tr
                            v-for="variant in variants"
                            :key="variant.id"
                            class="hover:bg-slate-50"
                        >
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <p class="font-semibold text-slate-900">
                                        {{ variant.name }}
                                    </p>

                                    <span
                                        v-if="variant.is_default"
                                        class="rounded bg-blue-100 px-2 py-0.5 text-xs font-bold text-blue-700"
                                    >
                                        Padrão
                                    </span>
                                </div>

                                <p class="mt-1 text-sm text-slate-500">
                                    SKU: {{ variant.sku }}
                                </p>

                                <p class="mt-1 text-xs text-slate-400">
                                    {{ formatAttributes(variant.attributes) }}
                                </p>
                            </td>

                            <td class="px-6 py-4">
                                <template v-if="variant.sale_price">
                                    <p class="font-bold text-emerald-700">
                                        {{
                                            formatCurrency(
                                                variant.sale_price,
                                            )
                                        }}
                                    </p>

                                    <p class="text-xs text-slate-400 line-through">
                                        {{ formatCurrency(variant.price) }}
                                    </p>
                                </template>

                                <p v-else class="font-bold text-slate-900">
                                    {{ formatCurrency(variant.price) }}
                                </p>
                            </td>

                            <td class="px-6 py-4">
                                <p
                                    class="font-bold"
                                    :class="
                                        variant.is_low_stock
                                            ? 'text-red-600'
                                            : 'text-slate-900'
                                    "
                                >
                                    {{ variant.available_stock }} disponível
                                </p>

                                <p class="mt-1 text-xs text-slate-500">
                                    {{ variant.stock }} total ·
                                    {{ variant.reserved_stock }} reservado
                                </p>
                            </td>

                            <td class="px-6 py-4">
                                <span
                                    class="rounded-full px-3 py-1 text-xs font-bold"
                                    :class="
                                        variant.is_active
                                            ? 'bg-emerald-100 text-emerald-700'
                                            : 'bg-red-100 text-red-700'
                                    "
                                >
                                    {{
                                        variant.is_active
                                            ? 'Ativa'
                                            : 'Inativa'
                                    }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-3">
                                    <Link
                                        :href="`/admin/products/${product.id}/variants/${variant.id}/edit`"
                                        class="text-sm font-bold text-blue-600 hover:text-blue-800"
                                    >
                                        Editar
                                    </Link>

                                    <button
                                        type="button"
                                        class="text-sm font-bold text-red-600 hover:text-red-800"
                                        @click="deleteVariant(variant)"
                                    >
                                        Excluir
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr v-if="variants.length === 0">
                            <td
                                colspan="5"
                                class="px-6 py-14 text-center text-sm text-slate-500"
                            >
                                Nenhuma variante cadastrada.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </AdminLayout>
</template>
