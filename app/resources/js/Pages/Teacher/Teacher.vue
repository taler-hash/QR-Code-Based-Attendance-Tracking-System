<template>

    <Head title="Teacher" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Teachers</h2>
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
                            @sort="handleSort($event as any)"
                            @page="handlePage($event)"
                            :sortField="page.props.sortBy"
                            :sortOrder="page.props.sortType"
                            v-model:first="filters.page"
                            :lazy="true"
                            :rowsPerPageOptions="[5, 10, 20, 50]" tableStyle="min-width: 50rem">
                            <template #header>
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <span class="text-xl font-bold">Lists</span>
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
                            <Column field="username" header="Username" style="width: 25%" sortable class="font-bold " />
                            <Column field="name" header="Name" style="width: 25%" sortable />
                            <Column field="section" header="Section" style="width: 25%">
                                <template #body="props">
                                    <div class="flex space-x-1">
                                        <Button class="px-1 py-1" v-for="section in props.data.sections" value="8" severity="secondary">
                                            <Link href="/sections" :data="{id : section.id}">{{ section.section }}</Link>
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
                                        <Button icon="pi pi-eye" @click="$refs.rd?.open(props)" severity="info" outlined text size="small" class="!p-1 min-w-0"/>
                                        <Button icon="pi pi-pencil" @click="$refs.um?.open(props)" severity="warn" outlined text size="small" class="!p-1 min-w-0"/>
                                        <Button icon="pi pi-times" @click="handleDelete(props)" severity="danger" outlined text size="small" class="!p-1 min-w-0"/>
                                    </div>
                                </template>
                            </Column>
                            <template #empty>
                                <div class="text-center">
                                    No Teachers found.
                                </div>
                            </template>
                        </DataTable>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
    <CreateModal ref="am" />
    <ReadDrawer ref="rd" />
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
import Badge from 'primevue/badge';
import CreateModal from './components/createModal.vue';
import ReadDrawer from './components/readDrawer.vue'
import UpdateModal from './components/updateModal.vue';
import { router } from '@inertiajs/vue3';
import type { PageTypes, FilterTypes, SortTypes } from './Types/types';
import { useConfirm } from "primevue/useconfirm";
import { useToast } from 'primevue/usetoast';
import { Link } from '@inertiajs/vue3'


const filters = ref<FilterTypes>({
    page: 1,
    sortBy: 'id',
    sortType: 1,
    rows: 10,
    filter: null
});

const am = ref()
const um = ref()
const dm = ref()

const page = usePage<any>()
const toast = useToast()
const confirm = useConfirm()

const emits = defineEmits(['update:sortField', 'update:sortOrder'])

onMounted(() => {
    const { filter, sortBy, sortType } = page.props
    filters.value = { 
        ...filters.value,
        filter: filter,
        sortBy: sortBy as string,
        sortType: sortType as number,
        page: page.props.page as number
    }
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
            router.delete(route('teachers.delete'), props)
            toast.add({ severity: 'success', summary: 'Success', detail: 'Deleted Section Successfully', life: 3000 });
        }

    })
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

provide('convertDateToTime', convertDateToTime)
provide('statusBadge', statusBadge)

function reloadTable() {
    router.reload({
        data: filters.value
    })
}
</script>
