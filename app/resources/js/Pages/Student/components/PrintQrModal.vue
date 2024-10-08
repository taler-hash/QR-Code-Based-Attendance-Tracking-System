<template>
    <Dialog v-model:visible="visible" modal header="Print QR" class="w-56">
        <form @submit.prevent="submit">
            <div class="flex flex-col pt-4">
                <div class="">
                    <div class="flex flex-col gap-2">
                        <label for="section">Sections</label>
                        <MultiSelect id="section" v-model="sections" :options="sectionOptions" optionLabel="section"
                            optionValue="id" />
                    </div>
                    
                </div>
                <div class="flex flex-col pt-4">
                    <Button type="submit" :disabled="!sections.length">Submit</Button>
                </div>
            </div>
        </form>
    </Dialog>
    <ToPrintModal ref="tm" />
</template>
<script setup lang="ts">
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import MultiSelect from 'primevue/multiselect';
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';
import ToPrintModal from './ToPrintModal.vue';

const visible = ref<boolean>(false)
const form = useForm<{ sections: [] }>({
    sections: []
})
const sections = ref([])
const sectionOptions = ref<{ id: number, section: string }[]>([])
const tm = ref()

async function open() {
    visible.value = true

    await getSections()
}

function submit() {
    tm.value.open(sections.value, 'section')
}

async function getSections() {
    const sections = await axios.get(route('sections.get'))

    sectionOptions.value = sections.data
}

defineExpose({
    open
})

</script>