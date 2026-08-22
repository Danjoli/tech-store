<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { onBeforeUnmount, ref } from 'vue';

interface Product {
    id: number;
    name: string;
}

interface VariantOption {
    id: number;
    name: string;
}

interface ProductImage {
    id: number;
    url: string;
    alt_text: string | null;
    sort_order: number;
    is_primary: boolean;
    variant: {
        id: number;
        name: string;
    } | null;
}

interface EditableImage extends ProductImage {
    product_variant_id: number | null;
}

const props = defineProps<{
    product: Product;
    images: ProductImage[];
    variants: VariantOption[];
}>();

const editableImages = ref<EditableImage[]>(
    props.images.map((image) => ({
        ...image,
        product_variant_id: image.variant?.id ?? null,
    })),
);

const uploadForm = useForm({
    images: [] as File[],
});

const previews = ref<string[]>([]);

function selectImages(event: Event): void {
    const input = event.target as HTMLInputElement;

    releasePreviews();

    uploadForm.images = Array.from(input.files ?? []).slice(0, 10);
    previews.value = uploadForm.images.map((image) =>
        URL.createObjectURL(image),
    );
}

function uploadImages(): void {
    uploadForm.post(
        `/admin/products/${props.product.id}/images`,
        {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                uploadForm.reset();
                releasePreviews();
            },
        },
    );
}

function updateImage(image: EditableImage): void {
    router.put(
        `/admin/products/${props.product.id}/images/${image.id}`,
        {
            product_variant_id: image.product_variant_id,
            alt_text: image.alt_text,
            sort_order: image.sort_order,
            is_primary: image.is_primary,
        },
        {
            preserveScroll: true,
        },
    );
}

function deleteImage(image: EditableImage): void {
    if (! confirm('Deseja realmente excluir esta imagem?')) {
        return;
    }

    router.delete(
        `/admin/products/${props.product.id}/images/${image.id}`,
        {
            preserveScroll: true,
        },
    );
}

function releasePreviews(): void {
    previews.value.forEach((preview) => {
        URL.revokeObjectURL(preview);
    });

    previews.value = [];
}

onBeforeUnmount(releasePreviews);
</script>

