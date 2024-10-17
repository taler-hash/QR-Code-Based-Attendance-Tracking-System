<template>
    <Dialog v-model:visible="visible" modal header="Update Section" :style="{ width: '25rem' }">
        <form @submit.prevent="submit">
            <div class="flex flex-col gap-2">
                <label for="name">Name</label>
                <div class="flex space-x-2 w-full">
                    <InputText id="name" v-model="form.first_name" aria-describedby="name-help" required placeholder="First Name" class="w-[8.5rem]"/>
                    <InputText v-model="form.last_name" aria-describedby="name-help" required placeholder="Last Name" />
                </div>
                <InputError :message="form.errors.first_name" />
                <InputError :message="form.errors.last_name" />
            </div>
            <div class="flex flex-col gap-2">
                <label for="section">Section</label>
                <Select id="section" 
                    v-model="form.sections" 
                    :options="sectionOptions" 
                    optionLabel="section" 
                    optionValue="id" 
                    aria-describedby="section-help" 
                    placeholder=""
                    required />
                <InputError :message="form.errors.sections" />
            </div>
            <div class="flex flex-col pt-4">
                <Button type="submit">Submit</Button>
            </div>
        </form>
    </Dialog>
    <Toast />
</template>
<script setup lang="ts">
import Dialog from 'primevue/dialog';
import InputError from '@/Components/InputError.vue';
import InputText from 'primevue/inputtext';
import { ref, defineExpose } from 'vue'
import { useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Toast from 'primevue/toast';
import Select from 'primevue/select';
import { useToast } from "primevue/usetoast";
import { router } from '@inertiajs/vue3';
import type { FormTypes } from '@/Pages/Student/Types/types';
import type { FormTypes as SectionTypes } from '@/Pages/Section/Types/types';
import axios from 'axios';
import _ from 'lodash';

const form = useForm<FormTypes>({
    name: '',
    first_name: '',
    last_name: '',
    sections: [],
    status: '',
});
const sectionOptions  =  ref<SectionTypes[]>()

const visible = ref(false)
const toast = useToast()

function open(props: { data: FormTypes}) {
    const _props = _.clone(props.data)

    _props.sections = _props.sections?.map(i => i?.id)[0] as any

    form.defaults(_props)
    form.reset()

    getSections()

    visible.value = true
}

async function getSections() {
    const sections = await axios.get(route('sections.get'))

    if(sections.status === 200) {
        sectionOptions.value = sections.data
    }
}

function close() {
    visible.value = false
}

function submit() {
    if (!form.isDirty) {
        toast.add({ severity: 'info', summary: 'Info', detail: 'No Changes were made', life: 3000 })

        return
    }

    form.patch(route('students.update'), {
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Success', detail: 'Updated Student Successfully', life: 3000 });
            close()
            form.reset()
            router.reload()

        },
        
    })
}

defineExpose({
    open
})
</script>