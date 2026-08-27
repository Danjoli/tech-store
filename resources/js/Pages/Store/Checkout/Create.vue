<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import StoreLayout from '@/Layouts/StoreLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

interface Address { recipient_name: string; phone: string; zip: string; street: string; number: string; complement: string | null; district: string; city: string; state: string; }
interface Method { value: string; label: string; }
const props = defineProps<{ address: Address | null; paymentMethods: Method[]; totalAmount: number; sandbox: boolean }>();
const form = useForm({ recipient_name: props.address?.recipient_name ?? '', phone: props.address?.phone ?? '', zip: props.address?.zip ?? '', street: props.address?.street ?? '', number: props.address?.number ?? '', complement: props.address?.complement ?? '', district: props.address?.district ?? '', city: props.address?.city ?? '', state: props.address?.state ?? '', payment_method: 'pix' });
const currency = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' });
function submit(): void { form.post('/checkout'); }
</script>

<template>
    <Head title="Checkout" />
    <StoreLayout>
        <section class="mx-auto w-[min(1100px,calc(100%_-_40px))] py-12 max-[640px]:w-[calc(100%_-_32px)] max-[640px]:py-8">
            <p class="text-[10px] font-extrabold uppercase tracking-[0.16em] text-[#6eaef6]">Finalizar pedido</p>
            <h1 class="mt-3 text-[clamp(32px,4vw,48px)] font-normal tracking-[-0.05em] text-[#f7f9fc]">Checkout</h1>
            <div v-if="sandbox" class="mt-5 rounded-xl border border-[#40577a] bg-[#101b2a] px-4 py-3 text-sm text-[#b9d7fb]">Pagamentos em modo sandbox: nenhum valor real será cobrado.</div>
            <form class="mt-8 grid gap-6 lg:grid-cols-[1fr_320px]" @submit.prevent="submit">
                <div class="space-y-5 rounded-2xl border border-[#222936] bg-[#0d1117] p-6 max-[640px]:p-5">
                    <div><h2 class="text-lg font-bold text-[#f7f9fc]">Entrega</h2><p class="mt-1 text-sm text-[#8b95a5]">Informe o endereço para este pedido.</p></div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2"><label class="mb-2 block text-sm font-semibold text-[#c7cfdb]">Destinatário</label><input v-model="form.recipient_name" required class="w-full rounded-xl border border-[#293344] bg-[#11151c] px-4 py-3 text-white outline-none focus:border-[#5ca5f7]"><InputError :message="form.errors.recipient_name" /></div>
                        <div><label class="mb-2 block text-sm font-semibold text-[#c7cfdb]">Telefone</label><input v-model="form.phone" required class="w-full rounded-xl border border-[#293344] bg-[#11151c] px-4 py-3 text-white outline-none focus:border-[#5ca5f7]"><InputError :message="form.errors.phone" /></div>
                        <div><label class="mb-2 block text-sm font-semibold text-[#c7cfdb]">CEP</label><input v-model="form.zip" required class="w-full rounded-xl border border-[#293344] bg-[#11151c] px-4 py-3 text-white outline-none focus:border-[#5ca5f7]"><InputError :message="form.errors.zip" /></div>
                        <div class="sm:col-span-2"><label class="mb-2 block text-sm font-semibold text-[#c7cfdb]">Rua</label><input v-model="form.street" required class="w-full rounded-xl border border-[#293344] bg-[#11151c] px-4 py-3 text-white outline-none focus:border-[#5ca5f7]"><InputError :message="form.errors.street" /></div>
                        <div><label class="mb-2 block text-sm font-semibold text-[#c7cfdb]">Número</label><input v-model="form.number" required class="w-full rounded-xl border border-[#293344] bg-[#11151c] px-4 py-3 text-white outline-none focus:border-[#5ca5f7]"></div><div><label class="mb-2 block text-sm font-semibold text-[#c7cfdb]">Complemento</label><input v-model="form.complement" class="w-full rounded-xl border border-[#293344] bg-[#11151c] px-4 py-3 text-white outline-none focus:border-[#5ca5f7]"></div>
                        <div><label class="mb-2 block text-sm font-semibold text-[#c7cfdb]">Bairro</label><input v-model="form.district" required class="w-full rounded-xl border border-[#293344] bg-[#11151c] px-4 py-3 text-white outline-none focus:border-[#5ca5f7]"></div><div><label class="mb-2 block text-sm font-semibold text-[#c7cfdb]">Cidade</label><input v-model="form.city" required class="w-full rounded-xl border border-[#293344] bg-[#11151c] px-4 py-3 text-white outline-none focus:border-[#5ca5f7]"></div>
                        <div><label class="mb-2 block text-sm font-semibold text-[#c7cfdb]">UF</label><input v-model="form.state" maxlength="2" required class="w-full rounded-xl border border-[#293344] bg-[#11151c] px-4 py-3 uppercase text-white outline-none focus:border-[#5ca5f7]"><InputError :message="form.errors.state" /></div>
                    </div>
                    <div class="border-t border-[#222936] pt-6"><h2 class="text-lg font-bold text-[#f7f9fc]">Pagamento</h2><div class="mt-4 grid gap-3 sm:grid-cols-3"><label v-for="method in paymentMethods" :key="method.value" class="cursor-pointer rounded-xl border p-4 text-sm font-bold" :class="form.payment_method === method.value ? 'border-[#5ca5f7] bg-[#15263e] text-white' : 'border-[#293344] bg-[#11151c] text-[#aeb6c4]'"><input v-model="form.payment_method" :value="method.value" type="radio" class="sr-only">{{ method.label }}</label></div><InputError :message="form.errors.payment_method" /></div>
                </div>
                <aside class="h-fit rounded-2xl border border-[#293344] bg-[#11151c] p-6"><h2 class="text-lg font-bold text-[#f7f9fc]">Resumo</h2><div class="mt-6 flex justify-between border-t border-[#293344] pt-4"><span class="font-bold text-[#f7f9fc]">Total</span><strong class="text-xl text-[#f7f9fc]">{{ currency.format(totalAmount) }}</strong></div><button type="submit" :disabled="form.processing" class="mt-6 w-full rounded-xl bg-[#4a99ed] px-4 py-3 text-sm font-bold text-white transition hover:bg-[#5ca5f7] disabled:opacity-60">Criar pedido</button><Link href="/carrinho" class="mt-4 block text-center text-sm font-bold text-[#6eaef6] hover:text-white">Voltar ao carrinho</Link></aside>
            </form>
        </section>
    </StoreLayout>
</template>
