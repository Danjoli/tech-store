<script setup lang="ts">
import ProductCard from '@/Components/Store/ProductCard.vue';
import StoreLayout from '@/Layouts/StoreLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, reactive, ref } from 'vue';

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

interface FilterOption {
    id: number;
    name: string;
    slug: string;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedProducts {
    data: Product[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
    links: PaginationLink[];
}

interface Filters {
    search: string;
    category: string;
    brand: string;
    min_price: string | number;
    max_price: string | number;
    featured: boolean;
    on_sale: boolean;
    in_stock: boolean;
    sort: string;
}

const props = defineProps<{
    products: PaginatedProducts;
    categories: FilterOption[];
    brands: FilterOption[];
    filters: Filters;
}>();

const mobileFiltersOpen = ref(false);

const form = reactive<Filters>({
    search: props.filters.search,
    category: props.filters.category,
    brand: props.filters.brand,
    min_price: props.filters.min_price,
    max_price: props.filters.max_price,
    featured: props.filters.featured,
    on_sale: props.filters.on_sale,
    in_stock: props.filters.in_stock,
    sort: props.filters.sort,
});

const hasActiveFilters = computed(
    () =>
        Boolean(form.search)
        || Boolean(form.category)
        || Boolean(form.brand)
        || form.min_price !== ''
        || form.max_price !== ''
        || form.featured
        || form.on_sale
        || form.in_stock,
);

function applyFilters(): void {
    const query: Record<string, string | number | boolean> = {};

    if (form.search.trim()) {
        query.search = form.search.trim();
    }

    if (form.category) {
        query.category = form.category;
    }

    if (form.brand) {
        query.brand = form.brand;
    }

    if (form.min_price !== '') {
        query.min_price = form.min_price;
    }

    if (form.max_price !== '') {
        query.max_price = form.max_price;
    }

    if (form.featured) {
        query.featured = true;
    }

    if (form.on_sale) {
        query.on_sale = true;
    }

    if (form.in_stock) {
        query.in_stock = true;
    }

    if (form.sort !== 'newest') {
        query.sort = form.sort;
    }

    router.get('/produtos', query, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
        onSuccess: () => {
            mobileFiltersOpen.value = false;
        },
    });
}

function clearFilters(): void {
    form.search = '';
    form.category = '';
    form.brand = '';
    form.min_price = '';
    form.max_price = '';
    form.featured = false;
    form.on_sale = false;
    form.in_stock = false;
    form.sort = 'newest';

    router.get(
        '/produtos',
        {},
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
}
</script>

<template>

    <Head title="Produtos">
        <meta name="description"
            content="Explore computadores, componentes, periféricos e acessórios disponíveis na Tech Store." />
    </Head>

    <StoreLayout>
        <section class="
                mx-auto min-h-[600px]
                w-[min(1180px,calc(100%_-_40px))]
                py-[70px]

                max-[640px]:w-[calc(100%_-_32px)]
                max-[640px]:py-12
            ">
            <nav aria-label="Navegação estrutural" class="text-xs text-[#778190]">
                <Link href="/" class="transition-colors hover:text-[#6eaef6]">
                    Início
                </Link>

                <span class="mx-2">
                    /
                </span>

                <span class="text-[#aeb6c4]">
                    Produtos
                </span>
            </nav>

            <div class="
                    mt-6 flex items-end
                    justify-between gap-8

                    max-[640px]:items-start
                    max-[640px]:flex-col
                ">
                <div>
                    <p class="
                            mb-3 flex items-center gap-2.5
                            text-[11px] font-extrabold
                            tracking-[0.18em] text-[#78b6ff]
                        ">
                        <span class="h-px w-[22px] bg-[#6aaeff]" />

                        CATÁLOGO
                    </p>

                    <h1 class="
                            text-[clamp(36px,5vw,58px)]
                            font-normal leading-none
                            tracking-[-0.055em]
                            text-[#f7f9fc]
                        ">
                        Encontre seu próximo
                        <span class="
                                bg-gradient-to-r
                                from-[#57adff] to-[#a67aff]
                                bg-clip-text text-transparent
                            ">
                            upgrade.
                        </span>
                    </h1>

                    <p class="mt-4 max-w-2xl text-sm text-[#8f99a8]">
                        Hardware e periféricos selecionados para entregar
                        desempenho, precisão e confiança.
                    </p>
                </div>

                <p class="shrink-0 text-xs text-[#778190]">
                    <strong class="text-[#dbe3ef]">
                        {{ products.total }}
                    </strong>
                    produto(s)
                </p>
            </div>

            <form class="
                    mt-10 flex items-center gap-3
                    rounded-xl border border-[#222936]
                    bg-[#0d1117] p-2
                " role="search" @submit.prevent="applyFilters">
                <svg class="ml-2 h-5 w-5 shrink-0 text-[#697487]" viewBox="0 0 24 24" aria-hidden="true" fill="none"
                    stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="7" />

                    <path d="m20 20-4-4" />
                </svg>

                <label for="catalog-search" class="sr-only">
                    Buscar no catálogo
                </label>

                <input id="catalog-search" v-model="form.search" type="search"
                    placeholder="Buscar por produto, marca, categoria ou SKU" class="
                        min-w-0 flex-1 border-0
                        bg-transparent px-1 py-2
                        text-sm text-white outline-none
                        placeholder:text-[#687282]
                    " />

                <button type="submit" class="
                        rounded-[9px]
                        bg-gradient-to-br
                        from-[#4a99ed] to-[#7d61ed]
                        px-5 py-2.5
                        text-xs font-bold text-white
                        transition-all

                        hover:brightness-110
                    ">
                    Buscar
                </button>
            </form>

            <div class="
                    mt-6 flex flex-wrap
                    items-center justify-between gap-4
                ">
                <div class="flex items-center gap-3">
                    <button type="button" class="
                            rounded-[9px]
                            border border-[#293344]
                            bg-[#151b25]
                            px-4 py-2.5
                            text-xs font-bold text-[#ecf3ff]

                            min-[901px]:hidden
                        " @click="mobileFiltersOpen = !mobileFiltersOpen">
                        {{
                            mobileFiltersOpen
                                ? 'Fechar filtros'
                                : 'Filtros'
                        }}
                    </button>

                    <button v-if="hasActiveFilters" type="button" class="
                            text-xs font-bold
                            text-[#d96c78]
                            transition-colors
                            hover:text-[#ff9da8]
                        " @click="clearFilters">
                        Limpar filtros
                    </button>
                </div>

                <div class="flex items-center gap-3">
                    <label for="sort" class="
                            text-xs font-semibold
                            text-[#778190]

                            max-[640px]:hidden
                        ">
                        Ordenar:
                    </label>

                    <select id="sort" v-model="form.sort" class="
                            rounded-[9px]
                            border border-[#293344]
                            bg-[#11151c]
                            px-4 py-2.5
                            text-xs font-semibold
                            text-[#c7cfdb]
                            outline-none

                            focus:border-[#558fda]
                        " @change="applyFilters">
                        <option value="newest">
                            Mais recentes
                        </option>

                        <option value="price_asc">
                            Menor preço
                        </option>

                        <option value="price_desc">
                            Maior preço
                        </option>

                        <option value="name_asc">
                            Nome: A–Z
                        </option>

                        <option value="name_desc">
                            Nome: Z–A
                        </option>
                    </select>
                </div>
            </div>

            <div class="
                    mt-6 grid
                    grid-cols-[245px_minmax(0,1fr)]
                    gap-6

                    max-[900px]:grid-cols-1
                ">
                <aside :class="mobileFiltersOpen
                        ? 'block'
                        : 'max-[900px]:hidden'
                    " class="
                        h-fit rounded-2xl
                        border border-[#222936]
                        bg-[#0d1117] p-5
                    ">
                    <div class="flex items-center justify-between">
                        <h2 class="text-sm font-bold text-[#f7f9fc]">
                            Filtros
                        </h2>

                        <button v-if="hasActiveFilters" type="button" class="
                                text-[10px] font-bold
                                text-[#d96c78]
                                hover:text-[#ff9da8]
                            " @click="clearFilters">
                            Limpar
                        </button>
                    </div>

                    <div class="mt-6">
                        <label for="category" class="
                                mb-2 block text-[10px]
                                font-extrabold uppercase
                                tracking-[0.12em]
                                text-[#778190]
                            ">
                            Categoria
                        </label>

                        <select id="category" v-model="form.category" class="
                                w-full rounded-[9px]
                                border border-[#293344]
                                bg-[#11151c]
                                px-3 py-2.5
                                text-xs text-[#c7cfdb]
                                outline-none

                                focus:border-[#558fda]
                            ">
                            <option value="">
                                Todas
                            </option>

                            <option v-for="category in categories" :key="category.id" :value="category.slug">
                                {{ category.name }}
                            </option>
                        </select>
                    </div>

                    <div class="mt-5">
                        <label for="brand" class="
                                mb-2 block text-[10px]
                                font-extrabold uppercase
                                tracking-[0.12em]
                                text-[#778190]
                            ">
                            Marca
                        </label>

                        <select id="brand" v-model="form.brand" class="
                                w-full rounded-[9px]
                                border border-[#293344]
                                bg-[#11151c]
                                px-3 py-2.5
                                text-xs text-[#c7cfdb]
                                outline-none

                                focus:border-[#558fda]
                            ">
                            <option value="">
                                Todas
                            </option>

                            <option v-for="brand in brands" :key="brand.id" :value="brand.slug">
                                {{ brand.name }}
                            </option>
                        </select>
                    </div>

                    <fieldset class="mt-5">
                        <legend class="
                                mb-2 text-[10px]
                                font-extrabold uppercase
                                tracking-[0.12em]
                                text-[#778190]
                            ">
                            Preço
                        </legend>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label for="min-price" class="sr-only">
                                    Preço mínimo
                                </label>

                                <input id="min-price" v-model="form.min_price" type="number" min="0" step="0.01"
                                    placeholder="Mín." class="
                                        w-full rounded-[9px]
                                        border border-[#293344]
                                        bg-[#11151c]
                                        px-3 py-2.5
                                        text-xs text-white
                                        outline-none
                                        placeholder:text-[#687282]

                                        focus:border-[#558fda]
                                    " />
                            </div>

                            <div>
                                <label for="max-price" class="sr-only">
                                    Preço máximo
                                </label>

                                <input id="max-price" v-model="form.max_price" type="number" min="0" step="0.01"
                                    placeholder="Máx." class="
                                        w-full rounded-[9px]
                                        border border-[#293344]
                                        bg-[#11151c]
                                        px-3 py-2.5
                                        text-xs text-white
                                        outline-none
                                        placeholder:text-[#687282]

                                        focus:border-[#558fda]
                                    " />
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="
                            mt-6 space-y-4
                            border-t border-[#222936]
                            pt-5
                        ">
                        <legend class="
                                px-1 text-[10px]
                                font-extrabold uppercase
                                tracking-[0.12em]
                                text-[#778190]
                            ">
                            Disponibilidade
                        </legend>

                        <label class="
                                flex cursor-pointer
                                items-center gap-3
                            ">
                            <input v-model="form.featured" type="checkbox" class="
                                    h-4 w-4
                                    accent-[#5ca5f7]
                                " />

                            <span class="text-xs text-[#aeb6c4]">
                                Em destaque
                            </span>
                        </label>

                        <label class="
                                flex cursor-pointer
                                items-center gap-3
                            ">
                            <input v-model="form.on_sale" type="checkbox" class="
                                    h-4 w-4
                                    accent-[#5ca5f7]
                                " />

                            <span class="text-xs text-[#aeb6c4]">
                                Em oferta
                            </span>
                        </label>

                        <label class="
                                flex cursor-pointer
                                items-center gap-3
                            ">
                            <input v-model="form.in_stock" type="checkbox" class="
                                    h-4 w-4
                                    accent-[#5ca5f7]
                                " />

                            <span class="text-xs text-[#aeb6c4]">
                                Em estoque
                            </span>
                        </label>
                    </fieldset>

                    <button type="button" class="
                            mt-7 w-full rounded-[9px]
                            border border-[#293344]
                            bg-[#151b25]
                            px-4 py-[11px]
                            text-xs font-bold
                            text-[#ecf3ff]
                            transition-colors

                            hover:border-[#4c87db]
                            hover:bg-[#4c87db]
                            hover:text-[#07101e]
                        " @click="applyFilters">
                        Aplicar filtros
                    </button>
                </aside>

                <section>
                    <div v-if="products.data.length" class="
                            grid grid-cols-3 gap-[18px]

                            max-[1100px]:grid-cols-2
                            max-[640px]:grid-cols-1
                        ">
                        <ProductCard v-for="product in products.data" :key="product.id" :product="product" />
                    </div>

                    <div v-else class="
                            rounded-2xl border
                            border-dashed border-[#2b3340]
                            px-6 py-[70px]
                            text-center text-[#808b9d]
                        ">
                        <svg class="mx-auto h-[35px] w-[35px]" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.8" aria-hidden="true">
                            <circle cx="11" cy="11" r="7" />

                            <path d="m20 20-4-4" />
                        </svg>

                        <h2 class="
                                mt-4 text-lg font-bold
                                text-[#e9edf3]
                            ">
                            Nenhum produto encontrado
                        </h2>

                        <p class="mt-2 text-xs">
                            Tente alterar ou remover alguns filtros.
                        </p>

                        <button type="button" class="
                                mt-6 rounded-[9px]
                                bg-gradient-to-br
                                from-[#4a99ed] to-[#7d61ed]
                                px-5 py-3
                                text-xs font-bold text-white
                            " @click="clearFilters">
                            Limpar filtros
                        </button>
                    </div>

                    <nav v-if="products.last_page > 1" aria-label="Paginação" class="
                            mt-10 flex flex-wrap
                            justify-center gap-2
                        ">
                        <template v-for="link in products.links" :key="link.label">
                            <Link v-if="link.url" :href="link.url" preserve-scroll preserve-state class="
                                    flex min-h-10 min-w-10
                                    items-center justify-center
                                    rounded-[9px] border px-3
                                    text-xs font-bold
                                    transition-colors
                                " :class="link.active
                                        ? 'border-[#558fda] bg-[#15263e] text-[#dbeeff]'
                                        : 'border-[#293344] bg-[#11151c] text-[#929cac] hover:border-[#558fda] hover:text-white'
                                    ">
                                <span v-html="link.label" />
                            </Link>

                            <span v-else class="
                                    flex min-h-10 min-w-10
                                    cursor-not-allowed
                                    items-center justify-center
                                    rounded-[9px]
                                    border border-[#222936]
                                    bg-[#0d1117] px-3
                                    text-xs text-[#4f5866]
                                " v-html="link.label" />
                        </template>
                    </nav>
                </section>
            </div>
        </section>
    </StoreLayout>
</template>
