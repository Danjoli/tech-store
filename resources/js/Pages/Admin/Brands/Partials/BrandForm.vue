<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3';
import { onBeforeUnmount, ref } from 'vue';

interface BrandData {
    id: number;
    name: string;
    description: string | null;
    website_url: string | null;
    logo_url: string | null;
    is_active: boolean;
}

const props = defineProps<{
    brand?: BrandData;
}>();

const isEditing = Boolean(props.brand);

const form = useForm({
    name: props.brand?.name ?? '',
    description: props.brand?.description ?? '',
    website_url: props.brand?.website_url ?? '',
    logo: null as File | null,
    remove_logo: false,
    is_active: props.brand?.is_active ?? true,
    _method: isEditing ? 'put' : 'post',
});

const logoInput = ref<HTMLInputElement | null>(null);
const logoPreview = ref<string | null>(props.brand?.logo_url ?? null);
let temporaryPreview: string | null = null;

function selectLogo(event: Event): void {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0] ?? null;

    releaseTemporaryPreview();

    form.logo = file;
    form.remove_logo = false;

    if (file) {
        temporaryPreview = URL.createObjectURL(file);
        logoPreview.value = temporaryPreview;
    } else {
        logoPreview.value = props.brand?.logo_url ?? null;
    }
}

function removeLogo(): void {
    releaseTemporaryPreview();

    form.logo = null;
    form.remove_logo = true;
    logoPreview.value = null;

    if (logoInput.value) {
        logoInput.value.value = '';
    }
}

function releaseTemporaryPreview(): void {
    if (temporaryPreview) {
        URL.revokeObjectURL(temporaryPreview);
        temporaryPreview = null;
    }
}

function submit(): void {
    const url = isEditing
        ? `/admin/brands/${props.brand?.id}`
        : '/admin/brands';

    form.post(url, {
        forceFormData: true,
        preserveScroll: true,
    });
}

onBeforeUnmount(releaseTemporaryPreview);
</script>

<template>
    <form
        class="space-y-6"
        enctype="multipart/form-data"
        @submit.prevent="submit"
    >
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="grid gap-6 lg:grid-cols-2">
                <div class="lg:col-span-2">
                    <label
                        for="name"
                        class="mb-2 block text-sm font-semibold text-slate-700"
                    >
                        Nome da marca
                    </label>

                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        maxlength="255"
                        autocomplete="off"
                        class="w-full rounded-lg border px-4 py-3 outline-none transition focus:ring-2"
                        :class="
                            form.errors.name
                                ? 'border-red-400 focus:border-red-500 focus:ring-red-100'
                                : 'border-slate-300 focus:border-blue-500 focus:ring-blue-100'
                        "
                    />

                    <p
                        v-if="form.errors.name"
                        class="mt-2 text-sm text-red-600"
                    >
                        {{ form.errors.name }}
                    </p>
                </div>

                <div class="lg:col-span-2">
                    <label
                        for="description"
                        class="mb-2 block text-sm font-semibold text-slate-700"
                    >
                        Descrição
                    </label>

                    <textarea
                        id="description"
                        v-model="form.description"
                        rows="5"
                        maxlength="5000"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    />

                    <p
                        v-if="form.errors.description"
                        class="mt-2 text-sm text-red-600"
                    >
                        {{ form.errors.description }}
                    </p>
                </div>

                <div>
                    <label
                        for="website_url"
                        class="mb-2 block text-sm font-semibold text-slate-700"
                    >
                        Site oficial
                    </label>

                    <input
                        id="website_url"
                        v-model="form.website_url"
                        type="url"
                        placeholder="https://exemplo.com"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    />

                    <p
                        v-if="form.errors.website_url"
                        class="mt-2 text-sm text-red-600"
                    >
                        {{ form.errors.website_url }}
                    </p>
                </div>

                <div>
                    <label
                        for="logo"
                        class="mb-2 block text-sm font-semibold text-slate-700"
                    >
                        Logo
                    </label>

                    <input
                        id="logo"
                        ref="logoInput"
                        type="file"
                        accept=".jpg,.jpeg,.png,.webp"
                        class="block w-full rounded-lg border border-slate-300 bg-white text-sm text-slate-600 file:mr-4 file:border-0 file:bg-slate-100 file:px-4 file:py-3 file:font-semibold file:text-slate-700"
                        @change="selectLogo"
                    />

                    <p class="mt-2 text-xs text-slate-500">
                        JPG, PNG ou WebP, com no máximo 2 MB.
                    </p>

                    <p
                        v-if="form.errors.logo"
                        class="mt-2 text-sm text-red-600"
                    >
                        {{ form.errors.logo }}
                    </p>
                </div>

                <div v-if="logoPreview" class="lg:col-span-2">
                    <p class="mb-2 text-sm font-semibold text-slate-700">
                        Pré-visualização
                    </p>

                    <div class="flex items-center gap-4">
                        <img
                            :src="logoPreview"
                            alt="Pré-visualização do logo"
                            class="h-24 w-24 rounded-xl border border-slate-200 object-contain p-2"
                        />

                        <button
                            type="button"
                            class="text-sm font-bold text-red-600 hover:text-red-800"
                            @click="removeLogo"
                        >
                            Remover logo
                        </button>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <label class="flex cursor-pointer items-center gap-3">
                        <input
                            v-model="form.is_active"
                            type="checkbox"
                            class="h-5 w-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                        />

                        <span>
                            <span class="block text-sm font-semibold text-slate-700">
                                Marca ativa
                            </span>

                            <span class="block text-sm text-slate-500">
                                Marcas inativas não serão exibidas na loja.
                            </span>
                        </span>
                    </label>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <Link
                href="/admin/brands"
                class="rounded-lg border border-slate-300 px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-100"
            >
                Cancelar
            </Link>

            <button
                type="submit"
                :disabled="form.processing"
                class="rounded-lg bg-blue-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
            >
                {{
                    form.processing
                        ? 'Salvando...'
                        : isEditing
                          ? 'Salvar alterações'
                          : 'Cadastrar marca'
                }}
            </button>
        </div>
    </form>
</template>
