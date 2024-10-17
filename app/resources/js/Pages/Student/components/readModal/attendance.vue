<template>
    <div class="grow border p-2 rounded">
        <p class="font-medium pb-2">Attendance</p>
        <DataTable 
            :value="data.data" 
            :lazy="true" 
            @page="handlePage($event)"
            paginator
            :totalRecords="data.total"
            :rows="data.per_page">
            <Column field="date" header="Date" class="text-nowrap"/>
            <Column header="Time in" class="text-nowrap w-0">
                <template #body="props">
                    <Badge 
                        :value="props.data.time_in.time" 
                        size="large" 
                        :severity="timeStatusColor(props.data.time_in.status)">
                    </Badge>
                </template>
            </Column>
            <Column header="Time out" class="text-nowrap w-0">
                <template #body="props">
                    <Badge 
                        :value="props.data.time_out.time" 
                        size="large" 
                        :severity="timeStatusColor(props.data.time_out.status)">
                    </Badge>
                </template>
            </Column>
        </DataTable>
    </div>
</template>
<script setup lang="ts">
import axios from 'axios';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { inject, onMounted, ref } from 'vue';
import Badge from 'primevue/badge';
import _ from 'lodash';

const attendance = ref<any>()
const id = inject<any>('id')
const data = ref<any>({})

onMounted(() => {
    data.value.id = id.value
    reloadTable()
})

function handlePage(event: any) {
    data.value.page = event.page + 1

    reloadTable()
}


async function reloadTable() {
    const res = await axios.get(route('attendance.getStudentAttendance'), { params: _.omit(data.value, ['data']) })
    data.value = {...data.value, ...res.data}
}

function timeStatusColor(status: string|null) {
    const statuses: {[status: string]: string} = {
        early: 'secondary',
        ontime: 'success',
        late: 'warn',
    }

    return statuses[status ?? ''] ?? 'danger'
}
</script>