<template>

    <Head title="Attendance" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Attendance</h2>
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
                                        <div class="flex space-x-2">
                                            <Select 
                                                @change="handleChangeSection"
                                                v-model="section" 
                                                :options="sectionOptions" 
                                                optionValue="id" 
                                                optionLabel="section" 
                                                class="w-fit " />
                                                <DatePicker v-model="filters.date" showButtonBar/>
                                            <Button icon="pi pi-qrcode" severity="success" @click="$refs.qm?.open()" />
                                            <Button icon="pi pi-calendar" severity="info" @click="$refs.om?.open()" />
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
                                                    placeholder="Enter to Search Student" />
                                            </IconField>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <Column header="First Name" style="width: 25%"> 
                                <template #body="props">
                                    <Button :label="props.data.user.first_name" link @click="$refs.vm?.open(props.data.user.id)"/>
                                </template>
                            </Column>
                            <Column header="Last Name" style="width: 25%"> 
                                <template #body="props">
                                    <Button :label="props.data.user.last_name" link @click="$refs.vm?.open(props.data.user.id)"/>
                                </template>
                            </Column>
                            <Column header="Section" style="width: 25%">
                                <template #body="props">
                                    <div class="flex space-x-1">
                                        <Button class="px-1 py-1" size="" v-for="section in props.data.user.sections" value="8" severity="secondary" @click="rsm.open(section.id)">
                                            {{ section.section }}
                                        </Button>
                                    </div>
                                </template>
                            </Column>
                            <Column value="date" header="Date" style="width: 20%" class="text-nowrap">
                                <template #body="props">
                                    <p>{{ props.data.date }}</p>
                                </template>
                            </Column>
                            <Column header="Time in" style="width: 10%" class="text-nowrap">
                                <template #body="props">
                                    <Badge 
                                        :value="props.data.time_in.time" 
                                        size="large" 
                                        :severity="timeStatusColor(props.data.time_in.status)">
                                    </Badge>
                                </template>
                            </Column>
                            <Column header="Time out" style="width: 10%" class="text-nowrap">
                                <template #body="props">
                                    <Badge 
                                        :value="props.data.time_out?.time ?? 'No Record'" 
                                        size="large" 
                                        :severity="timeStatusColor(props.data.time_out.status)">
                                    </Badge>
                                </template>
                            </Column>
                            <template #empty>
                                <div class="text-center">
                                    No Record found.
                                </div>
                            </template>
                        </DataTable>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
    <QrModal ref="qm" />
    <UpdateModal ref="um" />
    <ViewStudentModel ref="vm" />
    <ReadSectionModal ref="rsm" />
    <OverViewModal ref="om" />
</template>
<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { ref, onMounted, provide, watch, nextTick } from 'vue'
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import IconField from 'primevue/iconfield';
import InputText from 'primevue/inputtext';
import InputIcon from 'primevue/inputicon';
import Select from 'primevue/select';
import DatePicker from 'primevue/datepicker';
import QrModal from './components/QrModal.vue';
import UpdateModal from './components/UpdateModal.vue';
import { router } from '@inertiajs/vue3';
import type { PageTypes, FilterTypes, SortTypes } from '@/Pages/Attendance/types/types';
import { useConfirm } from "primevue/useconfirm";
import { useToast } from 'primevue/usetoast';
import axios from 'axios';
import ViewStudentModel from '@/Pages/Student/components/readModal/readModal.vue'
import Badge from 'primevue/badge';
import ReadSectionModal from '../Section/components/readModal.vue';
import OverViewModal from './components/overViewModal.vue';

const filters = ref<FilterTypes>({
    page: 1,
    sortBy: 'id',
    sortType: 1,
    rows: 10,
    filter: null,
    section: 0 ,
    date: null
});

const section = ref<number>(0)
const sectionOptions = ref<{id?: number, section?:string}[]>([])
const qm = ref()
const um = ref()
const vm = ref()
const rsm = ref()
const om = ref()

const page = usePage()

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



watch(
    () => filters.value.date,
    async (n, o) => {
        reloadTable()
    },
    {
        deep: true,
        immediate: false
    }
)


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

function reloadTable() {
    router.reload({
        data: filters.value
    })
}

function timeStatusColor(status: string|null) {
    const statuses: {[status: string]: string} = {
        early: 'secondary',
        ontime: 'success',
        late: 'warn',
    }

    return statuses[status ?? ''] ?? 'danger'
}

provide('convertDateToTime', convertDateToTime)
provide('statusBadge', statusBadge)
provide('reloadTable', reloadTable)
</script>