<template>
    <Head :title="`Imagens de ${product.name}`" />

    <AdminLayout>
        <div class="mb-8">
            <Link
                :href="`/admin/products/${product.id}/edit`"
                class="text-sm font-bold text-blue-600 hover:text-blue-800"
            >
                ← Voltar para o produto
            </Link>

            <p class="mt-6 text-sm font-semibold text-blue-600">
                Catálogo
            </p>

            <h1 class="mt-1 text-3xl font-bold text-slate-900">
                Imagens do produto
            </h1>

            <p class="mt-2 text-slate-600">
                Gerencie as imagens de {{ product.name }}.
            </p>
        </div>

        <form
            class="mb-8 rounded-xl border border-slate-200 bg-white p-6 shadow-sm"
            enctype="multipart/form-data"
            @submit.prevent="uploadImages"
        >
            <div class="mb-5">
                <h2 class="text-lg font-bold text-slate-900">
                    Enviar imagens
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Selecione até 10 arquivos JPG, PNG ou WebP.
                </p>
            </div>

            <input
                type="file"
                accept=".jpg,.jpeg,.png,.webp"
                multiple
                class="block w-full rounded-lg border border-slate-300 bg-white text-sm text-slate-600 file:mr-4 file:border-0 file:bg-slate-100 file:px-4 file:py-3 file:font-semibold file:text-slate-700"
                @change="selectImages"
            />

            <p
                v-if="uploadForm.errors.images"
                class="mt-2 text-sm text-red-600"
            >
                {{ uploadForm.errors.images }}
            </p>

            <div
                v-if="previews.length"
                class="mt-5 grid grid-cols-2 gap-4 sm:grid-cols-4 lg:grid-cols-6"
            >
                <img
                    v-for="preview in previews"
                    :key="preview"
                    :src="preview"
                    alt="Pré-visualização"
                    class="aspect-square w-full rounded-lg border border-slate-200 object-cover"
                />
            </div>

            <div class="mt-6 flex justify-end">
                <button
                    type="submit"
                    :disabled="
                        uploadForm.processing
                        || uploadForm.images.length === 0
                    "
                    class="rounded-lg bg-blue-600 px-5 py-3 text-sm font-bold text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    {{
                        uploadForm.processing
                            ? 'Enviando...'
                            : 'Enviar imagens'
                    }}
                </button>
            </div>
        </form>

        <section>
            <div class="mb-5">
                <h2 class="text-xl font-bold text-slate-900">
                    Imagens cadastradas
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    {{ editableImages.length }} imagem(ns).
                </p>
            </div>

            <div
                v-if="editableImages.length"
                class="grid gap-6 md:grid-cols-2 xl:grid-cols-3"
            >
                <article
                    v-for="image in editableImages"
                    :key="image.id"
                    class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm"
                >
                    <div class="relative bg-slate-100">
                        <img
                            :src="image.url"
                            :alt="image.alt_text ?? product.name"
                            class="aspect-[4/3] w-full object-contain"
                        />

                        <span
                            v-if="image.is_primary"
                            class="absolute left-3 top-3 rounded-full bg-blue-600 px-3 py-1 text-xs font-bold text-white"
                        >
                            Principal
                        </span>
                    </div>

                    <div class="space-y-4 p-5">
                        <div>
                            <label
                                :for="`alt-${image.id}`"
                                class="mb-2 block text-sm font-semibold text-slate-700"
                            >
                                Texto alternativo
                            </label>

                            <input
                                :id="`alt-${image.id}`"
                                v-model="image.alt_text"
                                type="text"
                                maxlength="255"
                                class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm"
                            />
                        </div>

                        <div>
                            <label
                                :for="`variant-${image.id}`"
                                class="mb-2 block text-sm font-semibold text-slate-700"
                            >
                                Variante
                            </label>

                            <select
                                :id="`variant-${image.id}`"
                                v-model="image.product_variant_id"
                                class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm"
                            >
                                <option :value="null">
                                    Imagem geral do produto
                                </option>

                                <option
                                    v-for="variant in variants"
                                    :key="variant.id"
                                    :value="variant.id"
                                >
                                    {{ variant.name }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label
                                :for="`order-${image.id}`"
                                class="mb-2 block text-sm font-semibold text-slate-700"
                            >
                                Ordem
                            </label>

                            <input
                                :id="`order-${image.id}`"
                                v-model.number="image.sort_order"
                                type="number"
                                min="0"
                                max="65535"
                                class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm"
                            />
                        </div>

                        <label class="flex cursor-pointer items-center gap-3">
                            <input
                                v-model="image.is_primary"
                                type="checkbox"
                                class="h-5 w-5 rounded border-slate-300 text-blue-600"
                            />

                            <span class="text-sm font-semibold text-slate-700">
                                Imagem principal
                            </span>
                        </label>

                        <div
                            class="flex justify-end gap-3 border-t border-slate-100 pt-4"
                        >
                            <button
                                type="button"
                                class="text-sm font-bold text-red-600 hover:text-red-800"
                                @click="deleteImage(image)"
                            >
                                Excluir
                            </button>

                            <button
                                type="button"
                                class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-bold text-white hover:bg-slate-700"
                                @click="updateImage(image)"
                            >
                                Salvar
                            </button>
                        </div>
                    </div>
                </article>
            </div>

            <div
                v-else
                class="rounded-xl border border-slate-200 bg-white px-6 py-14 text-center text-sm text-slate-500 shadow-sm"
            >
                Nenhuma imagem cadastrada para este produto.
            </div>
        </section>
    </AdminLayout>
</template>
