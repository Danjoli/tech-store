<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ProductForm from '@/Pages/Admin/Products/Partials/ProductForm.vue';
import { Head, Link } from '@inertiajs/vue3';

interface Option {
    id: number;
    name: string;
}

interface StatusOption {
    value: string;
    label: string;
}

defineProps<{
    brands: Option[];
    categories: Option[];
    statuses: StatusOption[];
}>();
</script>

<template>
    <Head title="Novo produto" />

    <AdminLayout>
        <div class="mb-8">
            <Link
                href="/admin/products"
                class="text-sm font-bold text-blue-600 hover:text-blue-800"
            >
                ← Voltar para produtos
            </Link>

            <p class="mt-6 text-sm font-semibold text-blue-600">
                Catálogo
            </p>

            <h1 class="mt-1 text-3xl font-bold text-slate-900">
                Novo produto
            </h1>

            <p class="mt-2 text-slate-600">
                Cadastre o produto e sua variante padrão.
            </p>
        </div>

        <ProductForm
            :brands="brands"
            :categories="categories"
            :statuses="statuses"
        />
    </AdminLayout>
</template>
