<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const page = usePage();
const menuOpen = ref(false);
const currentUrl = computed(() => page.url);

type FlashMessages = { success?: string | null; error?: string | null };
const flash = computed(() => page.props.flash as FlashMessages | undefined);

function isActive(url: string): boolean {
    return currentUrl.value === url || currentUrl.value.startsWith(`${url}/`);
}
</script>

<template>
    <div class="min-h-screen bg-[#f5f7fb] text-slate-800">
        <aside
            class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col border-r border-white/[0.06] bg-[#090d15] text-white transition-transform duration-200 lg:translate-x-0"
            :class="menuOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <div class="border-b border-white/[0.08] px-6 py-6">
                <Link href="/admin" class="inline-flex items-baseline text-xl font-black tracking-[-0.06em]">
                    <span class="text-sky-400">TECH</span>STORE<span class="ml-1 text-sm text-violet-400">.</span>
                </Link>
                <p class="mt-1 text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Administração</p>
            </div>

            <nav class="flex-1 space-y-1 overflow-y-auto p-4" aria-label="Navegação administrativa">
                <p class="px-3 pb-2 pt-2 text-[10px] font-bold uppercase tracking-[0.16em] text-slate-600">Visão geral</p>
                <Link href="/admin" class="block rounded-xl px-3 py-2.5 text-sm font-semibold transition" :class="isActive('/admin') && currentUrl === '/admin' ? 'bg-sky-500 text-white shadow-sm' : 'text-slate-300 hover:bg-white/[0.07] hover:text-white'" @click="menuOpen = false">Dashboard</Link>

                <p class="px-3 pb-2 pt-6 text-[10px] font-bold uppercase tracking-[0.16em] text-slate-600">Catálogo</p>
                <Link href="/admin/products" class="block rounded-xl px-3 py-2.5 text-sm font-semibold transition" :class="isActive('/admin/products') ? 'bg-sky-500 text-white shadow-sm' : 'text-slate-300 hover:bg-white/[0.07] hover:text-white'" @click="menuOpen = false">Produtos</Link>
                <Link href="/admin/categories" class="block rounded-xl px-3 py-2.5 text-sm font-semibold transition" :class="isActive('/admin/categories') ? 'bg-sky-500 text-white shadow-sm' : 'text-slate-300 hover:bg-white/[0.07] hover:text-white'" @click="menuOpen = false">Categorias</Link>
                <Link href="/admin/brands" class="block rounded-xl px-3 py-2.5 text-sm font-semibold transition" :class="isActive('/admin/brands') ? 'bg-sky-500 text-white shadow-sm' : 'text-slate-300 hover:bg-white/[0.07] hover:text-white'" @click="menuOpen = false">Marcas</Link>
            </nav>

            <div class="border-t border-white/[0.08] p-4">
                <Link href="/" class="block rounded-xl px-3 py-2.5 text-sm font-semibold text-slate-400 transition hover:bg-white/[0.07] hover:text-white">Ver loja</Link>
            </div>
        </aside>

        <div v-if="menuOpen" class="fixed inset-0 z-30 bg-slate-950/50 lg:hidden" @click="menuOpen = false" />

        <div class="min-h-screen lg:pl-64">
            <header class="sticky top-0 z-20 flex h-[72px] items-center justify-between border-b border-slate-200 bg-white/90 px-5 backdrop-blur lg:px-8">
                <button type="button" class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-bold text-slate-700 lg:hidden" @click="menuOpen = true">Menu</button>
                <p class="hidden text-sm font-medium text-slate-500 lg:block">Painel administrativo</p>
                <Link href="/logout" method="post" as="button" class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-bold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50">Sair</Link>
            </header>

            <main class="mx-auto max-w-[1440px] p-5 lg:p-8">
                <div v-if="flash?.success" class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">{{ flash.success }}</div>
                <div v-if="flash?.error" class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800">{{ flash.error }}</div>
                <slot />
            </main>
        </div>
    </div>
</template>
