<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3';
import { onBeforeUnmount, ref } from 'vue';

interface ParentCategory {
    id: number;
    name: string;
}

interface CategoryData {
    id: number;
    parent_id: number | null;
    name: string;
    description: string | null;
    image_url: string | null;
    sort_order: number;
    is_active: boolean;
    has_children: boolean;
}

const props = defineProps<{
    category?: CategoryData;
    parentCategories: ParentCategory[];
}>();

const isEditing = Boolean(props.category);

const form = useForm({
    parent_id: props.category?.parent_id ?? null,
    name: props.category?.name ?? '',
    description: props.category?.description ?? '',
    image: null as File | null,
    remove_image: false,
    sort_order: props.category?.sort_order ?? 0,
    is_active: props.category?.is_active ?? true,
    _method: isEditing ? 'put' : 'post',
});

const imageInput = ref<HTMLInputElement | null>(null);
const imagePreview = ref<string | null>(
    props.category?.image_url ?? null,
);

let temporaryPreview: string | null = null;

function selectImage(event: Event): void {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0] ?? null;

    releaseTemporaryPreview();

    form.image = file;
    form.remove_image = false;

    if (file) {
        temporaryPreview = URL.createObjectURL(file);
        imagePreview.value = temporaryPreview;
    } else {
        imagePreview.value = props.category?.image_url ?? null;
    }
}

function removeImage(): void {
    releaseTemporaryPreview();

    form.image = null;
    form.remove_image = true;
    imagePreview.value = null;

    if (imageInput.value) {
        imageInput.value.value = '';
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
        ? `/admin/categories/${props.category?.id}`
        : '/admin/categories';

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
                <div>
                    <label
                        for="name"
                        class="mb-2 block text-sm font-semibold text-slate-700"
                    >
                        Nome da categoria
                    </label>

                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        maxlength="255"
                        autocomplete="off"
                        class="w-full rounded-lg border px-4 py-3 outline-none focus:ring-2"
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

                <div>
                    <label
                        for="parent_id"
                        class="mb-2 block text-sm font-semibold text-slate-700"
                    >
                        Categoria principal
                    </label>

                    <select
                        id="parent_id"
                        v-model="form.parent_id"
                        :disabled="category?.has_children"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 disabled:cursor-not-allowed disabled:bg-slate-100"
                    >
                        <option :value="null">
                            Nenhuma — categoria principal
                        </option>

                        <option
                            v-for="parent in parentCategories"
                            :key="parent.id"
                            :value="parent.id"
                        >
                            {{ parent.name }}
                        </option>
                    </select>

                    <p
                        v-if="category?.has_children"
                        class="mt-2 text-xs text-amber-700"
                    >
                        Esta categoria possui subcategorias e não pode
                        se tornar uma subcategoria.
                    </p>

                    <p
                        v-if="form.errors.parent_id"
                        class="mt-2 text-sm text-red-600"
                    >
                        {{ form.errors.parent_id }}
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
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
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
                        for="sort_order"
                        class="mb-2 block text-sm font-semibold text-slate-700"
                    >
                        Ordem de exibição
                    </label>

                    <input
                        id="sort_order"
                        v-model.number="form.sort_order"
                        type="number"
                        min="0"
                        max="65535"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    />

                    <p
                        v-if="form.errors.sort_order"
                        class="mt-2 text-sm text-red-600"
                    >
                        {{ form.errors.sort_order }}
                    </p>
                </div>

                <div>
                    <label
                        for="image"
                        class="mb-2 block text-sm font-semibold text-slate-700"
                    >
                        Imagem
                    </label>

                    <input
                        id="image"
                        ref="imageInput"
                        type="file"
                        accept=".jpg,.jpeg,.png,.webp"
                        class="block w-full rounded-lg border border-slate-300 bg-white text-sm text-slate-600 file:mr-4 file:border-0 file:bg-slate-100 file:px-4 file:py-3 file:font-semibold file:text-slate-700"
                        @change="selectImage"
                    />

                    <p class="mt-2 text-xs text-slate-500">
                        JPG, PNG ou WebP, com no máximo 2 MB.
                    </p>

                    <p
                        v-if="form.errors.image"
                        class="mt-2 text-sm text-red-600"
                    >
                        {{ form.errors.image }}
                    </p>
                </div>

                <div v-if="imagePreview" class="lg:col-span-2">
                    <p class="mb-2 text-sm font-semibold text-slate-700">
                        Pré-visualização
                    </p>

                    <div class="flex items-center gap-4">
                        <img
                            :src="imagePreview"
                            alt="Pré-visualização da categoria"
                            class="h-28 w-40 rounded-xl border border-slate-200 object-cover"
                        />

                        <button
                            type="button"
                            class="text-sm font-bold text-red-600 hover:text-red-800"
                            @click="removeImage"
                        >
                            Remover imagem
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
                                Categoria ativa
                            </span>

                            <span class="block text-sm text-slate-500">
                                Categorias inativas não serão exibidas na loja.
                            </span>
                        </span>
                    </label>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <Link
                href="/admin/categories"
                class="rounded-lg border border-slate-300 px-5 py-3 text-sm font-bold text-slate-700 hover:bg-slate-100"
            >
                Cancelar
            </Link>

            <button
                type="submit"
                :disabled="form.processing"
                class="rounded-lg bg-blue-600 px-5 py-3 text-sm font-bold text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
            >
                {{
                    form.processing
                        ? 'Salvando...'
                        : isEditing
                          ? 'Salvar alterações'
                          : 'Cadastrar categoria'
                }}
            </button>
        </div>
    </form>
</template>
