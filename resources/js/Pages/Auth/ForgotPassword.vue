<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps<{
    status?: string;
}>();

const form = useForm({
    email: '',
});

function submit(): void {
    form.post('/forgot-password');
}
</script>

<template>
    <Head title="Recuperar senha" />

    <AuthLayout
        title="Recupere sua senha"
        description="Enviaremos um link de redefinição para seu e-mail."
    >
        <div
            v-if="status"
            class="
                mb-6 rounded-xl border border-emerald-500/30
                bg-emerald-500/10 p-4 text-sm text-emerald-300
            "
        >
            {{ status }}
        </div>

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
                        focus:border-blue-500 focus:ring-2
                        focus:ring-blue-500/20
                    "
                >

                <InputError :message="form.errors.email" />
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
                        ? 'Enviando...'
                        : 'Enviar link de recuperação'
                }}
            </button>
        </form>

        <p class="mt-8 text-center text-sm">
            <Link
                href="/login"
                class="font-semibold text-blue-400"
            >
                Voltar para o login
            </Link>
        </p>
    </AuthLayout>
</template>
