<template>
    <Dialog v-model:visible="visible" modal header="Update Section" :style="{ width: '25rem' }">
        <form @submit.prevent="submit">
            <div class="flex flex-col gap-2">
                <label for="section">Section</label>
                <InputText id="section" v-model="form.section" aria-describedby="section-help" required />
                <InputError :message="form.errors.section" />
            </div>
            <div class="flex flex-col gap-2">
                <label for="description">Description</label>
                <InputText id="description" v-model="form.description" aria-describedby="description-help" />
                <InputError :message="form.errors.description" />
            </div>
            <div class="flex flex-col gap-2">
                <label for="time_in">Time in</label>
                <DatePicker id="time_in" v-model="form.time_in" aria-describedby="time_in-help" required timeOnly
                    hourFormat="12" />
                <InputError :message="form.errors.time_in" />
            </div>
            <div class="flex flex-col gap-2">
                <label for="time_out">Time out</label>
                <DatePicker id="time_out" v-model="form.time_out" aria-describedby="time_out-help" required timeOnly
                    hourFormat="12" />
                <InputError :message="form.errors.time_out" />
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
import DatePicker from 'primevue/datepicker';
import Toast from 'primevue/toast';
import { useToast } from "primevue/usetoast";
import { router } from '@inertiajs/vue3';
import { FormTypes } from '@/Pages/Section/Types/types';
import _ from 'lodash';

const form = useForm<FormTypes>({
    section: null,
    description: null,
    time_in: null,
    time_out: null
});
const visible = ref(false)
const toast = useToast()

function open(props: any) {
    const _props = props.data

    _props.time_in = new Date(_props.time_in)
    _props.time_out = new Date(_props.time_out)

    form.defaults(_props)
    form.reset()

    visible.value = true
}

function close() {
    visible.value = false
}

function submit() {

    if (!form.isDirty) {
        toast.add({ severity: 'info', summary: 'Info', detail: 'No Changes were made', life: 3000 })

        return
    }

    form.patch(route('sections.update'), {
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Success', detail: 'Updated Section Successfully', life: 3000 });
            close()
            form.reset()
            router.reload()
        },
        onError: (err) => {
            console.log(err)
        }
    })
}

function checkIFHasChanges(oldProps: object, newProps: object) {

    return _.isEqual(oldProps, newProps)

}

defineExpose({
    open
})
</script>