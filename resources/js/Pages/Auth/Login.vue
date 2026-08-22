<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

function submit(): void {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
}
</script>

<template>
    <Head title="Entrar" />

    <AuthLayout
        title="Entre na sua conta"
        description="Acesse seus pedidos, favoritos e informações pessoais."
    >
        <form
            class="space-y-5"
            @submit.prevent="submit"
        >
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
                    autofocus
                    class="
                        w-full rounded-xl border border-slate-700
                        bg-slate-900 px-4 py-3 text-white outline-none
                        transition placeholder:text-slate-600
                        focus:border-blue-500 focus:ring-2
                        focus:ring-blue-500/20
                    "
                    placeholder="voce@email.com"
                >

                <InputError :message="form.errors.email" />
            </div>

            <div>
                <div class="mb-2 flex items-center justify-between">
                    <label
                        for="password"
                        class="text-sm font-medium text-slate-300"
                    >
                        Senha
                    </label>

                    <Link
                        href="/forgot-password"
                        class="
                            text-sm font-medium text-blue-400
                            hover:text-blue-300
                        "
                    >
                        Esqueci minha senha
                    </Link>
                </div>

                <input
                    id="password"
                    v-model="form.password"
                    type="password"
                    autocomplete="current-password"
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

            <label
                class="
                    flex cursor-pointer items-center
                    gap-3 text-sm text-slate-400
                "
            >
                <input
                    v-model="form.remember"
                    type="checkbox"
                    class="
                        size-4 rounded border-slate-600
                        bg-slate-900 text-blue-500
                    "
                >

                Manter conectado
            </label>

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
                {{ form.processing ? 'Entrando...' : 'Entrar' }}
            </button>
        </form>

        <p class="mt-8 text-center text-sm text-slate-400">
            Ainda não possui uma conta?

            <Link
                href="/register"
                class="font-semibold text-blue-400 hover:text-blue-300"
            >
                Criar conta
            </Link>
        </p>
    </AuthLayout>
</template>
