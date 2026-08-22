<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { reactive } from 'vue';

interface Brand {
    id: number;
    name: string;
    slug: string;
    logo_url: string | null;
    website_url: string | null;
    is_active: boolean;
    products_count: number;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedBrands {
    data: Brand[];
    links: PaginationLink[];
    from: number | null;
    to: number | null;
    total: number;
}

const props = defineProps<{
    brands: PaginatedBrands;
    filters: {
        search: string;
        status: string;
    };
}>();

const form = reactive({
    search: props.filters.search ?? '',
    status: props.filters.status ?? '',
});

function filterBrands(): void {
    router.get(
        '/admin/brands',
        {
            search: form.search || undefined,
            status: form.status || undefined,
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

    filterBrands();
}

function deleteBrand(brand: Brand): void {
    if (! confirm(`Deseja realmente excluir a marca "${brand.name}"?`)) {
        return;
    }

    router.delete(`/admin/brands/${brand.id}`, {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Marcas" />

    <AdminLayout>
        <div
            class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <div>
                <p class="text-sm font-semibold text-blue-600">
                    Catálogo
                </p>

                <h1 class="mt-1 text-3xl font-bold text-slate-900">
                    Marcas
                </h1>

                <p class="mt-2 text-slate-600">
                    Gerencie as marcas dos produtos da loja.
                </p>
            </div>

            <Link
                href="/admin/brands/create"
                class="inline-flex justify-center rounded-lg bg-blue-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-blue-700"
            >
                Nova marca
            </Link>
        </div>

        <form
            class="mb-6 rounded-xl border border-slate-200 bg-white p-5 shadow-sm"
            @submit.prevent="filterBrands"
        >
            <div class="grid gap-4 md:grid-cols-[1fr_220px_auto]">
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
                        placeholder="Nome da marca..."
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    />
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
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    >
                        <option value="">Todas</option>
                        <option value="active">Ativas</option>
                        <option value="inactive">Inativas</option>
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button
                        type="submit"
                        class="rounded-lg bg-slate-900 px-5 py-3 text-sm font-bold text-white transition hover:bg-slate-700"
                    >
                        Filtrar
                    </button>

                    <button
                        type="button"
                        class="rounded-lg border border-slate-300 px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-100"
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
                                Marca
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
                            v-for="brand in brands.data"
                            :key="brand.id"
                            class="hover:bg-slate-50"
                        >
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <img
                                        v-if="brand.logo_url"
                                        :src="brand.logo_url"
                                        :alt="brand.name"
                                        class="h-11 w-11 rounded-lg border border-slate-200 object-contain p-1"
                                    />

                                    <div
                                        v-else
                                        class="flex h-11 w-11 items-center justify-center rounded-lg bg-slate-100 text-sm font-bold text-slate-500"
                                    >
                                        {{ brand.name.charAt(0).toUpperCase() }}
                                    </div>

                                    <div>
                                        <p class="font-semibold text-slate-900">
                                            {{ brand.name }}
                                        </p>

                                        <p class="text-sm text-slate-500">
                                            {{ brand.slug }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 text-sm text-slate-600">
                                {{ brand.products_count }}
                            </td>

                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex rounded-full px-3 py-1 text-xs font-bold"
                                    :class="
                                        brand.is_active
                                            ? 'bg-emerald-100 text-emerald-700'
                                            : 'bg-red-100 text-red-700'
                                    "
                                >
                                    {{
                                        brand.is_active
                                            ? 'Ativa'
                                            : 'Inativa'
                                    }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-3">
                                    <Link
                                        :href="`/admin/brands/${brand.id}/edit`"
                                        class="text-sm font-bold text-blue-600 hover:text-blue-800"
                                    >
                                        Editar
                                    </Link>

                                    <button
                                        type="button"
                                        class="text-sm font-bold text-red-600 hover:text-red-800"
                                        @click="deleteBrand(brand)"
                                    >
                                        Excluir
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr v-if="brands.data.length === 0">
                            <td
                                colspan="4"
                                class="px-6 py-14 text-center text-sm text-slate-500"
                            >
                                Nenhuma marca encontrada.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <footer
                v-if="brands.total > 0"
                class="flex flex-col gap-4 border-t border-slate-200 px-6 py-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <p class="text-sm text-slate-500">
                    Exibindo {{ brands.from }}–{{ brands.to }} de
                    {{ brands.total }}
                </p>

                <nav class="flex flex-wrap gap-1">
                    <Link
                        v-for="link in brands.links"
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
