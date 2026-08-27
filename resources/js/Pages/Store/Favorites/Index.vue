<script setup lang="ts">
import ProductCard from '@/Components/Store/ProductCard.vue';
import StoreLayout from '@/Layouts/StoreLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

interface Product {
    id: number;
    name: string;
    slug: string;
    brand: string | null;
    category: string | null;
    image_url: string | null;
    price: string | number | null;
    sale_price: string | number | null;
    available_stock: number;
    is_favorited: boolean;
}

defineProps<{ products: Product[] }>();
</script>

<template>
    <Head title="Favoritos" />

    <StoreLayout>
        <section class="mx-auto w-[min(1180px,calc(100%_-_40px))] py-12 max-[640px]:w-[calc(100%_-_32px)] max-[640px]:py-8">
            <p class="text-[10px] font-extrabold uppercase tracking-[0.16em] text-[#6eaef6]">Sua seleção</p>
            <div class="mt-3 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h1 class="text-[clamp(32px,4vw,48px)] font-normal tracking-[-0.05em] text-[#f7f9fc]">Favoritos</h1>
                    <p class="mt-2 text-sm text-[#8b95a5]">Produtos salvos para você consultar depois.</p>
                </div>
                <Link href="/produtos" class="text-sm font-bold text-[#6eaef6] hover:text-white">Continuar explorando</Link>
            </div>

            <div v-if="products.length" class="mt-9 grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                <ProductCard v-for="product in products" :key="product.id" :product="product" />
            </div>

            <div v-else class="mt-9 rounded-2xl border border-dashed border-[#293344] bg-[#0d1117] px-6 py-16 text-center">
                <h2 class="text-xl font-bold text-[#f7f9fc]">Nenhum favorito ainda</h2>
                <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-[#8b95a5]">Use o coração nos produtos que chamarem sua atenção. Eles ficarão salvos nesta página.</p>
                <Link href="/produtos" class="mt-6 inline-flex rounded-xl bg-[#4a99ed] px-4 py-2.5 text-sm font-bold text-white transition hover:bg-[#5ca5f7]">Ver catálogo</Link>
            </div>
        </section>
    </StoreLayout>
</template>
