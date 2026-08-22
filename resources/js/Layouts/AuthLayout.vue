<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineProps<{
    title: string;
    description: string;
}>();
</script>

<template>
    <main
        class="
            grid min-h-screen bg-slate-950 text-white
            lg:grid-cols-[1.1fr_0.9fr]
        "
    >
        <section
            class="
                relative hidden overflow-hidden border-r border-white/10
                bg-gradient-to-br from-blue-950 via-slate-950 to-violet-950
                p-12 lg:flex lg:flex-col lg:justify-between
            "
        >
            <Link
                href="/"
                class="text-2xl font-black tracking-tight"
            >
                <span class="text-blue-400">TECH</span>STORE
            </Link>

            <div class="max-w-xl">
                <p
                    class="
                        text-sm font-semibold uppercase
                        tracking-[0.25em] text-blue-400
                    "
                >
                    Performance sem limites
                </p>

                <h2
                    class="
                        mt-6 text-5xl font-bold
                        leading-tight tracking-tight
                    "
                >
                    Tecnologia para criar, jogar e ir além.
                </h2>

                <p class="mt-6 leading-7 text-slate-400">
                    Hardware de alta performance, periféricos
                    de precisão e uma experiência de compra segura.
                </p>
            </div>

            <p class="text-sm text-slate-500">
                © 2026 Tech Store
            </p>
        </section>

        <section
            class="
                flex items-center justify-center
                px-6 py-12 sm:px-10
            "
        >
            <div class="w-full max-w-md">
                <Link
                    href="/"
                    class="
                        mb-12 block text-center text-2xl
                        font-black tracking-tight lg:hidden
                    "
                >
                    <span class="text-blue-400">TECH</span>STORE
                </Link>

                <div>
                    <h1 class="text-3xl font-bold tracking-tight">
                        {{ title }}
                    </h1>

                    <p class="mt-3 text-sm leading-6 text-slate-400">
                        {{ description }}
                    </p>
                </div>

                <div class="mt-8">
                    <slot />
                </div>
            </div>
        </section>
    </main>
</template>
