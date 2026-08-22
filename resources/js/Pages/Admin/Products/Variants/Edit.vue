<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import VariantForm from '@/Pages/Admin/Products/Variants/Partials/VariantForm.vue';
import { Head, Link } from '@inertiajs/vue3';

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
    cost_price: string | null;
    stock: number;
    reserved_stock: number;
    low_stock_threshold: number;
    attributes: Record<string, string>;
    is_default: boolean;
    is_active: boolean;
}

defineProps<{
    product: Product;
    variant: Variant;
}>();
</script>

<template>
    <Head :title="`Editar ${variant.name}`" />

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
                Editar variante
            </h1>

            <p class="mt-2 text-slate-600">
                Atualize {{ variant.name }} de {{ product.name }}.
            </p>
        </div>

        <VariantForm
            :product="product"
            :variant="variant"
        />
    </AdminLayout>
</template>
