<script setup lang="ts">
import ProductCard from '@/Components/Store/ProductCard.vue';
import StoreLayout from '@/Layouts/StoreLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

interface Category {
    id: number;
    name: string;
    slug: string;
    products_count: number;
}

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

defineProps<{
    categories: Category[];
    featuredProducts: Product[];
}>();
</script>

<template>

    <Head title="Tecnologia que acompanha seu ritmo">
        <meta name="description"
            content="Hardware de alta performance, notebooks, monitores e periféricos para criar, jogar e ir além." />
    </Head>

    <StoreLayout>
        <section id="inicio" class="
                relative grid min-h-[630px]
                grid-cols-[1fr_1.05fr]
                items-center overflow-hidden
                border-b border-[#222936]

                max-[900px]:grid-cols-1
                max-[900px]:items-stretch
            ">
            <div class="
                    pointer-events-none absolute inset-0

                    bg-[linear-gradient(rgba(255,255,255,0.024)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.024)_1px,transparent_1px)]
                    bg-[length:52px_52px]

                    [mask-image:linear-gradient(90deg,#000,transparent_75%)]
                " />

            <div class="
                    pointer-events-none absolute
                    left-[-280px] top-20
                    h-[550px] w-[550px]
                    rounded-full bg-[#244d9b]
                    opacity-30 blur-[140px]
                " />

            <div class="
                    relative z-10
                    ml-[max(28px,calc(50vw_-_590px))]
                    w-[min(570px,calc(100%_-_50px))]
                    py-20

                    max-[900px]:py-[70px]
                    max-[900px]:pb-[30px]

                    max-[640px]:ml-4
                    max-[640px]:w-[calc(100%_-_32px)]
                    max-[640px]:pt-[58px]
                ">
                <p class="
                        mb-5 flex items-center gap-2.5
                        text-[11px] font-extrabold
                        tracking-[0.18em] text-[#78b6ff]
                    ">
                    <span class="h-px w-[22px] bg-[#6aaeff]" />

                    PERFORMANCE SEM LIMITES
                </p>

                <h1 class="
                        m-0 max-w-[650px]
                        text-[clamp(46px,5vw,76px)]
                        font-normal leading-[0.98]
                        tracking-[-0.055em]

                        max-[640px]:text-[46px]
                    ">
                    Tecnologia que
                    <br />
                    acompanha o seu

                    <em class="
                            bg-gradient-to-r
                            from-[#57adff] to-[#a67aff]
                            bg-clip-text not-italic text-transparent
                        ">
                        ritmo.
                    </em>
                </h1>

                <p class="
                        my-[26px] max-w-[570px]
                        text-[17px] leading-[1.75]
                        text-[#aab2bf]

                        max-[640px]:text-[15px]
                    ">
                    Hardware de alta performance, periféricos de precisão
                    e tudo o que você precisa para criar, jogar e ir além.
                </p>

                <div class="flex items-center gap-[27px]">
                    <Link href="/produtos" class="
                            inline-flex items-center justify-center
                            gap-3 rounded-[10px]
                            bg-gradient-to-br
                            from-[#4a99ed] to-[#7d61ed]
                            px-5 py-3.5
                            text-sm font-bold text-white
                            shadow-[0_12px_30px_rgb(75_132_235/20%)]
                            transition-all duration-200

                            hover:-translate-y-0.5
                            hover:brightness-110
                        ">
                        Ver produtos

                        <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" aria-hidden="true" fill="none"
                            stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14" />
                            <path d="m13 6 6 6-6 6" />
                        </svg>
                    </Link>

                    <Link href="/produtos?on_sale=1" class="
                            border-b border-[#4b5260]
                            pb-1 text-sm font-bold
                            text-[#c6cfdb]
                            transition-colors
                            hover:text-white
                        ">
                        Explorar ofertas
                    </Link>
                </div>

                <div class="
                        mt-[34px] flex gap-6
                        text-xs text-[#7f8998]

                        max-[640px]:flex-wrap
                        max-[640px]:gap-[11px]
                    ">
                    <span>
                        ✓ Compra 100% segura
                    </span>

                    <span>
                        ✓ Até 12x sem juros
                    </span>
                </div>
            </div>

            <div class="
                    relative h-full min-h-[630px] overflow-hidden

                    max-[900px]:min-h-[430px]
                    max-[640px]:min-h-[340px]
                ">
                <img :src="'/images/store/hero-tech-store.png'"
                    alt="Computador e monitor em um setup tecnológico com iluminação azul e violeta"
                    class="h-full w-full object-cover saturate-[0.9] contrast-[1.04]">

                <div class="
                        pointer-events-none absolute inset-0

                        bg-[linear-gradient(90deg,#07090d_0%,transparent_25%),linear-gradient(0deg,#07090d_0%,transparent_28%)]

                        max-[900px]:bg-[linear-gradient(0deg,#07090d_0%,transparent_25%),linear-gradient(180deg,#07090d_0%,transparent_25%)]
                    " />

                <div class="
                        absolute bottom-[14%] right-[8%] z-10
                        flex flex-col rounded-[10px]
                        border border-white/15
                        bg-[#090c12]/70 px-4 py-3
                        backdrop-blur-[10px]

                        max-[640px]:bottom-[9%]
                    ">
                    <span class="
                            text-[9px] font-extrabold
                            tracking-[0.16em] text-[#69adfa]
                        ">
                        RTX SERIES
                    </span>

                    <strong class="mt-[3px] text-[13px]">
                        Potência máxima
                    </strong>
                </div>
            </div>
        </section>

        <section aria-label="Benefícios" class="
                mx-auto grid
                w-[min(1180px,calc(100%_-_40px))]
                grid-cols-3
                border-b border-[#222936]
                py-7

                max-[640px]:w-[calc(100%_-_32px)]
                max-[640px]:grid-cols-1
                max-[640px]:py-3
            ">
            <article class="
                    flex items-center justify-center
                    gap-3.5 border-r border-[#222936]

                    max-[900px]:px-2.5

                    max-[640px]:justify-start
                    max-[640px]:border-b
                    max-[640px]:border-r-0
                    max-[640px]:px-2
                    max-[640px]:py-4
                ">
                <svg class="h-6 w-6 text-[#5fa8ff]" viewBox="0 0 24 24" aria-hidden="true" fill="none"
                    stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 6h11v10H3zM14 10h4l3 3v3h-7z" />

                    <circle cx="7" cy="18" r="2" />

                    <circle cx="18" cy="18" r="2" />
                </svg>

                <div class="flex flex-col gap-[3px]">
                    <strong class="text-[13px]">
                        Entrega rápida
                    </strong>

                    <span class="text-[11px] text-[#828c9b]">
                        Envio para todo o Brasil
                    </span>
                </div>
            </article>

            <article class="
                    flex items-center justify-center
                    gap-3.5 border-r border-[#222936]

                    max-[900px]:px-2.5

                    max-[640px]:justify-start
                    max-[640px]:border-b
                    max-[640px]:border-r-0
                    max-[640px]:px-2
                    max-[640px]:py-4
                ">
                <svg class="h-6 w-6 text-[#5fa8ff]" viewBox="0 0 24 24" aria-hidden="true" fill="none"
                    stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 3 4 6v5c0 5 3.4 8.6 8 10 4.6-1.4 8-5 8-10V6z" />
                </svg>

                <div class="flex flex-col gap-[3px]">
                    <strong class="text-[13px]">
                        Compra protegida
                    </strong>

                    <span class="text-[11px] text-[#828c9b]">
                        Ambiente 100% seguro
                    </span>
                </div>
            </article>

            <article class="
                    flex items-center justify-center gap-3.5

                    max-[900px]:px-2.5

                    max-[640px]:justify-start
                    max-[640px]:px-2
                    max-[640px]:py-4
                ">
                <svg class="h-6 w-6 text-[#5fa8ff]" viewBox="0 0 24 24" aria-hidden="true" fill="none"
                    stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 14v-2a8 8 0 0 1 16 0v2" />

                    <path d="
                            M4 14a2 2 0 0 1 2-2h2v7H6a2 2 0 0 1-2-2z
                            M20 14a2 2 0 0 0-2-2h-2v7h2a2 2 0 0 0 2-2z
                        " />
                </svg>

                <div class="flex flex-col gap-[3px]">
                    <strong class="text-[13px]">
                        Suporte especialista
                    </strong>

                    <span class="text-[11px] text-[#828c9b]">
                        Conte com quem entende
                    </span>
                </div>
            </article>
        </section>

        <section id="produtos" class="
                mx-auto w-[min(1180px,calc(100%_-_40px))]
                py-[90px]

                max-[640px]:w-[calc(100%_-_32px)]
                max-[640px]:py-[70px]
            ">
            <div class="flex items-end justify-between gap-[30px]">
                <div>
                    <p class="
                            mb-3 flex items-center gap-2.5
                            text-[11px] font-extrabold
                            tracking-[0.18em] text-[#78b6ff]
                        ">
                        <span class="h-px w-[22px] bg-[#6aaeff]" />

                        DESTAQUES
                    </p>

                    <h2 class="
                            m-0 text-[clamp(30px,3vw,46px)]
                            font-bold tracking-[-0.045em]
                        ">
                        Escolhidos para elevar seu setup
                    </h2>
                </div>

                <Link href="/produtos?featured=1" class="
                        flex items-center gap-2
                        text-[13px] font-bold
                        text-[#9eabc0]
                        transition-colors
                        hover:text-white

                        max-[640px]:hidden
                    ">
                    Ver todos

                    <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" aria-hidden="true" fill="none"
                        stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12h14" />
                        <path d="m13 6 6 6-6 6" />
                    </svg>
                </Link>
            </div>

            <div id="categorias" class="
                    mt-[38px] mb-6
                    flex gap-2 overflow-x-auto pb-1
                " aria-label="Filtrar por categoria">
                <Link href="/produtos" class="
                        whitespace-nowrap rounded-full
                        border border-[#558fda]
                        bg-[#15263e] px-[15px] py-[9px]
                        text-xs text-[#dbeeff]
                    ">
                    Todos
                </Link>

                <Link v-for="category in categories" :key="category.id" :href="`/produtos?category=${category.slug}`"
                    class="
                        whitespace-nowrap rounded-full
                        border border-[#222936]
                        bg-[#0d1117] px-[15px] py-[9px]
                        text-xs text-[#929cac]
                        transition-colors

                        hover:border-[#558fda]
                        hover:bg-[#15263e]
                        hover:text-[#dbeeff]
                    ">
                    {{ category.name }}
                </Link>
            </div>

            <div v-if="featuredProducts.length" class="
                    grid grid-cols-3 gap-[18px]

                    max-[900px]:grid-cols-2
                    max-[640px]:grid-cols-1
                ">
                <ProductCard v-for="product in featuredProducts" :key="product.id" :product="product" />
            </div>

            <div v-else class="
                    rounded-2xl border border-dashed
                    border-[#2b3340]
                    px-5 py-[70px]
                    text-center text-[#808b9d]
                ">
                <svg class="mx-auto h-[35px] w-[35px]" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="1.8" aria-hidden="true">
                    <circle cx="11" cy="11" r="7" />

                    <path d="m20 20-4-4" />
                </svg>

                <h3 class="mt-4 font-bold text-[#e9edf3]">
                    Nenhum produto em destaque
                </h3>

                <p class="mt-1.5 text-[13px]">
                    Os produtos destacados aparecerão aqui.
                </p>
            </div>
        </section>

        <section id="ofertas" class="
                relative mx-auto mb-[90px]
                flex min-h-[300px]
                w-[min(1180px,calc(100%_-_40px))]
                items-center justify-between gap-[35px]
                overflow-hidden rounded-[22px]
                border border-[#2d3950]
                px-[60px] py-[55px]

                [background:radial-gradient(circle_at_80%,rgb(112_77_223/50%),transparent_31%),linear-gradient(120deg,#111c2c,#0c1017_58%)]

                max-[900px]:px-9
                max-[900px]:py-[45px]

                max-[640px]:w-[calc(100%_-_32px)]
                max-[640px]:flex-col
                max-[640px]:items-start
                max-[640px]:px-[25px]
                max-[640px]:py-[38px]
            ">
            <span aria-hidden="true" class="
                    absolute right-[17%]
                    text-[150px] font-black
                    tracking-[-0.08em] text-white/[0.035]

                    max-[900px]:right-[4%]

                    max-[640px]:bottom-0
                    max-[640px]:text-[100px]
                ">
                25%
            </span>

            <div class="relative z-10">
                <p class="
                        mb-[13px] flex items-center gap-2.5
                        text-[11px] font-extrabold
                        tracking-[0.18em] text-[#78b6ff]
                    ">
                    <span class="h-px w-[22px] bg-[#6aaeff]" />

                    OFERTA DA SEMANA
                </p>

                <h2 class="
                        m-0 text-[40px] font-bold
                        leading-none tracking-[-0.045em]

                        max-[640px]:text-[34px]
                    ">
                    Seu próximo upgrade
                    <br />
                    começa aqui.
                </h2>

                <p class="mt-[18px] text-sm text-[#8f99a8]">
                    Componentes selecionados com até 25% de desconto.
                </p>
            </div>

            <Link href="/produtos?on_sale=1" class="
                    relative z-10
                    inline-flex shrink-0
                    items-center justify-center gap-3
                    rounded-[10px]
                    bg-gradient-to-br
                    from-[#4a99ed] to-[#7d61ed]
                    px-5 py-3.5
                    text-sm font-bold text-white
                    shadow-[0_12px_30px_rgb(75_132_235/20%)]
                    transition-all duration-200

                    hover:-translate-y-0.5
                    hover:brightness-110
                ">
                Aproveitar agora

                <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor"
                    stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14" />
                    <path d="m13 6 6 6-6 6" />
                </svg>
            </Link>
        </section>
    </StoreLayout>
</template>
