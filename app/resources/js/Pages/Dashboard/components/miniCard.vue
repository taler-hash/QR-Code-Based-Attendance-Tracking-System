<template>
    <div class="border p-4 rounded-xl shadow">
        <div class="flex justify-between items-end">
            <p class="font-medium">{{ props.header }}</p>
            <span class="pi text-2xl" :class="props.icon"></span>
        </div>
        <p class="font-bold text-6xl">{{ props.value }}</p>
    </div>
</template>
<script setup lang="ts">

const props = defineProps(['icon', 'header', 'value'])
</script>