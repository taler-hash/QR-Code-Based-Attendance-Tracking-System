

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 lg:grid-flow-col grid grid-cols-auto gap-4">
                    <MiniCard 
                        v-if="page.props.auth.user.roles.some((v: any) => v.id === 1)"
                        icon="pi-user"
                        header="Teachers"
                        :value="teacherCount"
                    />
                    <MiniCard 
                        icon="pi-user"
                        header="Students"
                        :value="studentsCount"
                    />
                    <MiniCard
                        v-if="page.props.auth.user.roles.some((v: any) => v.id === 2)"
                        icon="pi-check"
                        header="Attendance"
                        :value="attendedCount"
                    />
                    <MiniCard 
                        v-if="page.props.auth.user.roles.some((v: any) => v.id === 2)"
                        icon="pi-flag"
                        header="Sections"
                        :value="page.props.auth.user.sections.length"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import MiniCard from './components/miniCard.vue';
import axios from 'axios';
import { onMounted, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage<any>()
const studentsCount = ref(0)
const attendedCount = ref(0)
const teacherCount = ref(0)

onMounted(() => {
    console.log()
    getStudentCount()
    getTeacherCount()
    getAttendanceCount()
})

async function getStudentCount() {
    const res = await axios.get(route('students.getCount'))

    studentsCount.value = res.data
}

async function getAttendanceCount() {
    const res = await axios.get(route('attendance.getCurrentTotalAttended'))

    attendedCount.value = res.data
}

async function getTeacherCount() {
    const res = await axios.get(route('teachers.getCount'))

    teacherCount.value = res.data
}
</script>
