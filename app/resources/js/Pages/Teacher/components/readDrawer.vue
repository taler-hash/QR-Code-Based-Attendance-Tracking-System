<template>
    <Drawer v-model:visible="visible" header="View Teacher" position="right">
        <div class="p-4">
                <div class="flex flex-col gap-2">
                    <label for="section">Username</label>
                    <InputText id="section" v-model="data.username" aria-describedby="section-help" :readonly="true" />
                </div>
                <div class="flex flex-col gap-2">
                    <label for="description">Name</label>
                    <InputText id="description" v-model="data.name" aria-describedby="description-help"
                        :readonly="true" />
                </div>
                <div class="flex flex-col gap-2">
                    <label for="description">Sections</label>
                    <div class="flex space-x-1">
                        <Button class="px-1 py-1" size="" v-for="section in data.sections " value="8"
                            severity="secondary">
                            <Link href="/sections" :data="{ id: section?.id }">
                                {{ section?.section }}
                            </Link>
                        </Button>
                    </div>
                </div>
            </div>
    </Drawer>
</template>
<script setup lang="ts">
import Drawer from 'primevue/drawer';
import InputText from 'primevue/inputtext';
import { ref, inject } from 'vue';
import type { FormTypes } from '@/Pages/Teacher/Types/types';
import Button from 'primevue/button';
import { Link } from '@inertiajs/vue3';

const convertDateToTime = inject('convertDateToTime')
const statusBadge = inject('statusBadge')

const visible = ref(false)
const data = ref<FormTypes>({
    name: '',
    username: '',
    password: '',
    sections: [],
    status: ''

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