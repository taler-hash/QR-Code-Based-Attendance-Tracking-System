<template>
    <Dialog v-model:visible="visible" modal header="Overview">
        <div class="card space-y-4">
            <div class="flex-auto">
                <label for="startDate" class="font-bold block mb-2"> Sections </label>
                <MultiSelect v-model="form.sections" :options="sectionOptions" filter value="id" optionLabel="section"
                    optionValue="id" placeholder="Select Sections" class="w-full md:w-80" />
            </div>
            <div class="flex-auto">
                <label for="startDate" class="font-bold block mb-2"> Start Date </label>
                <DatePicker id="startDate" v-model="form.startDate" fluid  :maxDate="startDateMax()"  />
            </div>
            <div class="flex-auto">
                <label for="endDate" class="font-bold block mb-2"> End Date </label>
                <DatePicker id="endDate" v-model="form.endDate" fluid :maxDate="startDateMax()"/>
            </div>
            <div class="flex-auto">
                <label for="startDate" class="font-bold block mb-2"> Week days to Exclude </label>
                <MultiSelect v-model="form.weekDaysToDisable" :options="weekDayOption" filter
                    placeholder="Select WeekDays to disable" class="w-full md:w-80" />
            </div>
            <div v-if="form.startDate && form.endDate" class="flex-auto space-y-2">
                <label for="startDate" class="font-bold block mb-2"> Dates to Exclude</label>
                <div  v-for="(date, index) in form.excludeDates">
                    <div class=" flex space-x-2">
                        <DatePicker v-model="date.date" fluid  class="grow" :minDate="form.startDate" :maxDate="form.endDate"/>
                        <Button @click="addDate" icon="pi pi-plus" severity="success"></Button>
                        <Button v-if="form.excludeDates.length > 1" @click="removeDate(index)" icon="pi pi-times"
                            severity="danger"></Button>
                    </div>
                </div>
            </div>
            <div class="flex justify-center border-t-2 pt-4">
                <Button @click="generate" label="Generate" severity="success" :disabled="disableGenerateButton()"/>
            </div>
        </div>
    </Dialog>
</template>
<script setup lang="ts">
import Dialog from 'primevue/dialog';
import DatePicker from 'primevue/datepicker';
import Button from 'primevue/button';
import MultiSelect from 'primevue/multiselect';
import { ref } from 'vue';
import axios from 'axios';
import moment from 'moment';
import { useToast } from 'primevue/usetoast';

const toast = useToast()

interface FormTypes {
    sections: number[],
    startDate: Date | null,
    endDate: Date | null,
    weekDaysToDisable: string[]
    excludeDates: any[],
    overview: boolean
}

const visible = ref(false)

const form = ref<any>({
    sections: [],
    startDate: null,
    endDate: null,
    weekDaysToDisable: [],
    excludeDates: [{ date: null }]
})
const sectionOptions = ref<{ id?: number, section?: string }[]>([])

const weekDayOption = [
    'monday',
    'tuesday',
    'wednesday',
    'thursday',
    'friday',
    'saturday',
    'sunday'
]

async function getSections() {
    const sections = await axios.get(route('sections.get'))

    sectionOptions.value = sections.data
}

function addDate() {
    form.value.excludeDates.push({ date: null })
}

function removeDate(index: number) {
    form.value.excludeDates.splice(index, 1)
}

function disableGenerateButton() {
    return !form.value.sections.length || !form.value.startDate || !form.value.endDate
}

async function generate() {
    const _form = JSON.parse(JSON.stringify(form.value))
    _form.excludeDates = _form.excludeDates.map((v: any) => v.date)
    _form.overview = true

    const res = await axios.get(route('attendance.overview'), {params: _form})

    if(res.status === 204) {
        toast.add({ severity: 'info', summary: 'Info', detail: 'No Available Data.', life: 3000 });
    } else {
        window.open(res.request.responseURL)
        toast.add({ severity: 'success', summary: 'Success', detail: 'Overview Excel generated.', life: 3000 });
        visible.value = false
    }
}

function startDateMax() {
    return new Date(moment().subtract(1, 'd').toDate())
}

function open() {
    visible.value = true
    getSections()
}

defineExpose({
    open
})
</script>