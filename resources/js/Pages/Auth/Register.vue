<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

function submit(): void {
    form.post('/register', {
        onFinish: () => {
            form.reset('password', 'password_confirmation');
        },
    });
}
</script>

<template>
    <Head title="Criar conta" />

    <AuthLayout
        title="Crie sua conta"
        description="Cadastre-se para comprar e acompanhar seus pedidos."
    >
        <form
            class="space-y-5"
            @submit.prevent="submit"
        >
            <div>
                <label
                    for="name"
                    class="mb-2 block text-sm font-medium text-slate-300"
                >
                    Nome completo
                </label>

                <input
                    id="name"
                    v-model="form.name"
                    type="text"
                    autocomplete="name"
                    required
                    autofocus
                    class="
                        w-full rounded-xl border border-slate-700
                        bg-slate-900 px-4 py-3 text-white outline-none
                        transition focus:border-blue-500 focus:ring-2
                        focus:ring-blue-500/20
                    "
                >

                <InputError :message="form.errors.name" />
            </div>

            <div>
                <label
                    for="email"
                    class="mb-2 block text-sm font-medium text-slate-300"
                >
                    E-mail
                </label>

                <input
                    id="email"
                    v-model="form.email"
                    type="email"
                    autocomplete="email"
                    required
                    class="
                        w-full rounded-xl border border-slate-700
                        bg-slate-900 px-4 py-3 text-white outline-none
                        transition focus:border-blue-500 focus:ring-2
                        focus:ring-blue-500/20
                    "
                >

                <InputError :message="form.errors.email" />
            </div>

            <div>
                <label
                    for="password"
                    class="mb-2 block text-sm font-medium text-slate-300"
                >
                    Senha
                </label>

                <input
                    id="password"
                    v-model="form.password"
                    type="password"
                    autocomplete="new-password"
                    required
                    class="
                        w-full rounded-xl border border-slate-700
                        bg-slate-900 px-4 py-3 text-white outline-none
                        transition focus:border-blue-500 focus:ring-2
                        focus:ring-blue-500/20
                    "
                >

                <InputError :message="form.errors.password" />
            </div>

            <div>
                <label
                    for="password_confirmation"
                    class="mb-2 block text-sm font-medium text-slate-300"
                >
                    Confirme a senha
                </label>

                <input
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    autocomplete="new-password"
                    required
                    class="
                        w-full rounded-xl border border-slate-700
                        bg-slate-900 px-4 py-3 text-white outline-none
                        transition focus:border-blue-500 focus:ring-2
                        focus:ring-blue-500/20
                    "
                >
            </div>

            <button
                type="submit"
                :disabled="form.processing"
                class="
                    w-full rounded-xl bg-gradient-to-r
                    from-blue-500 to-violet-500 px-5 py-3
                    font-semibold text-white transition
                    hover:brightness-110 disabled:cursor-not-allowed
                    disabled:opacity-60
                "
            >
                {{ form.processing ? 'Criando conta...' : 'Criar conta' }}
            </button>
        </form>

        <p class="mt-8 text-center text-sm text-slate-400">
            Já possui uma conta?

            <Link
                href="/login"
                class="font-semibold text-blue-400 hover:text-blue-300"
            >
                Entrar
            </Link>
        </p>
    </AuthLayout>
</template>
