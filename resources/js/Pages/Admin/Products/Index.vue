<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { reactive } from 'vue';

interface Option {
    id: number;
    name: string;
}

interface StatusOption {
    value: string;
    label: string;
}

interface Product {
    id: number;
    name: string;
    slug: string;
    brand: string | null;
    category: string;
    status: string;
    is_featured: boolean;
    price: string | null;
    sale_price: string | null;
    sku: string | null;
    stock: number;
    variants_count: number;
    image_url: string | null;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedProducts {
    data: Product[];
    links: PaginationLink[];
    from: number | null;
    to: number | null;
    total: number;
}

const props = defineProps<{
    products: PaginatedProducts;
    brands: Option[];
    categories: Option[];
    statuses: StatusOption[];
    filters: {
        search: string;
        status: string;
        brand_id: number | null;
        category_id: number | null;
    };
}>();

const form = reactive({
    search: props.filters.search ?? '',
    status: props.filters.status ?? '',
    brand_id: props.filters.brand_id ?? null,
    category_id: props.filters.category_id ?? null,
});

function filterProducts(): void {
    router.get(
        '/admin/products',
        {
            search: form.search || undefined,
            status: form.status || undefined,
            brand_id: form.brand_id || undefined,
            category_id: form.category_id || undefined,
        },
        {
            preserveState: true,
            replace: true,
        },
    );
}

function clearFilters(): void {
    form.search = '';
    form.status = '';
    form.brand_id = null;
    form.category_id = null;

    filterProducts();
}

function deleteProduct(product: Product): void {
    if (
        ! confirm(
            `Deseja realmente excluir o produto "${product.name}"?`,
        )
    ) {
        return;
    }

    router.delete(`/admin/products/${product.id}`, {
        preserveScroll: true,
    });
}

function formatCurrency(value: string | null): string {
    if (value === null) {
        return 'Não informado';
    }

    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    }).format(Number(value));
}

function statusLabel(status: string): string {
    return (
        props.statuses.find((item) => item.value === status)?.label
        ?? status
    );
}

function statusClass(status: string): string {
    if (status === 'active') {
        return 'bg-emerald-100 text-emerald-700';
    }

    if (status === 'inactive') {
        return 'bg-red-100 text-red-700';
    }

    return 'bg-amber-100 text-amber-700';
}
</script>

