<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import BrandForm from '@/Pages/Admin/Brands/Partials/BrandForm.vue';
import { Head, Link } from '@inertiajs/vue3';

interface Brand {
    id: number;
    name: string;
    description: string | null;
    website_url: string | null;
    logo_url: string | null;
    is_active: boolean;
}

defineProps<{
    brand: Brand;
}>();
</script>

<template>
    <Head :title="`Editar ${brand.name}`" />

    <AdminLayout>
        <div class="mb-8">
            <Link
                href="/admin/brands"
                class="text-sm font-bold text-blue-600 hover:text-blue-800"
            >
                ← Voltar para marcas
            </Link>

            <p class="mt-6 text-sm font-semibold text-blue-600">
                Catálogo
            </p>

            <h1 class="mt-1 text-3xl font-bold text-slate-900">
                Editar marca
            </h1>

            <p class="mt-2 text-slate-600">
                Atualize as informações de {{ brand.name }}.
            </p>
        </div>

        <BrandForm :brand="brand" />
    </AdminLayout>
</template>
