<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

interface LowStockVariant {
    id: number;
    name: string;
    sku: string;
    available_stock: number;
}

interface Metrics {
    products: number;
    active_products: number;
    draft_products: number;
    categories: number;
    brands: number;
    low_stock_variants: LowStockVariant[];
}

defineProps<{ metrics: Metrics }>();
</script>

<template>
    <Head title="Painel administrativo" />

    <AdminLayout>
        <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.16em] text-sky-500">Administração</p>
                <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-950">Visão geral</h1>
                <p class="mt-2 max-w-xl text-sm leading-6 text-slate-500">
                    Acompanhe o catálogo e encontre rapidamente o que precisa de atenção.
                </p>
            </div>

            <Link href="/admin/products/create" class="rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-sky-500">
                Novo produto
            </Link>
        </div>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-[0.12em] text-slate-500">Produtos</p>
                <p class="mt-3 text-3xl font-black tracking-tight text-slate-950">{{ metrics.products }}</p>
                <p class="mt-2 text-sm text-slate-500">{{ metrics.active_products }} ativos no catálogo</p>
            </article>
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-[0.12em] text-slate-500">Rascunhos</p>
                <p class="mt-3 text-3xl font-black tracking-tight text-slate-950">{{ metrics.draft_products }}</p>
                <p class="mt-2 text-sm text-slate-500">Produtos ainda não publicados</p>
            </article>
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-[0.12em] text-slate-500">Categorias</p>
                <p class="mt-3 text-3xl font-black tracking-tight text-slate-950">{{ metrics.categories }}</p>
                <p class="mt-2 text-sm text-slate-500">Estrutura de navegação da loja</p>
            </article>
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-[0.12em] text-slate-500">Marcas</p>
                <p class="mt-3 text-3xl font-black tracking-tight text-slate-950">{{ metrics.brands }}</p>
                <p class="mt-2 text-sm text-slate-500">Parceiros cadastrados</p>
            </article>
        </section>

        <section class="mt-6 rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
                <div>
                    <h2 class="font-bold text-slate-950">Estoque que merece atenção</h2>
                    <p class="mt-1 text-sm text-slate-500">Variações com saldo igual ou abaixo do limite definido.</p>
                </div>
                <Link href="/admin/products" class="text-sm font-bold text-sky-700 hover:text-sky-600">Ver produtos</Link>
            </div>

            <div v-if="metrics.low_stock_variants.length" class="divide-y divide-slate-100">
                <div v-for="variant in metrics.low_stock_variants" :key="variant.id" class="flex items-center justify-between gap-4 px-5 py-4">
                    <div class="min-w-0">
                        <p class="truncate text-sm font-bold text-slate-800">{{ variant.name }}</p>
                        <p class="mt-1 text-xs text-slate-500">SKU: {{ variant.sku }}</p>
                    </div>
                    <span class="shrink-0 rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700">{{ variant.available_stock }} em estoque</span>
                </div>
            </div>
            <div v-else class="px-5 py-12 text-center">
                <p class="font-bold text-slate-700">Estoque saudável</p>
                <p class="mt-1 text-sm text-slate-500">Nenhuma variação está abaixo do limite configurado.</p>
            </div>
        </section>
    </AdminLayout>
</template>
