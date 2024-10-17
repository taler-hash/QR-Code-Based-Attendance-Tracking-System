<template>

    <Head title="Student" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Students</h2>
        </template>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="card">
                        <DataTable 
                            showGridlines
                            size="small" 
                            removableSort 
                            :value="page.props.data" 
                            paginator 
                            :totalRecords="page.props.total"
                            :rows="page.props.per_page"
                            @sort="handleSort($event)"
                            @page="handlePage($event)"
                            :sortField="page.props.sortBy"
                            :sortOrder="page.props.sortType"
                            v-model:first="filters.page"
                            :lazy="true"
                            :rowsPerPageOptions="[5, 10, 20, 50]" tableStyle="min-width: 50rem">
                            <template #header>
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <div class="flex space-x-2 items-center">
                                        <span class="text-xl font-bold">Lists</span>
                                        <div class="flex items-center space-x-2">
                                            <Select 
                                                @change="handleChangeSection"
                                                v-model="section" 
                                                :options="sectionOptions" 
                                                optionValue="id" 
                                                optionLabel="section" 
                                                class="w-fit " />
                                            <Button severity="info" icon="pi pi-print" @click="$refs.pm?.open()"></Button>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <div class="flex justify-end">
                                            <IconField>
                                                <InputIcon>
                                                    <i class="pi pi-search" />
                                                </InputIcon>
                                                <InputText
                                                    v-model="filters.filter"
                                                    @keypress="handleSearch"
                                                    placeholder="Enter to Search" />
                                            </IconField>
                                        </div>
                                        <Button icon="pi pi-user-plus" severity="success" @click="$refs.am?.open()" />
                                    </div>
                                </div>
                            </template>
                            <Column class="w-fit" style="width: 5%">
                                <template #header>
                                    <Checkbox v-model="checkedAll" binary @change="handleCheckAll"  variant="filled" class="mr-2" />
                                    <Button :disabled="hasChecked()" @click="handleToPrintCustom" severity="info" icon="pi pi-qrcode" size="small" class="!px-0 !py-2"></Button>
                                </template>
                                <template #body="props">
                                    <Checkbox v-model="props.data.printQr" binary variant="filled" />
                                </template>
                            </Column>
                            <Column field="first_name" header="First Name" style="width: 25%" sortable />
                            <Column field="last_name" header="Last Name" style="width: 25%" sortable />
                            <Column field="section" header="Section" style="width: 25%">
                                <template #body="props">
                                    <div class="flex space-x-1">
                                        <Button class="px-1 py-1" size="" v-for="section in props.data.sections" value="8" severity="secondary" @click="rsm.open(section.id)">
                                            <p>{{ section.section }}</p>
                                        </Button>
                                    </div>
                                </template>
                            </Column>   
                            <Column style="width: 10%">
                                <template #header>
                                    <div class="w-full text-center">
                                        Actions
                                    </div>
                                </template>
                                <template #body="props">
                                    <div class="flex items-center">
                                        <Button icon="pi pi-eye" @click="$refs.rm?.open(props.data.id)" severity="info" outlined text size="small" class="!p-1 min-w-0"/>
                                        <Button icon="pi pi-pencil" @click="$refs.um?.open(props)" severity="warn" outlined text size="small" class="!p-1 min-w-0"/>
                                        <Button icon="pi pi-times" @click="handleDelete(props)" severity="danger" outlined text size="small" class="!p-1 min-w-0"/>
                                    </div>
                                </template>
                            </Column>
                            <template #empty>
                                <div class="text-center">
                                    No Students found.
                                </div>
                            </template>
                        </DataTable>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
    <CreateModal ref="am" />
    <ReadModal ref="rm" />
    <UpdateModal ref="um" />
    <PrintQrModal ref="pm" />
    <ToPrintModal ref="tm" />
    <ReadSectionModal ref="rsm"/>

