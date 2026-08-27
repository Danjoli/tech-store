<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

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
}

const props = defineProps<{
    product: Product;
}>();

const regularPrice = computed(() =>
    Number(props.product.price ?? 0),
);

const promotionalPrice = computed(() =>
    Number(props.product.sale_price ?? 0),
);

const hasPromotion = computed(
    () =>
        promotionalPrice.value > 0
        && promotionalPrice.value < regularPrice.value,
);

const currentPrice = computed(() =>
    hasPromotion.value
        ? promotionalPrice.value
        : regularPrice.value,
);

const discountPercentage = computed(() => {
    if (!hasPromotion.value || regularPrice.value <= 0) {
        return 0;
    }

    return Math.round(
        (
            (regularPrice.value - promotionalPrice.value)
            / regularPrice.value
        ) * 100,
    );
});

const installmentPrice = computed(
    () => currentPrice.value / 12,
);

function formatCurrency(value: number): string {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    }).format(value);
}
</script>

<template>
    <article class="
            group overflow-hidden rounded-2xl
            border border-[#222936] bg-[#0d1117]
            transition-all duration-300

            hover:-translate-y-[5px]
            hover:border-[#34445b]
            hover:shadow-[0_20px_50px_rgb(0_0_0/24%)]
        ">
        <div class="
                relative aspect-[1.3]
                overflow-hidden bg-[#161b24]
            ">
            <span v-if="hasPromotion" class="
                    absolute left-[13px] top-[13px] z-20
                    rounded-md bg-[#5ca5f7]
                    px-2 py-1.5
                    text-[9px] font-black uppercase
                    tracking-[0.06em] text-[#06111f]
                ">
                {{ discountPercentage }}% OFF
            </span>

            <Link :href="`/produtos/${product.slug}`" class="block h-full w-full">
                <img v-if="product.image_url" :src="product.image_url" :alt="product.name" loading="lazy" class="
                        h-full w-full object-cover
                        transition-transform duration-300
                        group-hover:scale-[1.035]
                    " />

                <div v-else class="
                        flex h-full w-full
                        items-center justify-center
                        px-8 text-center
                        text-xs font-semibold text-[#747e8d]
                    ">
                    Produto sem imagem
                </div>
            </Link>

            <div class="
                    pointer-events-none absolute inset-0
                    bg-gradient-to-b
                    from-transparent from-55%
                    to-[#07090d]/25
                " />
        </div>

        <div class="p-[18px]">
            <span class="
                    text-[9px] font-extrabold uppercase
                    tracking-[0.13em] text-[#6eaef6]
                ">
                {{
                    product.category
                    ?? product.brand
                    ?? 'Tech Store'
                }}
            </span>

            <h2 class="
                    mt-2 min-h-[41px]
                    line-clamp-2 text-[15px]
                    font-bold leading-[1.35]
                    text-[#f7f9fc]
                ">
                <Link :href="`/produtos/${product.slug}`" class="
                        transition-colors duration-150
                        hover:text-[#6eaef6]
                    ">
                    {{ product.name }}
                </Link>
            </h2>

            <div class="
                    mt-2 flex items-center gap-2
                    text-[10px]
                ">
                <span class="tracking-[1px] text-[#f7b955]" aria-hidden="true">
                    ★★★★★
                </span>

                <span :class="product.available_stock > 0
                        ? 'text-[#8b95a5]'
                        : 'text-[#d96c78]'
                    ">
                    {{
                        product.available_stock > 0
                            ? 'Disponível'
                            : 'Indisponível'
                    }}
                </span>
            </div>

            <div class="mt-[14px]">
                <del v-if="hasPromotion" class="
                        block text-[10px]
                        text-[#666f7d]
                    ">
                    {{ formatCurrency(regularPrice) }}
                </del>

                <strong class="
                        mt-0.5 block text-xl
                        font-bold text-[#f7f9fc]
                    ">
                    {{ formatCurrency(currentPrice) }}
                </strong>

                <small class="
                        mt-[3px] block
                        text-[10px] text-[#747e8d]
                    ">
                    ou 12x de
                    {{ formatCurrency(installmentPrice) }}
                    sem juros
                </small>
            </div>

            <Link :href="`/produtos/${product.slug}`" class="
                    mt-4 flex w-full
                    items-center justify-center gap-[9px]
                    rounded-[9px]
                    border border-[#293344]
                    bg-[#151b25]
                    px-3 py-[11px]
                    text-[11px] font-bold
                    text-[#ecf3ff]
                    transition-colors duration-200

                    hover:border-[#4c87db]
                    hover:bg-[#4c87db]
                    hover:text-[#07101e]
                ">
                Ver produto

                <svg class="h-4 w-4" viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor"
                    stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14" />
                    <path d="m13 6 6 6-6 6" />
                </svg>
            </Link>
        </div>
    </article>
</template>
