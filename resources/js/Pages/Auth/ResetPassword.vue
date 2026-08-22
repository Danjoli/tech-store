<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    token: string;
    email: string;
}>();

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

function submit(): void {
    form.post('/reset-password', {
        onFinish: () => {
            form.reset('password', 'password_confirmation');
        },
    });
}
</script>

<template>
    <Head title="Redefinir senha" />

    <AuthLayout
        title="Crie uma nova senha"
        description="Escolha uma senha segura para acessar sua conta."
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
                    class="
                        w-full rounded-xl border border-slate-700
                        bg-slate-900 px-4 py-3 text-white outline-none
                        focus:border-blue-500
                    "
                >

                <InputError :message="form.errors.email" />
            </div>

            <div>
                <label
                    for="password"
                    class="mb-2 block text-sm font-medium text-slate-300"
                >
                    Nova senha
                </label>

                <input
                    id="password"
                    v-model="form.password"
                    type="password"
                    autocomplete="new-password"
                    required
                    autofocus
                    class="
                        w-full rounded-xl border border-slate-700
                        bg-slate-900 px-4 py-3 text-white outline-none
                        focus:border-blue-500
                    "
                >

                <InputError :message="form.errors.password" />
            </div>

            <div>
                <label
                    for="password_confirmation"
                    class="mb-2 block text-sm font-medium text-slate-300"
                >
                    Confirme a nova senha
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
                        focus:border-blue-500
                    "
                >
            </div>

            <button
                type="submit"
                :disabled="form.processing"
                class="
                    w-full rounded-xl bg-gradient-to-r
                    from-blue-500 to-violet-500 px-5 py-3
                    font-semibold disabled:opacity-60
                "
            >
                {{
                    form.processing
                        ? 'Redefinindo...'
                        : 'Redefinir senha'
                }}
            </button>
        </form>
    </AuthLayout>
</template>
