<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import CategoryForm from '@/Pages/Admin/Categories/Partials/CategoryForm.vue';
import { Head, Link } from '@inertiajs/vue3';

interface ParentCategory {
    id: number;
    name: string;
}

defineProps<{
    parentCategories: ParentCategory[];
}>();
</script>

<template>
    <Head title="Nova categoria" />

    <AdminLayout>
        <div class="mb-8">
            <Link
                href="/admin/categories"
                class="text-sm font-bold text-blue-600 hover:text-blue-800"
            >
                ← Voltar para categorias
            </Link>

            <p class="mt-6 text-sm font-semibold text-blue-600">
                Catálogo
            </p>

            <h1 class="mt-1 text-3xl font-bold text-slate-900">
                Nova categoria
            </h1>

            <p class="mt-2 text-slate-600">
                Cadastre uma categoria principal ou subcategoria.
            </p>
        </div>

        <CategoryForm :parent-categories="parentCategories" />
    </AdminLayout>
</template>
