<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import BrandForm from '@/Pages/Admin/Brands/Partials/BrandForm.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Nova marca" />

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
                Nova marca
            </h1>

            <p class="mt-2 text-slate-600">
                Cadastre uma nova marca de produtos.
            </p>
        </div>

        <BrandForm />
    </AdminLayout>
</template>
