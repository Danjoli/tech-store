<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { reactive } from 'vue';

interface Category {
    id: number;
    parent: {
        id: number;
        name: string;
    } | null;
    name: string;
    slug: string;
    image_url: string | null;
    sort_order: number;
    is_active: boolean;
    products_count: number;
    children_count: number;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedCategories {
    data: Category[];
    links: PaginationLink[];
    from: number | null;
    to: number | null;
    total: number;
}

const props = defineProps<{
    categories: PaginatedCategories;
    filters: {
        search: string;
        status: string;
        type: string;
    };
}>();

const form = reactive({
    search: props.filters.search ?? '',
    status: props.filters.status ?? '',
    type: props.filters.type ?? '',
});

function filterCategories(): void {
    router.get(
        '/admin/categories',
        {
            search: form.search || undefined,
            status: form.status || undefined,
            type: form.type || undefined,
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
    form.type = '';

    filterCategories();
}

function deleteCategory(category: Category): void {
    if (
        ! confirm(
            `Deseja realmente excluir a categoria "${category.name}"?`,
        )
    ) {
        return;
    }

    router.delete(`/admin/categories/${category.id}`, {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Categorias" />

    <AdminLayout>
        <div
            class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <div>
                <p class="text-sm font-semibold text-blue-600">
                    Catálogo
                </p>

                <h1 class="mt-1 text-3xl font-bold text-slate-900">
                    Categorias
                </h1>

                <p class="mt-2 text-slate-600">
                    Organize os produtos em categorias e subcategorias.
                </p>
            </div>

            <Link
                href="/admin/categories/create"
                class="inline-flex justify-center rounded-lg bg-blue-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-blue-700"
            >
                Nova categoria
            </Link>
        </div>

        <form
            class="mb-6 rounded-xl border border-slate-200 bg-white p-5 shadow-sm"
            @submit.prevent="filterCategories"
        >
            <div class="grid gap-4 lg:grid-cols-[1fr_180px_190px_auto]">
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
                        placeholder="Nome da categoria..."
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    />
                </div>

                <div>
                    <label
                        for="type"
                        class="mb-2 block text-sm font-semibold text-slate-700"
                    >
                        Tipo
                    </label>

                    <select
                        id="type"
                        v-model="form.type"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    >
                        <option value="">Todos</option>
                        <option value="main">Principais</option>
                        <option value="subcategory">Subcategorias</option>
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
                        <option value="active">Ativas</option>
                        <option value="inactive">Inativas</option>
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
                            <th
                                class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500"
                            >
                                Categoria
                            </th>

                            <th
                                class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500"
                            >
                                Tipo
                            </th>

                            <th
                                class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500"
                            >
                                Ordem
                            </th>

                            <th
                                class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500"
                            >
                                Produtos
                            </th>

                            <th
                                class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500"
                            >
                                Situação
                            </th>

                            <th
                                class="px-6 py-4 text-right text-xs font-bold uppercase tracking-wider text-slate-500"
                            >
                                Ações
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-200">
                        <tr
                            v-for="category in categories.data"
                            :key="category.id"
                            class="hover:bg-slate-50"
                        >
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <img
                                        v-if="category.image_url"
                                        :src="category.image_url"
                                        :alt="category.name"
                                        class="h-11 w-11 rounded-lg border border-slate-200 object-cover"
                                    />

                                    <div
                                        v-else
                                        class="flex h-11 w-11 items-center justify-center rounded-lg bg-slate-100 font-bold text-slate-500"
                                    >
                                        {{
                                            category.name
                                                .charAt(0)
                                                .toUpperCase()
                                        }}
                                    </div>

                                    <div>
                                        <p class="font-semibold text-slate-900">
                                            {{ category.name }}
                                        </p>

                                        <p class="text-sm text-slate-500">
                                            <template v-if="category.parent">
                                                Em {{ category.parent.name }}
                                            </template>

                                            <template v-else>
                                                {{ category.children_count }}
                                                subcategoria(s)
                                            </template>
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <span
                                    class="rounded-full px-3 py-1 text-xs font-bold"
                                    :class="
                                        category.parent
                                            ? 'bg-violet-100 text-violet-700'
                                            : 'bg-blue-100 text-blue-700'
                                    "
                                >
                                    {{
                                        category.parent
                                            ? 'Subcategoria'
                                            : 'Principal'
                                    }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-sm text-slate-600">
                                {{ category.sort_order }}
                            </td>

                            <td class="px-6 py-4 text-sm text-slate-600">
                                {{ category.products_count }}
                            </td>

                            <td class="px-6 py-4">
                                <span
                                    class="rounded-full px-3 py-1 text-xs font-bold"
                                    :class="
                                        category.is_active
                                            ? 'bg-emerald-100 text-emerald-700'
                                            : 'bg-red-100 text-red-700'
                                    "
                                >
                                    {{
                                        category.is_active
                                            ? 'Ativa'
                                            : 'Inativa'
                                    }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-3">
                                    <Link
                                        :href="`/admin/categories/${category.id}/edit`"
                                        class="text-sm font-bold text-blue-600 hover:text-blue-800"
                                    >
                                        Editar
                                    </Link>

                                    <button
                                        type="button"
                                        class="text-sm font-bold text-red-600 hover:text-red-800"
                                        @click="deleteCategory(category)"
                                    >
                                        Excluir
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr v-if="categories.data.length === 0">
                            <td
                                colspan="6"
                                class="px-6 py-14 text-center text-sm text-slate-500"
                            >
                                Nenhuma categoria encontrada.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <footer
                v-if="categories.total > 0"
                class="flex flex-col gap-4 border-t border-slate-200 px-6 py-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <p class="text-sm text-slate-500">
                    Exibindo {{ categories.from }}–{{ categories.to }} de
                    {{ categories.total }}
                </p>

                <nav class="flex flex-wrap gap-1">
                    <Link
                        v-for="link in categories.links"
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
