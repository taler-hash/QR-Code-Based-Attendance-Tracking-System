<template>
    <Drawer v-model:visible="visible" header="Section" position="right">
        <div class="">
            <div class="flex flex-col gap-2">
                <label for="section">Section</label>
                <InputText id="section" v-model="data.section" aria-describedby="section-help" :readonly="true" />
            </div>
            <div class="flex flex-col gap-2">
                <label for="description">Description</label>
                <InputText id="description" v-model="data.description" aria-describedby="description-help" :readonly="true"/>
            </div>
            
            <div class="flex flex-col gap-2">
                <label for="time_in">Time in</label>
                <InputText type="text" id="time_out" :value="convertDateToTime(data.time_in)" aria-describedby="time_out-help" :readonly="true"/>
            </div>
            <div class="flex flex-col gap-2">
                <label for="time_out">Time out</label>
                <InputText type="text" id="time_out" :value="convertDateToTime(data.time_out)" aria-describedby="time_out-help" :readonly="true"/>
            </div>
            
        </div>
    </Drawer>
</template>
<script setup lang="ts">
import Drawer from 'primevue/drawer';
import InputText from 'primevue/inputtext';
import { ref, inject } from 'vue';
export type { FormTypes } from '@/Pages/Section/Types/types'

const convertDateToTime = inject('convertDateToTime')


const visible = ref(false)
const data = ref<FormTypes>({
    section: null,
    description: null,
    time_in: null,
    time_out: null
})

function open(props: { data: FormTypes }) {
    
    data.value = props.data

    visible.value = true
}

function close() {
    visible.value = false
}

defineExpose({
    open,
    close
})
</script>