<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import StoreLayout from '@/Layouts/StoreLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

interface Profile {
    name: string;
    email: string;
    phone: string | null;
    email_verified_at: string | null;
    favorites_count: number;
}

const props = defineProps<{ profile: Profile }>();

const profileForm = useForm({
    name: props.profile.name,
    email: props.profile.email,
    phone: props.profile.phone ?? '',
});

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

function updateProfile(): void {
    profileForm.put('/perfil', { preserveScroll: true });
}

function updatePassword(): void {
    passwordForm.put('/user/password', {
        preserveScroll: true,
        onSuccess: () => passwordForm.reset(),
    });
}
</script>

<template>
    <Head title="Meu perfil" />

    <StoreLayout>
        <section class="mx-auto w-[min(1000px,calc(100%_-_40px))] py-12 max-[640px]:w-[calc(100%_-_32px)] max-[640px]:py-8">
            <p class="text-[10px] font-extrabold uppercase tracking-[0.16em] text-[#6eaef6]">Minha conta</p>
            <h1 class="mt-3 text-[clamp(32px,4vw,48px)] font-normal tracking-[-0.05em] text-[#f7f9fc]">Meu perfil</h1>
            <p class="mt-2 text-sm text-[#8b95a5]">Mantenha seus dados e a segurança da sua conta atualizados.</p>

            <div class="mt-9 grid gap-5 lg:grid-cols-[1fr_280px]">
                <div class="space-y-5">
                    <form class="rounded-2xl border border-[#222936] bg-[#0d1117] p-6 max-[640px]:p-5" @submit.prevent="updateProfile">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <h2 class="text-lg font-bold text-[#f7f9fc]">Dados pessoais</h2>
                                <p class="mt-1 text-sm text-[#8b95a5]">Informações usadas para identificar sua conta.</p>
                            </div>
                            <span v-if="profile.email_verified_at" class="rounded-full bg-[#143124] px-3 py-1 text-xs font-bold text-[#8ee7bd]">E-mail verificado</span>
                        </div>

                        <div class="mt-6 grid gap-5 sm:grid-cols-2">
                            <div>
                                <label for="name" class="mb-2 block text-sm font-semibold text-[#c7cfdb]">Nome</label>
                                <input id="name" v-model="profileForm.name" type="text" autocomplete="name" required class="w-full rounded-xl border border-[#293344] bg-[#11151c] px-4 py-3 text-sm text-white outline-none transition placeholder:text-[#687282] focus:border-[#5ca5f7] focus:ring-2 focus:ring-[#5ca5f7]/20">
                                <InputError :message="profileForm.errors.name" />
                            </div>
                            <div>
                                <label for="phone" class="mb-2 block text-sm font-semibold text-[#c7cfdb]">Telefone</label>
                                <input id="phone" v-model="profileForm.phone" type="tel" autocomplete="tel" placeholder="(11) 99999-9999" class="w-full rounded-xl border border-[#293344] bg-[#11151c] px-4 py-3 text-sm text-white outline-none transition placeholder:text-[#687282] focus:border-[#5ca5f7] focus:ring-2 focus:ring-[#5ca5f7]/20">
                                <InputError :message="profileForm.errors.phone" />
                            </div>
                            <div class="sm:col-span-2">
                                <label for="email" class="mb-2 block text-sm font-semibold text-[#c7cfdb]">E-mail</label>
                                <input id="email" v-model="profileForm.email" type="email" autocomplete="email" required class="w-full rounded-xl border border-[#293344] bg-[#11151c] px-4 py-3 text-sm text-white outline-none transition placeholder:text-[#687282] focus:border-[#5ca5f7] focus:ring-2 focus:ring-[#5ca5f7]/20">
                                <p class="mt-2 text-xs leading-5 text-[#778190]">Ao alterar o e-mail, será necessário confirmá-lo novamente.</p>
                                <InputError :message="profileForm.errors.email" />
                            </div>
                        </div>

                        <button type="submit" :disabled="profileForm.processing" class="mt-6 rounded-xl bg-[#4a99ed] px-4 py-2.5 text-sm font-bold text-white transition hover:bg-[#5ca5f7] disabled:opacity-60">Salvar alterações</button>
                    </form>

                    <form class="rounded-2xl border border-[#222936] bg-[#0d1117] p-6 max-[640px]:p-5" @submit.prevent="updatePassword">
                        <h2 class="text-lg font-bold text-[#f7f9fc]">Alterar senha</h2>
                        <p class="mt-1 text-sm text-[#8b95a5]">Use uma senha forte e não reutilize senhas antigas.</p>
                        <div class="mt-6 grid gap-5">
                            <div>
                                <label for="current-password" class="mb-2 block text-sm font-semibold text-[#c7cfdb]">Senha atual</label>
                                <input id="current-password" v-model="passwordForm.current_password" type="password" autocomplete="current-password" required class="w-full rounded-xl border border-[#293344] bg-[#11151c] px-4 py-3 text-sm text-white outline-none focus:border-[#5ca5f7] focus:ring-2 focus:ring-[#5ca5f7]/20">
                                <InputError :message="passwordForm.errors.current_password" />
                            </div>
                            <div class="grid gap-5 sm:grid-cols-2">
                                <div>
                                    <label for="password" class="mb-2 block text-sm font-semibold text-[#c7cfdb]">Nova senha</label>
                                    <input id="password" v-model="passwordForm.password" type="password" autocomplete="new-password" required class="w-full rounded-xl border border-[#293344] bg-[#11151c] px-4 py-3 text-sm text-white outline-none focus:border-[#5ca5f7] focus:ring-2 focus:ring-[#5ca5f7]/20">
                                    <InputError :message="passwordForm.errors.password" />
                                </div>
                                <div>
                                    <label for="password-confirmation" class="mb-2 block text-sm font-semibold text-[#c7cfdb]">Confirmar nova senha</label>
                                    <input id="password-confirmation" v-model="passwordForm.password_confirmation" type="password" autocomplete="new-password" required class="w-full rounded-xl border border-[#293344] bg-[#11151c] px-4 py-3 text-sm text-white outline-none focus:border-[#5ca5f7] focus:ring-2 focus:ring-[#5ca5f7]/20">
                                </div>
                            </div>
                        </div>
                        <button type="submit" :disabled="passwordForm.processing" class="mt-6 rounded-xl border border-[#3d4b60] bg-[#151b25] px-4 py-2.5 text-sm font-bold text-white transition hover:border-[#5ca5f7] hover:bg-[#1c2635] disabled:opacity-60">Atualizar senha</button>
                    </form>
                </div>

                <aside class="h-fit rounded-2xl border border-[#222936] bg-[#0d1117] p-6">
                    <p class="text-[10px] font-extrabold uppercase tracking-[0.16em] text-[#6eaef6]">Atalhos</p>
                    <Link href="/favoritos" class="mt-5 flex items-center justify-between rounded-xl border border-[#293344] bg-[#11151c] p-4 transition hover:border-[#5ca5f7]">
                        <span><strong class="block text-sm text-[#f7f9fc]">Favoritos</strong><small class="mt-1 block text-xs text-[#8b95a5]">Produtos salvos</small></span>
                        <b class="text-xl text-[#6eaef6]">{{ profile.favorites_count }}</b>
                    </Link>
                    <Link href="/logout" method="post" as="button" class="mt-4 w-full rounded-xl border border-[#4b2930] px-4 py-2.5 text-sm font-bold text-[#ff9da8] transition hover:border-[#d96c78] hover:bg-[#2b171b]">Sair da conta</Link>
                </aside>
            </div>
        </section>
    </StoreLayout>
</template>
