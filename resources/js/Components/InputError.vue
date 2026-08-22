<script setup lang="ts">
defineProps<{
    message?: string;
}>();
</script>

<template>
    <p
        v-if="message"
        class="mt-2 text-sm text-red-400"
    >
        {{ message }}
    </p>
</template>
