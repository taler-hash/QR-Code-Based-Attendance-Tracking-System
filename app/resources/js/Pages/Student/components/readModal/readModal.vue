<template>
    <Dialog v-model:visible="visible" modal header="View Student" class="w-[calc(100%-2rem)]">
        <div class="lg:flex">
            <Details />
            <Attendance />
        </div>
    </Dialog>
</template>
<script setup lang="ts">
import Dialog from 'primevue/dialog';
import { provide, ref } from 'vue';
import Details from './details.vue';
import Attendance from './attendance.vue';

const visible = ref(false)
const id = ref<number>()

function open(_id: number) {
    id.value = _id
    visible.value = true
}

function close() {
    visible.value = false
}

provide('id', id)

defineExpose({
    open,
    close
})
</script>