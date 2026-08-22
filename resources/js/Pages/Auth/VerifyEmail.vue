<script setup lang="ts">
import AuthLayout from '@/Layouts/AuthLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps<{
    status?: string;
}>();

const form = useForm({});

function resend(): void {
    form.post('/email/verification-notification');
}
</script>

<template>
    <Head title="Verificar e-mail" />

    <AuthLayout
        title="Verifique seu e-mail"
        description="Enviamos um link de confirmação para o endereço cadastrado."
    >
        <div
            v-if="status === 'verification-link-sent'"
            class="
                mb-6 rounded-xl border border-emerald-500/30
                bg-emerald-500/10 p-4 text-sm text-emerald-300
            "
        >
            Um novo link de verificação foi enviado.
        </div>

        <p class="text-sm leading-7 text-slate-400">
            Antes de continuar, abra seu e-mail e clique no link
            de confirmação. Caso não tenha recebido, solicite outro.
        </p>

        <button
            type="button"
            :disabled="form.processing"
            class="
                mt-6 w-full rounded-xl bg-gradient-to-r
                from-blue-500 to-violet-500 px-5 py-3
                font-semibold disabled:opacity-60
            "
            @click="resend"
        >
            {{
                form.processing
                    ? 'Enviando...'
                    : 'Reenviar e-mail'
            }}
        </button>

        <Link
            href="/logout"
            method="post"
            as="button"
            class="
                mt-5 w-full text-center text-sm
                font-medium text-slate-400 hover:text-white
            "
        >
            Sair da conta
        </Link>
    </AuthLayout>
</template>