</template>
<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { ref, onMounted, provide, inject } from 'vue'
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import IconField from 'primevue/iconfield';
import InputText from 'primevue/inputtext';
import InputIcon from 'primevue/inputicon';
import Select from 'primevue/select';
import Checkbox from 'primevue/checkbox';
import CreateModal from './components/createModal.vue';
import ReadModal from './components/readModal/readModal.vue'
import UpdateModal from './components/updateModal.vue';
import PrintQrModal from './components/PrintQrModal.vue';
import ToPrintModal from './components/ToPrintModal.vue';
import { router } from '@inertiajs/vue3';
import type { PageTypes, FilterTypes, SortTypes, UsePageTypes } from './Types/types';
import { useConfirm } from "primevue/useconfirm";
import { useToast } from 'primevue/usetoast';
import ReadSectionModal from '../Section/components/readModal.vue';
import axios from 'axios';
import _ from 'lodash';
import { useQRCode } from '@vueuse/integrations/useQRCode';

const filters = ref<FilterTypes>({
    page: 1,
    sortBy: 'id',
    sortType: 1,
    rows: 10,
    filter: null,
    section: 0 
});

const rsm = ref()
const section = ref<number>(0)
const sectionOptions = ref<{id?: number, section?:string}[]>([])
const am = ref()
const um = ref()
const rm = ref()
const pm = ref()
const tm = ref()

const page = usePage<{data: any[], auth: any}>()
const toast = useToast()
const confirm = useConfirm()
const checkedAll = ref<boolean>(false)

const emits = defineEmits(['update:sortField', 'update:sortOrder'])
provide('rsm', rsm)


onMounted(async () => {

    const { filter, sortBy, sortType } = page.props
    filters.value = { 
        ...filters.value,
        filter: filter,
        sortBy: sortBy as string,
        sortType: sortType as number,
        page: page.props.page as number,
        section: 0
    }

    await getSections()

})


function handleSort(event : SortTypes) {
    filters.value.sortType =  event.sortOrder
    filters.value.sortBy = event.sortField 

    reloadTable()
}

function handlePage(event : PageTypes) {
    filters.value.page = event.page + 1
    filters.value.rows = event.rows

    reloadTable()
}

function handleSearch(event: KeyboardEvent) {
    if (event.key === 'Enter') {
        reloadTable()
    }   
}

function handleDelete(props: object) {
    confirm.require({
        message: 'Do you want to delete this section permanently?',
        header: 'Delete',
        icon: 'pi pi-info-circle',
        rejectLabel: 'Cancel',
        rejectProps: {
            label: 'Cancel',
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            label: 'Delete',
            severity: 'danger'
        },
        accept: () => {
            router.delete(route('students.delete'), props)
            toast.add({ severity: 'success', summary: 'Success', detail: 'Deleted Section Successfully', life: 3000 });
        }

    })
}

async function getSections() {
    const sections = await axios.get(route('sections.get'))

    sectionOptions.value = sections.data
    sectionOptions.value.push({id:0, section:'All'})
}

function handleChangeSection() {

    filters.value.section = section.value

    reloadTable()
}

function convertDateToTime(str: string) {
    const date = new Date(str)

    return date.toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
    })
}

function statusBadge(status: string): string {
    return `${status === 'active' ? 'success' : 'secondary'}`
}

function hasChecked() {
    return !page.props.data.some((i: any) => i.printQr === true)
}

function handleCheckAll() {
    page.props.data.map((i: {printQr: boolean}) => {
        i.printQr = checkedAll.value

        return i
    })
}

function handleToPrintCustom() {
    const _data = JSON.parse(JSON.stringify(page.props.data))

    const data = {
        'Custom': {
            index: 0,
            data: _data.filter((i: any) => i.printQr).map((i: any) => {
                i.qrCode = useQRCode(i.uuid)
                i.section = i.sections[0].section

                return i
            })
        }
    }

    tm.value.open(data, 'custom')
}

provide('convertDateToTime', convertDateToTime)
provide('statusBadge', statusBadge)

function reloadTable() {
    router.reload({
        data: filters.value
    })
}
</script>
