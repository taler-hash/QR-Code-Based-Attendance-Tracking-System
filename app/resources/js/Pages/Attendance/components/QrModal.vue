<template>
    <Dialog header="Scan QR" v-model:visible="visible" modal @hide="onClose">
        <div v-if="!detected" class="">
            <qr-stream @decode="onDecode" class="mb">
                <div style="color: red;" class="frame"></div>
            </qr-stream>
        </div>
        <div v-else-if="form.processing" class="">
            {{ form.processing }}
        </div>
        <div v-else-if="form.errors.uuid" class="">
            QR Code invalid
        </div>
        <div v-else class="">

            Captured
        </div>
    </Dialog>
    <ConfirmDialog group="templating">
        <template #message="slotProps">
            <div>
                <div class="flex justify-center pb-4">
                    <i :class="slotProps.message.icon" class="!text-6xl text-primary-500"></i>
                </div>
                <p>{{ slotProps.message.message }}</p>
                <p>Name: <span class="font-bold">{{ student.name }}</span></p>
                <p>Section: <span class="font-bold">{{ student.sections[0].section }}</span></p>
            </div>
        </template>
    </ConfirmDialog>
</template>
<script setup lang="ts">
import Dialog from 'primevue/dialog';
import { inject, ref } from 'vue';
import { QrStream } from 'vue3-qr-reader';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { useConfirm } from "primevue/useconfirm";
import ConfirmDialog from 'primevue/confirmdialog';
import { useToast } from 'primevue/usetoast';
import _ from 'lodash';

const confirm = useConfirm()
const form = useForm<{ uuid: string, id: number}>({
    id: 0,
    uuid: ''
})
const toast = useToast()
const reloadTable = inject<any>('reloadTable')
const visible = ref<boolean>(false)
const detected = ref<boolean>(false)
const student = ref<any>({})

function open() {
    visible.value = true
}

function onClose() {
    detected.value = false
}

function onDecode(data: string) {
    submit(data)
    detected.value = true
}

async function submit(props: string) {

    try {
        const res = await axios.get(route('attendance.getStudent'), { params: { uuid: props } })
        student.value = res.data

        confirm.require({
            group: 'templating',
            header: 'Match?',
            message: 'Is this student currently match in the record?',
            icon: 'pi pi-exclamation-circle',
            rejectProps: {
                label: 'Cancel',
                class: 'bg-red-500',
            },
            acceptProps: {
                label: 'Confirm',
                class: 'bg-lime-500',
            },
            accept: () => {
                confirmed()
            }
        })
    } catch(err: any) {
        if(err.status === 422) {

            _.forEach(err.response.data.errors, (val: string[]) => {
                toast.add({ severity: 'error', summary: 'Error', detail: val[0], life: 6000 });
            })
        }
    }
}

function confirmed() {
    form.defaults('id', student.value.id)
    form.reset()
    form.put(route('attendance.create'), {
        onSuccess: () => {
            visible.value = false
            toast.add({ severity: 'success', summary: 'Success', detail: 'Added Record Successfully', life: 3000 });
            reloadTable()
        },
    })
}



defineExpose({
    open
})

</script>