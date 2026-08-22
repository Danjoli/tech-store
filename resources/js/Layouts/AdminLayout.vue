<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const page = usePage();
const menuOpen = ref(false);

const currentUrl = computed(() => page.url);

function isActive(url: string): boolean {
    return currentUrl.value === url;
}
</script>

<template>
    <div class="min-h-screen bg-slate-100">
        <aside
            class="fixed inset-y-0 left-0 z-40 w-64 transform bg-slate-950 text-white transition-transform lg:translate-x-0"
            :class="menuOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <div class="flex h-20 items-center border-b border-slate-800 px-6">
                <div>
                    <p class="text-lg font-bold">Tech Store</p>
                    <p class="text-xs text-slate-400">Administração</p>
                </div>
            </div>

            <nav class="space-y-2 p-4">
                <Link
                    href="/admin"
                    class="block rounded-lg px-4 py-3 text-sm font-semibold transition"
                    :class="
                        isActive('/admin')
                            ? 'bg-blue-600 text-white'
                            : 'text-slate-300 hover:bg-slate-800 hover:text-white'
                    "
                    @click="menuOpen = false"
                >
                    Visão geral
                </Link>

                <div class="pt-5">
                    <p
                        class="mb-2 px-4 text-xs font-bold uppercase tracking-wider text-slate-500"
                    >
                        Catálogo
                    </p>

                    <span
                        class="block cursor-not-allowed rounded-lg px-4 py-3 text-sm text-slate-500"
                    >
                        Produtos
                    </span>

                    <span
                        class="block cursor-not-allowed rounded-lg px-4 py-3 text-sm text-slate-500"
                    >
                        Categorias
                    </span>

                    <span
                        class="block cursor-not-allowed rounded-lg px-4 py-3 text-sm text-slate-500"
                    >
                        Marcas
                    </span>
                </div>
            </nav>
        </aside>

        <div
            v-if="menuOpen"
            class="fixed inset-0 z-30 bg-slate-950/50 lg:hidden"
            @click="menuOpen = false"
        />

        <div class="lg:pl-64">
            <header
                class="sticky top-0 z-20 flex h-20 items-center justify-between border-b border-slate-200 bg-white px-5 lg:px-8"
            >
                <button
                    type="button"
                    class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 lg:hidden"
                    @click="menuOpen = true"
                >
                    Menu
                </button>

                <p class="hidden text-sm text-slate-500 lg:block">
                    Painel administrativo
                </p>

                <Link
                    href="/logout"
                    method="post"
                    as="button"
                    class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100"
                >
                    Sair
                </Link>
            </header>

            <main class="p-5 lg:p-8">
                <slot />
            </main>
        </div>
    </div>
</template>
