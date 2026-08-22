<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import VariantForm from '@/Pages/Admin/Products/Variants/Partials/VariantForm.vue';
import { Head, Link } from '@inertiajs/vue3';

interface Product {
    id: number;
    name: string;
}

defineProps<{
    product: Product;
}>();
</script>

<template>
    <Head :title="`Nova variante de ${product.name}`" />

    <AdminLayout>
        <div class="mb-8">
            <Link
                :href="`/admin/products/${product.id}/variants`"
                class="text-sm font-bold text-blue-600 hover:text-blue-800"
            >
                ← Voltar para variantes
            </Link>

            <p class="mt-6 text-sm font-semibold text-blue-600">
                Catálogo
            </p>

            <h1 class="mt-1 text-3xl font-bold text-slate-900">
                Nova variante
            </h1>

            <p class="mt-2 text-slate-600">
                Adicione uma nova opção para {{ product.name }}.
            </p>
        </div>

        <VariantForm :product="product" />
    </AdminLayout>
</template>
