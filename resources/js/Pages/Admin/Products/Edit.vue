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

interface Product {
    id: number;
    brand_id: number | null;
    category_id: number;
    name: string;
    short_description: string | null;
    description: string | null;
    status: string;
    is_featured: boolean;
    warranty_months: number | null;
    weight: string | null;
    height: string | null;
    width: string | null;
    length: string | null;
    seo_title: string | null;
    seo_description: string | null;
    variant_name: string;
    sku: string;
    barcode: string | null;
    price: string;
    sale_price: string | null;
    cost_price: string | null;
    stock: number;
    low_stock_threshold: number;
    variant_is_active: boolean;
}

defineProps<{
    product: Product;
    brands: Option[];
    categories: Option[];
    statuses: StatusOption[];
}>();
</script>

<template>
    <Head :title="`Editar ${product.name}`" />

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
                Editar produto
            </h1>

            <p class="mt-2 text-slate-600">
                Atualize {{ product.name }} e sua variante padrão.
            </p>
        </div>

        <ProductForm
            :product="product"
            :brands="brands"
            :categories="categories"
            :statuses="statuses"
        />
    </AdminLayout>
</template>
