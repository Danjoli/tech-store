<script setup lang="ts">
import ProductCard from '@/Components/Store/ProductCard.vue';
import StoreLayout from '@/Layouts/StoreLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

interface ProductImage {
    id: number;
    url: string;
    alt_text: string | null;
    product_variant_id: number | null;
    is_primary: boolean;
    sort_order: number;
}

interface ProductVariant {
    id: number;
    name: string;
    sku: string;
    price: string | number;
    sale_price: string | number | null;
    available_stock: number;
    attributes: Record<string, string>;
    is_default: boolean;
}

interface ProductReference {
    id: number;
    name: string;
    slug: string;
}

interface Product {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    brand: ProductReference | null;
    category: ProductReference | null;
    images: ProductImage[];
    variants: ProductVariant[];
    default_variant_id: number;
    is_favorited: boolean;
}

interface RelatedProduct {
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
    relatedProducts: RelatedProduct[];
}>();

const selectedVariantId = ref(props.product.default_variant_id);
const selectedImageId = ref<number | null>(null);
const quantity = ref(1);

const selectedVariant = computed(
    () =>
        props.product.variants.find(
            (variant) =>
                variant.id === selectedVariantId.value,
        ) ?? props.product.variants[0],
);

const visibleImages = computed(() => {
    const variantImages = props.product.images.filter(
        (image) =>
            image.product_variant_id
            === selectedVariant.value?.id,
    );

    const generalImages = props.product.images.filter(
        (image) => image.product_variant_id === null,
    );

    return variantImages.length > 0
        ? [...variantImages, ...generalImages]
        : generalImages.length > 0
            ? generalImages
            : props.product.images;
});

const selectedImage = computed(
    () =>
        visibleImages.value.find(
            (image) => image.id === selectedImageId.value,
        )
        ?? visibleImages.value[0]
        ?? null,
);

const regularPrice = computed(() =>
    Number(selectedVariant.value?.price ?? 0),
);

const promotionalPrice = computed(() =>
    Number(selectedVariant.value?.sale_price ?? 0),
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

const installmentPrice = computed(
    () => currentPrice.value / 12,
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

const availableStock = computed(
    () => selectedVariant.value?.available_stock ?? 0,
);

const maximumQuantity = computed(() => Math.min(10, availableStock.value));


function formatCurrency(value: number): string {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    }).format(value);
}

function selectVariant(variantId: number): void {
    selectedVariantId.value = variantId;
}

function toggleFavorite(): void {
    router.post(`/favoritos/${props.product.slug}/alternar`, {}, {
        preserveScroll: true,
    });
}

function decreaseQuantity(): void {
    if (quantity.value > 1) quantity.value--;
}

function increaseQuantity(): void {
    if (quantity.value < maximumQuantity.value) quantity.value++;
}

function addToCart(): void {
    if (!selectedVariant.value || availableStock.value < 1) return;

    router.post('/carrinho/itens', {
        product_variant_id: selectedVariant.value.id,
        quantity: quantity.value,
    }, { preserveScroll: true });
}

watch(
    selectedVariantId,
    () => {
        selectedImageId.value =
            visibleImages.value[0]?.id ?? null;
        quantity.value = 1;

    },
    {
        immediate: true,
    },
);
</script>