<template>
    <Head title="Produtos" />

    <AdminLayout>
        <div
            class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <div>
                <p class="text-sm font-semibold text-blue-600">
                    Catálogo
                </p>

                <h1 class="mt-1 text-3xl font-bold text-slate-900">
                    Produtos
                </h1>

                <p class="mt-2 text-slate-600">
                    Gerencie o catálogo, preços e estoque da loja.
                </p>
            </div>

            <Link
                href="/admin/products/create"
                class="inline-flex justify-center rounded-lg bg-blue-600 px-5 py-3 text-sm font-bold text-white hover:bg-blue-700"
            >
                Novo produto
            </Link>
        </div>

        <form
            class="mb-6 rounded-xl border border-slate-200 bg-white p-5 shadow-sm"
            @submit.prevent="filterProducts"
        >
            <div class="grid gap-4 xl:grid-cols-5">
                <div>
                    <label
                        for="search"
                        class="mb-2 block text-sm font-semibold text-slate-700"
                    >
                        Pesquisar
                    </label>

                    <input
                        id="search"
                        v-model="form.search"
                        type="search"
                        placeholder="Nome ou SKU..."
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    />
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
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    >
                        <option :value="null">Todas</option>

                        <option
                            v-for="category in categories"
                            :key="category.id"
                            :value="category.id"
                        >
                            {{ category.name }}
                        </option>
                    </select>
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
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    >
                        <option :value="null">Todas</option>

                        <option
                            v-for="brand in brands"
                            :key="brand.id"
                            :value="brand.id"
                        >
                            {{ brand.name }}
                        </option>
                    </select>
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
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    >
                        <option value="">Todas</option>

                        <option
                            v-for="status in statuses"
                            :key="status.value"
                            :value="status.value"
                        >
                            {{ status.label }}
                        </option>
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button
                        type="submit"
                        class="rounded-lg bg-slate-900 px-5 py-3 text-sm font-bold text-white hover:bg-slate-700"
                    >
                        Filtrar
                    </button>

                    <button
                        type="button"
                        class="rounded-lg border border-slate-300 px-5 py-3 text-sm font-bold text-slate-700 hover:bg-slate-100"
                        @click="clearFilters"
                    >
                        Limpar
                    </button>
                </div>
            </div>
        </form>

        <section
            class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm"
        >
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500">
                                Produto
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500">
                                Categoria
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500">
                                Preço
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500">
                                Estoque
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500">
                                Situação
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-bold uppercase text-slate-500">
                                Ações
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-200">
                        <tr
                            v-for="product in products.data"
                            :key="product.id"
                            class="hover:bg-slate-50"
                        >
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <img
                                        v-if="product.image_url"
                                        :src="product.image_url"
                                        :alt="product.name"
                                        class="h-14 w-14 rounded-lg border border-slate-200 object-cover"
                                    />

                                    <div
                                        v-else
                                        class="flex h-14 w-14 items-center justify-center rounded-lg bg-slate-100 font-bold text-slate-500"
                                    >
                                        {{ product.name.charAt(0).toUpperCase() }}
                                    </div>

                                    <div>
                                        <div class="flex items-center gap-2">
                                            <p class="font-semibold text-slate-900">
                                                {{ product.name }}
                                            </p>

                                            <span
                                                v-if="product.is_featured"
                                                class="rounded bg-violet-100 px-2 py-0.5 text-xs font-bold text-violet-700"
                                            >
                                                Destaque
                                            </span>
                                        </div>

                                        <p class="text-sm text-slate-500">
                                            {{ product.brand ?? 'Sem marca' }}
                                            · {{ product.sku ?? 'Sem SKU' }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 text-sm text-slate-600">
                                {{ product.category }}
                            </td>

                            <td class="px-6 py-4">
                                <template v-if="product.sale_price">
                                    <p class="font-bold text-emerald-700">
                                        {{ formatCurrency(product.sale_price) }}
                                    </p>

                                    <p class="text-xs text-slate-400 line-through">
                                        {{ formatCurrency(product.price) }}
                                    </p>
                                </template>

                                <p
                                    v-else
                                    class="font-bold text-slate-900"
                                >
                                    {{ formatCurrency(product.price) }}
                                </p>
                            </td>

                            <td class="px-6 py-4">
                                <p
                                    class="font-bold"
                                    :class="
                                        product.stock > 5
                                            ? 'text-slate-900'
                                            : 'text-red-600'
                                    "
                                >
                                    {{ product.stock }}
                                </p>

                                <p class="text-xs text-slate-500">
                                    {{ product.variants_count }} variante(s)
                                </p>
                            </td>

                            <td class="px-6 py-4">
                                <span
                                    class="rounded-full px-3 py-1 text-xs font-bold"
                                    :class="statusClass(product.status)"
                                >
                                    {{ statusLabel(product.status) }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-3">
                                    <Link
                                        :href="`/admin/products/${product.id}/edit`"
                                        class="text-sm font-bold text-blue-600 hover:text-blue-800"
                                    >
                                        Editar
                                    </Link>

                                    <button
                                        type="button"
                                        class="text-sm font-bold text-red-600 hover:text-red-800"
                                        @click="deleteProduct(product)"
                                    >
                                        Excluir
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr v-if="products.data.length === 0">
                            <td
                                colspan="6"
                                class="px-6 py-14 text-center text-sm text-slate-500"
                            >
                                Nenhum produto encontrado.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <footer
                v-if="products.total > 0"
                class="flex flex-col gap-4 border-t border-slate-200 px-6 py-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <p class="text-sm text-slate-500">
                    Exibindo {{ products.from }}–{{ products.to }} de
                    {{ products.total }}
                </p>

                <nav class="flex flex-wrap gap-1">
                    <Link
                        v-for="link in products.links"
                        :key="link.label"
                        :href="link.url ?? '#'"
                        preserve-scroll
                        preserve-state
                        class="rounded-md border px-3 py-2 text-sm"
                        :class="[
                            link.active
                                ? 'border-blue-600 bg-blue-600 text-white'
                                : 'border-slate-300 bg-white text-slate-600 hover:bg-slate-50',
                            !link.url
                                ? 'pointer-events-none opacity-40'
                                : '',
                        ]"
                        v-html="link.label"
                    />
                </nav>
            </footer>
        </section>
    </AdminLayout>
</template>
