<template>
    <Drawer v-model:visible="visible" position="right" class="w-full">
        <template #container>
            <div class="p-4">
                <div class="flex justify-between">
                    <div class="pb-5 flex space-x-2">
                        <p class="font-bold">Student</p>
                        <Badge :value="data.status" :severity="statusBadge(data.status)"
                            class="capitalize !font-bold" />
                    </div>
                    <div class="">
                        <Button @click="visible = false" icon="pi pi-times" severity="ghost"
                            class="!rounded-full"></Button>
                    </div>
                </div>
                <div class="block sm:flex">
                    <div class="flex-shrink">
                        <div class="flex flex-col gap-2">
                            <label for="section">Username</label>
                            <InputText id="section" v-model="data.username" aria-describedby="section-help"
                                :readonly="true" />
                        </div>
                        <div class="flex flex-col gap-2">
                            <label for="description">Name</label>
                            <InputText id="description" v-model="data.name" aria-describedby="description-help"
                                :readonly="true" />
                        </div>
                        <div class="flex flex-col gap-2">
                            <label for="description">Section</label>
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
                </div>
                <div class="">

                </div>
            </div>
        </template>
    </Drawer>
</template>
<script setup lang="ts">
import Drawer from 'primevue/drawer';
import InputText from 'primevue/inputtext';
import { ref, inject } from 'vue';
import Badge from 'primevue/badge';
import type { FormTypes, sectionOptionTypes } from '@/Pages/Student/Types/types';
import Button from 'primevue/button';
import { Link } from '@inertiajs/vue3';

const convertDateToTime = inject('convertDateToTime')
const statusBadge = inject('statusBadge')

const visible = ref(false)
const data = ref<FormTypes>({
    username: '',
    password: '',
    name: '',
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