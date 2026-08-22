<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';

const cards = [
    { title: 'Produtos', value: '0' },
    { title: 'Pedidos', value: '0' },
    { title: 'Clientes', value: '0' },
    { title: 'Faturamento', value: 'R$ 0,00' },
];
</script>

<template>
    <Head title="Painel administrativo" />

    <div class="min-h-screen bg-slate-100">
        <header class="border-b border-slate-200 bg-white">
            <div
                class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4"
            >
                <div>
                    <p class="text-sm font-semibold text-blue-600">
                        Tech Store
                    </p>

                    <h1 class="text-xl font-bold text-slate-900">
                        Painel administrativo
                    </h1>
                </div>

                <Link
                    href="/logout"
                    method="post"
                    as="button"
                    class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100"
                >
                    Sair
                </Link>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-6 py-8">
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-slate-900">
                    Visão geral
                </h2>

                <p class="mt-1 text-slate-600">
                    Acompanhe as principais informações da loja.
                </p>
            </div>

            <section class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
                <article
                    v-for="card in cards"
                    :key="card.title"
                    class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm"
                >
                    <p class="text-sm font-medium text-slate-500">
                        {{ card.title }}
                    </p>

                    <p class="mt-3 text-3xl font-bold text-slate-900">
                        {{ card.value }}
                    </p>
                </article>
            </section>
        </main>
    </div>
</template>
