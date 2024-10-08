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
                                                <DatePicker 
                                                    v-model="filters.date"
                                                    selection-mode="range"
                                                    showButtonBar
                                                 />
                                            <Button icon="pi pi-qrcode" severity="success" @click="$refs.qm?.open()" />
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
                                    </div>
                                </div>
                            </template>
                            <Column field="user.name" header="Name" style="width: 25%" sortable />
                            <Column header="Section" style="width: 25%">
                                <template #body="props">
                                    <div class="flex space-x-1">
                                        <Button class="px-1 py-1" size="" v-for="section in props.data.user.sections" value="8" severity="secondary">
                                            <Link href="/sections" :data="{id : section.id}">{{ section.section }}</Link>
                                        </Button>
                                    </div>
                                </template>
                            </Column>
                            <Column header="Date" style="width: 25%">
                                <template #body="props">
                                    <p>{{ convertLongDate(props.data.date)}}</p>
                                </template>
                            </Column>
                            <Column header="Time in" style="width: 10%">
                                <template  #body="props">
                                    <p class="p-1 rounded w-fit text-white" :class="determineTime(props, 'in')?.class">{{ formatTime(determineTime(props, 'in')?.time) }}</p>
                                </template>
                            </Column>
                            <!-- Tiwasa ug unsaon nimu pag display ang time naa na solution collapse ug expand sa datatable unya kuhaa ang midpoint sa time-->
                            <Column header="Time out" style="width: 10%">
                                <template  #body="props">
                                    <p class="p-1 rounded w-fit text-white" :class="determineTime(props, 'out')?.class">{{ formatTime(determineTime(props, 'out')?.time)}}</p>
                                </template>
                            </Column>
                            <Column field="reason" header="Reason" style="width: 25%" />
                            <Column style="width: 10%">
                                <template #header>
                                    <div class="w-full text-center">
                                        Actions
                                    </div>
                                </template>
                                <template #body="props">
                                    <div class="flex items-center">
                                        <Button icon="pi pi-pencil" @click="$refs.um?.open(props)" severity="warn" outlined text size="small" class="!p-1 min-w-0"/>
                                    </div>
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
</template>
<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { ref, onMounted, provide } from 'vue'
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
import type { PageTypes, FilterTypes, SortTypes } from '@/Pages/Attendance/types/types.ts';
import { useConfirm } from "primevue/useconfirm";
import { useToast } from 'primevue/usetoast';
import { Link } from '@inertiajs/vue3'
import axios from 'axios';
import moment from 'moment';


const filters = ref<FilterTypes>({
    page: 1,
    sortBy: 'id',
    sortType: 1,
    rows: 10,
    filter: null,
    section: 0 ,
    date: ''
});

const section = ref<number>(0)
const sectionOptions = ref<{id?: number, section?:string}[]>([])
const qm = ref()
const um = ref()

const page = usePage()
const toast = useToast()
const confirm = useConfirm()

const emits = defineEmits(['update:sortField', 'update:sortOrder'])

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

function determineTime(props: any, type: string): {time: string, class: string} {
    const _props = JSON.parse(JSON.stringify(props.data))
    const midPoint = determineMidPoint(_props);

    if(type === 'in') {
        return determineTimeIn(_props, midPoint)
    } else {
        return determineTimeOut(_props, midPoint)
    }
}

function determineMidPoint(props: any): string {
    // Parse the two times
    const timeIn = moment(props.user.sections[0].time_in, 'YYYY-MM-DD HH:mm:ss');
    const timeOut = moment(props.user.sections[0].time_out, 'YYYY-MM-DD HH:mm:ss');

    // Calculate the midpoint
    const diff = timeOut.diff(timeIn);
    const midpoint = timeIn.add(diff / 2);

    return midpoint.format('HH:mm:ss')
}

function determineTimeIn(props:any, midPoint: string) {
    let _class = ''
    const timeIns = props.time_logs.filter((v: any) => {
        return moment(v.time, "HH:mm:ss").isBefore(moment(midPoint, "HH:mm:ss"))
    })
    const timeIn = timeIns?.[0] ? timeIns?.[0].time : null
    
    if(!timeIn) {
        _class = 'bg-red-500'
    } else {
        const isLate = moment(timeIn, 'HH:mm:ss').isAfter(moment(props.user.sections[0].time_in, 'YYYY-MM-DD HH:mm:ss'))

        _class = isLate ? 'bg-yellow-500' : 'bg-lime-500'
    }

    return {
        time: timeIn ?? 'no record',
        class: _class
    }
}

function determineTimeOut(props:any, midPoint: string) {
    let _class = ''
    const timeOuts = props.time_logs.filter((v:any) => {
        return moment(v.time, "HH:mm:ss").isAfter(moment(midPoint, "HH:mm:ss"))
    })
    const timeOut = timeOuts?.[timeOuts?.length - 1] ? timeOuts?.[timeOuts?.length - 1].time : null
    
    if(!timeOut) {
        _class = 'bg-red-500'
    } else {
        const isEarly = moment(timeOut, 'HH:mm:ss').isBefore(moment(props.user.sections[0].time_out, 'YYYY-MM-DD HH:mm:ss'))

        _class = isEarly ? 'bg-yellow-500' : 'bg-lime-500'
    }

    return {
        time: timeOut ?? 'no record',
        class: _class
    }
}

function formatTime(time: string) {
    const converted = moment(time, 'HH:mm:ss')
    return converted.isValid() ? converted.format('hh:mm:ss a') : 'No Record'
}

function statusBadge(status: string): string {
    return `${status === 'active' ? 'success' : 'secondary'}`
}

function reloadTable() {
    router.reload({
        data: filters.value
    })
}

function convertLongDate(date: string) {
    return moment(date).format('ddd MMM DD, YYYY');
}

provide('convertDateToTime', convertDateToTime)
provide('statusBadge', statusBadge)
provide('reloadTable', reloadTable)


</script>
