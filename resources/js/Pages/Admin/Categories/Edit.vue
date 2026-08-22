<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import CategoryForm from '@/Pages/Admin/Categories/Partials/CategoryForm.vue';
import { Head, Link } from '@inertiajs/vue3';

interface ParentCategory {
    id: number;
    name: string;
}

interface Category {
    id: number;
    parent_id: number | null;
    name: string;
    description: string | null;
    image_url: string | null;
    sort_order: number;
    is_active: boolean;
    has_children: boolean;
}

defineProps<{
    category: Category;
    parentCategories: ParentCategory[];
}>();
</script>

<template>
    <Head :title="`Editar ${category.name}`" />

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
                Editar categoria
            </h1>

            <p class="mt-2 text-slate-600">
                Atualize as informações de {{ category.name }}.
            </p>
        </div>

        <CategoryForm
            :category="category"
            :parent-categories="parentCategories"
        />
    </AdminLayout>
</template>
