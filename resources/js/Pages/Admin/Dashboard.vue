<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const cards = [
    {
        title: 'Produtos',
        value: '0',
        description: 'Produtos cadastrados',
    },
    {
        title: 'Pedidos',
        value: '0',
        description: 'Pedidos realizados',
    },
    {
        title: 'Clientes',
        value: '0',
        description: 'Clientes cadastrados',
    },
    {
        title: 'Faturamento',
        value: 'R$ 0,00',
        description: 'Receita total',
    },
];
</script>

<template>
    <Head title="Painel administrativo" />

    <AdminLayout>
        <div class="mb-8">
            <p class="text-sm font-semibold text-blue-600">
                Visão geral
            </p>

            <h1 class="mt-1 text-3xl font-bold text-slate-900">
                Dashboard
            </h1>

            <p class="mt-2 text-slate-600">
                Acompanhe as principais informações da Tech Store.
            </p>
        </div>

        <section class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
            <article
                v-for="card in cards"
                :key="card.title"
                class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm"
            >
                <p class="text-sm font-semibold text-slate-500">
                    {{ card.title }}
                </p>

                <p class="mt-3 text-3xl font-bold text-slate-900">
                    {{ card.value }}
                </p>

                <p class="mt-2 text-sm text-slate-500">
                    {{ card.description }}
                </p>
            </article>
        </section>

        <section
            class="mt-8 rounded-xl border border-slate-200 bg-white p-6 shadow-sm"
        >
            <h2 class="text-lg font-bold text-slate-900">
                Pedidos recentes
            </h2>

            <div class="mt-6 rounded-lg bg-slate-50 px-6 py-12 text-center">
                <p class="font-semibold text-slate-700">
                    Nenhum pedido encontrado
                </p>

                <p class="mt-1 text-sm text-slate-500">
                    Os pedidos recentes aparecerão aqui.
                </p>
            </div>
        </section>
    </AdminLayout>
</template>