<template>

    <Head :title="product.name">
        <meta name="description" :content="product.description?.slice(0, 155)
            ?? `Conheça ${product.name} na Tech Store.`
            " />
    </Head>

    <StoreLayout>
        <section class="
                mx-auto w-[min(1180px,calc(100%_-_40px))]
                py-[50px]

                max-[640px]:w-[calc(100%_-_32px)]
                max-[640px]:py-8
            ">
            <nav aria-label="Navegação estrutural" class="
                    flex flex-wrap items-center
                    gap-2 text-xs text-[#778190]
                ">
                <Link href="/" class="transition-colors hover:text-[#6eaef6]">
                    Início
                </Link>

                <span>/</span>

                <Link href="/produtos" class="transition-colors hover:text-[#6eaef6]">
                    Produtos
                </Link>

                <template v-if="product.category">
                    <span>/</span>

                    <Link :href="`/produtos?category=${product.category.slug}`"
                        class="transition-colors hover:text-[#6eaef6]">
                        {{ product.category.name }}
                    </Link>
                </template>

                <span>/</span>

                <span class="text-[#aeb6c4]">
                    {{ product.name }}
                </span>
            </nav>

            <div class="
                    mt-8 grid
                    grid-cols-[1.08fr_0.92fr]
                    gap-[50px]

                    max-[900px]:grid-cols-1
                    max-[900px]:gap-9
                ">
                <section aria-label="Galeria do produto">
                    <div class="
                            relative aspect-square
                            overflow-hidden rounded-[18px]
                            border border-[#222936]
                            bg-[#10141c]
                        ">
                        <span v-if="hasPromotion" class="
                                absolute left-4 top-4 z-20
                                rounded-md bg-[#5ca5f7]
                                px-2.5 py-1.5
                                text-[10px] font-black
                                uppercase tracking-[0.06em]
                                text-[#06111f]
                            ">
                            {{ discountPercentage }}% OFF
                        </span>

                        <img v-if="selectedImage" :src="selectedImage.url" :alt="selectedImage.alt_text
                            ?? product.name
                            " class="
                                h-full w-full
                                object-contain p-8

                                max-[640px]:p-4
                            " />

                        <div v-else class="
                                flex h-full items-center
                                justify-center text-sm
                                text-[#747e8d]
                            ">
                            Produto sem imagem
                        </div>

                        <div class="
                                pointer-events-none absolute inset-0
                                bg-[radial-gradient(circle_at_70%_20%,rgb(80_168_255/8%),transparent_38%)]
                            " />
                    </div>

                    <div v-if="visibleImages.length > 1" class="
                            mt-4 flex gap-3
                            overflow-x-auto pb-2
                        ">
                        <button v-for="image in visibleImages" :key="image.id" type="button" class="
                                h-[82px] w-[82px]
                                shrink-0 overflow-hidden
                                rounded-[10px] border
                                bg-[#10141c]
                                transition-colors
                            " :class="selectedImage?.id === image.id
                                    ? 'border-[#558fda]'
                                    : 'border-[#222936] hover:border-[#34445b]'
                                " @click="selectedImageId = image.id">
                            <img :src="image.url" :alt="image.alt_text
                                ?? product.name
                                " class="
                                    h-full w-full
                                    object-contain p-2
                                " />
                        </button>
                    </div>
                </section>

                <section>
                    <p class="
                            flex items-center gap-2.5
                            text-[10px] font-extrabold
                            uppercase tracking-[0.16em]
                            text-[#6eaef6]
                        ">
                        <span class="h-px w-[22px] bg-[#6aaeff]" />

                        {{
                            product.category?.name
                            ?? 'Tech Store'
                        }}
                    </p>

                    <div class="mt-4 flex items-start justify-between gap-5">
                        <h1 class="text-[clamp(34px,4vw,54px)] font-normal leading-[1.04] tracking-[-0.05em] text-[#f7f9fc]">
                            {{ product.name }}
                        </h1>

                        <button
                            type="button"
                            class="grid h-11 w-11 shrink-0 place-items-center rounded-xl border border-[#293344] bg-[#11151c] text-[#c7cfdb] transition hover:border-[#8b80fa] hover:text-white"
                            :class="product.is_favorited ? 'border-[#8b80fa] bg-[#6f60ec] text-white' : ''"
                            :aria-label="product.is_favorited ? 'Remover dos favoritos' : 'Adicionar aos favoritos'"
                            @click="toggleFavorite"
                        >
                            <svg class="h-5 w-5" viewBox="0 0 24 24" aria-hidden="true" fill="currentColor" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8l1.1 1.1L12 21l7.7-7.5 1.1-1.1a5.5 5.5 0 0 0 0-7.8Z" :fill="product.is_favorited ? 'currentColor' : 'none'" />
                            </svg>
                        </button>
                    </div>

                    <div class="
                            mt-5 flex flex-wrap
                            items-center gap-4
                            text-xs text-[#778190]
                        ">
                        <span v-if="product.brand">
                            Marca:

                            <Link :href="`/produtos?brand=${product.brand.slug}`" class="
                                    font-bold text-[#aeb6c4]
                                    hover:text-[#6eaef6]
                                ">
                                {{ product.brand.name }}
                            </Link>
                        </span>

                        <span>
                            SKU:
                            <strong class="text-[#aeb6c4]">
                                {{ selectedVariant.sku }}
                            </strong>
                        </span>
                    </div>

                    <div class="
                            mt-7 border-y border-[#222936]
                            py-6
                        ">
                        <del v-if="hasPromotion" class="
                                block text-sm text-[#666f7d]
                            ">
                            {{ formatCurrency(regularPrice) }}
                        </del>

                        <strong class="
                                mt-1 block text-[34px]
                                font-bold tracking-[-0.03em]
                                text-[#f7f9fc]
                            ">
                            {{ formatCurrency(currentPrice) }}
                        </strong>

                        <p class="mt-1 text-xs text-[#747e8d]">
                            ou 12x de
                            <strong class="text-[#aeb6c4]">
                                {{ formatCurrency(installmentPrice) }}
                            </strong>
                            sem juros
                        </p>
                    </div>

                    <div v-if="product.variants.length > 1" class="mt-7">
                        <div class="
                                flex items-center
                                justify-between gap-4
                            ">
                            <h2 class="
                                    text-xs font-extrabold
                                    uppercase tracking-[0.12em]
                                    text-[#aeb6c4]
                                ">
                                Escolha uma opção
                            </h2>

                            <span class="text-[10px] text-[#778190]">
                                {{ selectedVariant.name }}
                            </span>
                        </div>

                        <div class="
                                mt-3 flex flex-wrap gap-2
                            ">
                            <button v-for="variant in product.variants" :key="variant.id" type="button"
                                :disabled="variant.available_stock <= 0" class="
                                    rounded-[9px] border
                                    px-4 py-2.5
                                    text-xs font-bold
                                    transition-colors

                                    disabled:cursor-not-allowed
                                    disabled:opacity-40
                                " :class="selectedVariantId === variant.id
                                        ? 'border-[#558fda] bg-[#15263e] text-[#dbeeff]'
                                        : 'border-[#293344] bg-[#11151c] text-[#929cac] hover:border-[#558fda] hover:text-white'
                                    " @click="selectVariant(variant.id)">
                                {{ variant.name }}
                            </button>
                        </div>
                    </div>

                    <dl v-if="
                        Object.keys(
                            selectedVariant.attributes,
                        ).length
                    " class="
                            mt-6 grid grid-cols-2
                            gap-x-6 gap-y-3
                            rounded-xl border
                            border-[#222936]
                            bg-[#0d1117] p-4
                        ">
                        <div v-for="(
value,
                                    attribute
                            ) in selectedVariant.attributes" :key="attribute">
                            <dt class="
                                    text-[9px] font-extrabold
                                    uppercase tracking-[0.1em]
                                    text-[#697487]
                                ">
                                {{ attribute }}
                            </dt>

                            <dd class="
                                    mt-1 text-xs
                                    font-semibold text-[#c7cfdb]
                                ">
                                {{ value }}
                            </dd>
                        </div>
                    </dl>

                    <div class="mt-7">
                        <p class="text-xs font-bold" :class="availableStock > 0
                                ? 'text-[#70c69a]'
                                : 'text-[#d96c78]'
                            ">
                            {{
                                availableStock > 0
                                    ? `${availableStock} unidade(s) disponível(is)`
                                    : 'Produto indisponível'
                            }}
                        </p>

                        <div class="mt-4 grid grid-cols-[110px_1fr] gap-3 max-[480px]:grid-cols-1">
                            <div class="flex h-[48px] items-center justify-between rounded-[10px] border border-[#293344] bg-[#11151c]">
                                <button type="button" :disabled="quantity <= 1" class="grid h-full w-9 place-items-center text-lg text-[#aeb6c4] hover:text-white disabled:cursor-not-allowed disabled:opacity-30" aria-label="Diminuir quantidade" @click="decreaseQuantity">−</button>
                                <span class="text-sm font-bold">{{ quantity }}</span>
                                <button type="button" :disabled="quantity >= maximumQuantity" class="grid h-full w-9 place-items-center text-lg text-[#aeb6c4] hover:text-white disabled:cursor-not-allowed disabled:opacity-30" aria-label="Aumentar quantidade" @click="increaseQuantity">+</button>
                            </div>
                            <button type="button" :disabled="availableStock < 1" class="flex h-[48px] items-center justify-center gap-2.5 rounded-[10px] bg-gradient-to-br from-[#4a99ed] to-[#7d61ed] px-5 text-sm font-bold text-white shadow-[0_12px_30px_rgb(75_132_235/20%)] transition-all hover:-translate-y-0.5 hover:brightness-110 disabled:cursor-not-allowed disabled:opacity-40 disabled:hover:translate-y-0 disabled:hover:brightness-100" @click="addToCart">
                                Adicionar ao carrinho
                                <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3h2l2.4 11.2a2 2 0 0 0 2 1.6h7.9a2 2 0 0 0 2-1.6L21 7H6" /><circle cx="10" cy="20" r="1" /><circle cx="18" cy="20" r="1" /></svg>
                            </button>
                        </div>
                    </div>

                    <div class="
                            mt-7 grid grid-cols-3
                            gap-3 border-t
                            border-[#222936] pt-6

                            max-[480px]:grid-cols-1
                        ">
                        <div>
                            <strong class="
                                    block text-[10px]
                                    text-[#c7cfdb]
                                ">
                                Compra segura
                            </strong>

                            <span class="text-[9px] text-[#697487]">
                                Dados protegidos
                            </span>
                        </div>

                        <div>
                            <strong class="
                                    block text-[10px]
                                    text-[#c7cfdb]
                                ">
                                Entrega rápida
                            </strong>

                            <span class="text-[9px] text-[#697487]">
                                Para todo o Brasil
                            </span>
                        </div>

                        <div>
                            <strong class="
                                    block text-[10px]
                                    text-[#c7cfdb]
                                ">
                                Suporte
                            </strong>

                            <span class="text-[9px] text-[#697487]">
                                Atendimento especializado
                            </span>
                        </div>
                    </div>
                </section>
            </div>

            <section v-if="product.description" class="
                    mt-20 border-t
                    border-[#222936] pt-14
                ">
                <p class="
                        mb-3 flex items-center gap-2.5
                        text-[11px] font-extrabold
                        tracking-[0.18em] text-[#78b6ff]
                    ">
                    <span class="h-px w-[22px] bg-[#6aaeff]" />

                    SOBRE O PRODUTO
                </p>

                <h2 class="
                        text-[clamp(28px,3vw,40px)]
                        font-normal tracking-[-0.045em]
                    ">
                    Detalhes e desempenho
                </h2>

                <p class="
                        mt-6 max-w-4xl
                        whitespace-pre-line
                        text-[15px] leading-7
                        text-[#9ca6b7]
                    ">
                    {{ product.description }}
                </p>
            </section>

            <section v-if="relatedProducts.length" class="
                    mt-20 border-t
                    border-[#222936] pt-14
                ">
                <div class="
                        flex items-end
                        justify-between gap-6
                    ">
                    <div>
                        <p class="
                                mb-3 flex items-center gap-2.5
                                text-[11px] font-extrabold
                                tracking-[0.18em]
                                text-[#78b6ff]
                            ">
                            <span class="
                                    h-px w-[22px]
                                    bg-[#6aaeff]
                                " />

                            RECOMENDADOS
                        </p>

                        <h2 class="
                                text-[clamp(28px,3vw,40px)]
                                font-normal
                                tracking-[-0.045em]
                            ">
                            Você também pode gostar
                        </h2>
                    </div>

                    <Link href="/produtos" class="
                            text-xs font-bold
                            text-[#9eabc0]
                            hover:text-white

                            max-[640px]:hidden
                        ">
                        Ver catálogo →
                    </Link>
                </div>

                <div class="
                        mt-8 grid grid-cols-3
                        gap-[18px]

                        max-[900px]:grid-cols-2
                        max-[640px]:grid-cols-1
                    ">
                    <ProductCard v-for="relatedProduct in relatedProducts" :key="relatedProduct.id"
                        :product="relatedProduct" />
                </div>
            </section>
        </section>
    </StoreLayout>
</template>
