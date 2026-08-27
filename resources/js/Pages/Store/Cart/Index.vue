<script setup lang="ts">
import StoreLayout from '@/Layouts/StoreLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

interface CartItem {
    id: number;
    quantity: number;
    product_name: string;
    product_slug: string;
    variant_name: string;
    available_stock: number;
    image_url: string | null;
    unit_price: number;
    subtotal: number;
}

const props = defineProps<{ items: CartItem[]; total_items: number; total_amount: number }>();
const quantities = ref(Object.fromEntries(props.items.map((item) => [item.id, item.quantity])) as Record<number, number>);

function formatCurrency(value: number): string {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value);
}

function updateItem(item: CartItem): void {
    router.put(`/carrinho/itens/${item.id}`, { quantity: quantities.value[item.id] }, { preserveScroll: true });
}

function removeItem(item: CartItem): void {
    router.delete(`/carrinho/itens/${item.id}`, { preserveScroll: true });
}
</script>

<template>
    <Head title="Carrinho" />
    <StoreLayout>
        <section class="mx-auto w-[min(1100px,calc(100%_-_40px))] py-12 max-[640px]:w-[calc(100%_-_32px)] max-[640px]:py-8">
            <p class="text-[10px] font-extrabold uppercase tracking-[0.16em] text-[#6eaef6]">Sua compra</p>
            <h1 class="mt-3 text-[clamp(32px,4vw,48px)] font-normal tracking-[-0.05em] text-[#f7f9fc]">Carrinho</h1>

            <div v-if="items.length" class="mt-9 grid gap-6 lg:grid-cols-[1fr_320px]">
                <div class="space-y-3">
                    <article v-for="item in items" :key="item.id" class="grid gap-4 rounded-2xl border border-[#222936] bg-[#0d1117] p-4 sm:grid-cols-[110px_1fr_auto] sm:items-center">
                        <Link :href="`/produtos/${item.product_slug}`" class="aspect-square overflow-hidden rounded-xl bg-[#11151c]">
                            <img v-if="item.image_url" :src="item.image_url" :alt="item.product_name" class="h-full w-full object-contain p-2">
                        </Link>
                        <div>
                            <Link :href="`/produtos/${item.product_slug}`" class="text-base font-bold text-[#f7f9fc] hover:text-[#6eaef6]">{{ item.product_name }}</Link>
                            <p class="mt-1 text-xs text-[#8b95a5]">Variação: {{ item.variant_name }}</p>
                            <p class="mt-3 text-sm font-bold text-[#f7f9fc]">{{ formatCurrency(item.unit_price) }}</p>
                            <p class="mt-1 text-xs text-[#8b95a5]">{{ item.available_stock }} unidade(s) disponível(is)</p>
                        </div>
                        <div class="flex items-center gap-3 sm:flex-col sm:items-end">
                            <div class="flex h-10 items-center rounded-xl border border-[#293344] bg-[#11151c]">
                                <button type="button" class="grid h-full w-9 place-items-center text-[#aeb6c4] hover:text-white" @click="quantities[item.id] = Math.max(1, quantities[item.id] - 1)">−</button>
                                <input v-model.number="quantities[item.id]" type="number" min="1" :max="Math.min(10, item.available_stock)" class="w-9 bg-transparent text-center text-sm font-bold text-white outline-none">
                                <button type="button" class="grid h-full w-9 place-items-center text-[#aeb6c4] hover:text-white" @click="quantities[item.id] = Math.min(Math.min(10, item.available_stock), quantities[item.id] + 1)">+</button>
                            </div>
                            <button type="button" class="text-xs font-bold text-[#6eaef6] hover:text-white" @click="updateItem(item)">Atualizar</button>
                            <button type="button" class="text-xs font-bold text-[#ff9da8] hover:text-[#ffbdc4]" @click="removeItem(item)">Remover</button>
                        </div>
                    </article>
                </div>

                <aside class="h-fit rounded-2xl border border-[#293344] bg-[#11151c] p-6">
                    <h2 class="text-lg font-bold text-[#f7f9fc]">Resumo</h2>
                    <div class="mt-6 flex justify-between text-sm text-[#aeb6c4]"><span>Itens</span><span>{{ total_items }}</span></div>
                    <div class="mt-4 flex justify-between border-t border-[#293344] pt-4"><span class="font-bold text-[#f7f9fc]">Total</span><strong class="text-xl text-[#f7f9fc]">{{ formatCurrency(total_amount) }}</strong></div>
                    <p class="mt-5 rounded-xl border border-[#293344] bg-[#0d1117] px-4 py-3 text-xs leading-5 text-[#8b95a5]">O checkout e os meios de pagamento serão habilitados na próxima etapa.</p>
                    <Link href="/produtos" class="mt-5 block text-center text-sm font-bold text-[#6eaef6] hover:text-white">Continuar comprando</Link>
                </aside>
            </div>

            <div v-else class="mt-9 rounded-2xl border border-dashed border-[#293344] bg-[#0d1117] px-6 py-16 text-center">
                <h2 class="text-xl font-bold text-[#f7f9fc]">Seu carrinho está vazio</h2>
                <p class="mt-2 text-sm text-[#8b95a5]">Explore o catálogo e adicione os produtos que quiser acompanhar.</p>
                <Link href="/produtos" class="mt-6 inline-flex rounded-xl bg-[#4a99ed] px-4 py-2.5 text-sm font-bold text-white transition hover:bg-[#5ca5f7]">Ver produtos</Link>
            </div>
        </section>
    </StoreLayout>
</template>
