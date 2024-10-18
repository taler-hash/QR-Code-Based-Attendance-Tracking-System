<template>
    <div class="shrink p-2 border rounded">
        <p class="font-medium pb-2">Details</p>
        <div class="block sm:flex">
            <div class="flex-shrink">
                <div class="flex flex-col gap-2">
                    <label for="description">Name</label>
                    <InputText id="description" v-model="data.name" aria-describedby="description-help"
                        :readonly="true" />
                </div>
                <div class="flex flex-col gap-2">
                    <label for="description">Section</label>
                    <div class="flex space-x-1">
                        <Button class="px-1 py-1" v-for="section in data.sections " value="8" @click="rsm.open(section.id)"
                            severity="secondary">
                            {{ section?.section }}
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup lang="ts">
import Button from 'primevue/button';
import { Link } from '@inertiajs/vue3';
import InputText from 'primevue/inputtext';
import { inject, onMounted, ref } from 'vue';
import type { FormTypes } from '@/Pages/Student/Types/types';
import axios from 'axios';

const id = inject<any>('id')
const rsm = inject<any>('rsm')

const data = ref<FormTypes>({
    name: '',
    username: '',
    password: '',
    first_name: '',
    last_name: '',
    sections: [],
    status: ''
})

onMounted(() => {
    getStudent()
})

async function getStudent() {
    const res = await axios.get(route('students.get'), {params: {id: id.value}})

    data.value = res.data
}

</script>