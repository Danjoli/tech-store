<script setup lang="ts">
import type { PageProps } from '@inertiajs/core';
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

interface AuthUser {
    id: number;
    name: string;
}

interface SharedPageProps extends PageProps {
    auth?: {
        user: AuthUser | null;
    };
    flash?: {
        success?: string;
        error?: string;
    };
    wishlistCount?: number;
    cartCount?: number;
    filters?: {
        search?: string;
    };
}

const page = usePage<SharedPageProps>();

const search = ref(page.props.filters?.search ?? '');

const user = computed(() => page.props.auth?.user ?? null);
const wishlistCount = computed(() => page.props.wishlistCount ?? 0);
const cartCount = computed(() => page.props.cartCount ?? 0);

const successMessage = computed(
    () => page.props.flash?.success ?? null,
);

const errorMessage = computed(
    () => page.props.flash?.error ?? null,
);

function submitSearch(): void {
    const value = search.value.trim();

    router.get(
        '/produtos',
        value ? { search: value } : {},
        {
            preserveState: true,
        },
    );
}
</script>

<template>
    <div class="min-h-screen bg-[#07090d] font-sans text-[#f7f9fc]">
        <div class="
                bg-gradient-to-r from-[#3b7fff] to-[#8b5cf6]
                px-5 py-[9px] text-center text-xs font-bold
                tracking-[0.02em] text-white
                max-[640px]:text-[10px]
            ">
            Frete grátis para todo o Brasil em compras acima de R$ 599
        </div>

        <header class="
                sticky top-0 z-40
                border-b border-white/[0.07]
                bg-[#07090d]/85 backdrop-blur-[18px]
            ">
            <div class="
                    mx-auto flex h-[72px]
                    w-[min(1180px,calc(100%_-_40px))]
                    items-center justify-between gap-[30px]

                    max-[640px]:h-auto
                    max-[640px]:w-[calc(100%_-_28px)]
                    max-[640px]:items-start
                    max-[640px]:gap-3
                    max-[640px]:py-[13px]
                ">
                <Link href="/" class="
                        relative inline-flex items-center
                        whitespace-nowrap text-xl font-black
                        tracking-[-0.05em] text-[#f7f9fc]
                    " aria-label="Tech Store — início">
                    <span class="text-[#58a7ff]">
                        TECH
                    </span>

                    STORE

                    <i class="
                            ml-[3px] mt-[9px]
                            h-[5px] w-[5px]
                            rounded-full bg-[#8d6cff]
                            shadow-[0_0_12px_#8d6cff]
                        " />
                </Link>

                <nav class="
                        flex gap-[30px] text-sm text-[#aeb6c4]
                        max-[900px]:hidden
                    " aria-label="Navegação principal">
                    <Link href="/produtos" class="
                            transition-colors duration-150
                            hover:text-white
                        ">
                        Produtos
                    </Link>

                    <Link href="/#categorias" class="
                            transition-colors duration-150
                            hover:text-white
                        ">
                        Categorias
                    </Link>

                    <Link href="/produtos?on_sale=1" class="
                            transition-colors duration-150
                            hover:text-white
                        ">
                        Ofertas
                    </Link>
                </nav>

                <div class="
                        flex items-center gap-2

                        max-[640px]:flex-1
                        max-[640px]:flex-wrap
                        max-[640px]:justify-end
                    ">
                    <form class="
                            flex h-10 w-[235px]
                            items-center gap-[9px]
                            rounded-[10px]
                            border border-[#222936]
                            bg-[#11151c] px-3
                            text-[#697487]

                            max-[900px]:w-[200px]

                            max-[640px]:order-2
                            max-[640px]:h-[38px]
                            max-[640px]:w-full
                        " role="search" @submit.prevent="submitSearch">
                        <svg class="h-[18px] w-[18px] shrink-0" viewBox="0 0 24 24" aria-hidden="true" fill="none"
                            stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="7" />

                            <path d="m20 20-4-4" />
                        </svg>

                        <label for="store-search" class="sr-only">
                            Buscar produtos
                        </label>

                        <input id="store-search" v-model="search" type="search" placeholder="Buscar tecnologia..."
                            class="
                                w-full border-0 bg-transparent
                                text-[13px] text-white outline-none
                                placeholder:text-[#687282]
                            " />
                    </form>

                    <Link :href="user ? '/perfil' : '/login'" class="
                            relative grid h-10 w-10
                            place-items-center rounded-[10px]
                            border border-[#222936]
                            bg-[#11151c] text-[#c7cfdb]
                            transition-colors duration-150

                            hover:border-[#4a80bd]
                            hover:text-white

                            max-[640px]:hidden
                        " :aria-label="user
                                ? `Conta de ${user.name}`
                                : 'Entrar na conta'
                            ">
                        <svg class="h-[22px] w-[22px]" viewBox="0 0 24 24" aria-hidden="true" fill="none"
                            stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="8" r="4" />

                            <path d="M4 21a8 8 0 0 1 16 0" />
                        </svg>
                    </Link>

                    <Link href="/favoritos" class="relative grid h-10 w-10 place-items-center rounded-[10px] border border-[#222936] bg-[#11151c] text-[#c7cfdb] transition-colors duration-150 hover:border-[#8b80fa] hover:text-white max-[640px]:hidden" :aria-label="`${wishlistCount} favoritos`">
                        <svg class="h-[22px] w-[22px]" viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8l1.1 1.1L12 21l7.7-7.5 1.1-1.1a5.5 5.5 0 0 0 0-7.8Z" />
                        </svg>
                        <b v-if="wishlistCount > 0" class="absolute -right-[5px] -top-[5px] grid h-[17px] min-w-[17px] place-items-center rounded-[9px] bg-[#8d6cff] px-1 text-[10px] text-white">{{ wishlistCount }}</b>
                    </Link>

                    <Link href="/carrinho" class="relative grid h-10 w-10 place-items-center rounded-[10px] border border-[#222936] bg-[#11151c] text-[#c7cfdb] transition-colors duration-150 hover:border-[#4a80bd] hover:text-white" :aria-label="`${cartCount} itens no carrinho`">
                        <svg class="h-[22px] w-[22px]" viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 3h2l2.4 11.2a2 2 0 0 0 2 1.6h7.9a2 2 0 0 0 2-1.6L21 7H6" />
                            <circle cx="10" cy="20" r="1" />
                            <circle cx="18" cy="20" r="1" />
                        </svg>
                        <b v-if="cartCount > 0" class="absolute -right-[5px] -top-[5px] grid h-[17px] min-w-[17px] place-items-center rounded-[9px] bg-[#5e9eff] px-1 text-[10px] text-[#07101b]">{{ cartCount }}</b>
                    </Link>

                </div>
            </div>
        </header>

        <div v-if="successMessage" class="
                fixed bottom-6 right-6 z-[100]
                max-w-[min(400px,calc(100%_-_32px))]
                rounded-[10px]
                border border-[#315b4a]
                bg-[#13261f] px-[18px] py-[13px]
                text-xs font-bold text-[#8ee7bd]
                shadow-[0_16px_50px_rgb(0_0_0/40%)]

                max-[640px]:bottom-4
                max-[640px]:right-4
            ">
            {{ successMessage }}
        </div>

        <div v-if="errorMessage" class="
                fixed bottom-6 right-6 z-[100]
                max-w-[min(400px,calc(100%_-_32px))]
                rounded-[10px]
                border border-[#65363d]
                bg-[#2b171b] px-[18px] py-[13px]
                text-xs font-bold text-[#ff9da8]
                shadow-[0_16px_50px_rgb(0_0_0/40%)]

                max-[640px]:bottom-4
                max-[640px]:right-4
            ">
            {{ errorMessage }}
        </div>

        <main>
            <slot />
        </main>

        <footer class="
                mx-auto grid
                w-[min(1180px,calc(100%_-_40px))]
                grid-cols-3 items-center
                border-t border-[#222936]
                py-[45px] text-[#778190]

                max-[640px]:w-[calc(100%_-_32px)]
                max-[640px]:grid-cols-1
                max-[640px]:gap-3
                max-[640px]:text-center
            ">
            <Link href="/" class="
                    relative inline-flex items-center
                    whitespace-nowrap text-xl font-black
                    tracking-[-0.05em] text-[#f7f9fc]

                    max-[640px]:mx-auto
                ">
                <span class="text-[#58a7ff]">
                    TECH
                </span>

                STORE

                <i class="
                        ml-[3px] mt-[9px]
                        h-[5px] w-[5px]
                        rounded-full bg-[#8d6cff]
                        shadow-[0_0_12px_#8d6cff]
                    " />
            </Link>

            <p class="text-center text-xs">
                Tecnologia certa para cada conquista.
            </p>

            <small class="
                    text-right text-[10px]
                    max-[640px]:text-center
                ">
                © {{ new Date().getFullYear() }} Tech Store.
                Todos os direitos reservados.
            </small>
        </footer>
    </div>
</template>
