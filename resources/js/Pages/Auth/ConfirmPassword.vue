<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    password: '',
});

function submit(): void {
    form.post('/user/confirm-password', {
        onFinish: () => form.reset(),
    });
}
</script>

<template>
    <Head title="Confirmar senha" />

    <AuthLayout
        title="Confirme sua senha"
        description="Esta é uma área protegida. Confirme sua senha para continuar."
    >
        <form
            class="space-y-5"
            @submit.prevent="submit"
        >
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
                    autocomplete="current-password"
                    required
                    autofocus
                    class="
                        w-full rounded-xl border border-slate-700
                        bg-slate-900 px-4 py-3 text-white outline-none
                        focus:border-blue-500 focus:ring-2
                        focus:ring-blue-500/20
                    "
                >

                <InputError :message="form.errors.password" />
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
                Confirmar senha
            </button>
        </form>
    </AuthLayout>
</template>
