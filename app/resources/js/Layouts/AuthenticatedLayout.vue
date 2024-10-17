<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <Menubar :model="links">
                <template #start>
                    <div class="rounded-md overflow-hidden">
                        <img :src="Logo" alt="" class="w-14">
                    </div>
                </template>
                <template #item="{ item, props }">
                    <Link v-if="item.show" :href="route(item.label.toLowerCase())" class="flex items-center space-x-2 mx-2 px-2 py-4"
                        :class="{ 'bg-surface-100': route().current(item.label.toLowerCase()) }">
                    <span :class="item.icon"></span>
                    <span>{{ item.label }}</span>
                    </Link>
                </template>
                <template #end>
                    <Button @click="toggleOption" severity="primary">
                        <div class="flex">
                            <p class="pr-2 border-r-2">{{ $page.props.auth.user.name }}</p>
                            <p v-for="role in displayRoles()" class="pl-2 w-fit">{{ role }}</p>
                        </div>
                    </Button>
                    <Popover ref="option" 
                        pt:content="p-0">
                        <div class="grid divide-y-2">
                            <Link :href="route('profile.edit')"
                                class="flex items-center space-x-2 px-4 py-4 rounded-t-md"
                                :class="{ 'bg-surface-100': route().current('profile.edit') }">
                            <span class="pi pi-user"></span>
                            <span>Profile</span>
                            </Link>
                            <Link :href="route('logout')"
                                as="button"
                                method="post"
                                class="flex items-center space-x-2 px-4 py-4"
                                :class="{ 'bg-surface-100': route().current('logout') }">
                            <span class="pi pi-sign-out"></span>
                            <span>Logout</span>
                            </Link>
                        </div>
                    </Popover>
                </template>
            </Menubar>
            <!-- Page Heading -->
            <header class="bg-white shadow" v-if="$slots.header">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <Toast />
                <ConfirmDialog />
                <slot/>
            </main>
        </div>
    </div>
    
</template>

<script setup lang="ts">
import { provide, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import Logo from '../../assets/logo.png'
import Menubar from 'primevue/menubar';
import Button from 'primevue/button';
import Popover from 'primevue/popover';
import Toast from 'primevue/toast';
import ConfirmDialog from 'primevue/confirmdialog';

const page = usePage<any>()
const sectionReadDialog = ref()

const props = ref({
    sectionReadDialog: sectionReadDialog
})
const displayRoles = () => {
    return page.props.auth.user.roles.map((i: any) => i.name)
}

const links = ref([
    {
        label: 'Dashboard',
        icon: 'pi pi-chart-pie',
        show: true
    },
    {
        label: 'Teachers',
        icon: 'pi pi-users',
        show: page.props.auth.user.roles.some((v: any) => v.name === 'admin')
    },
    {
        label: 'Students',
        icon: 'pi pi-users',
        show: page.props.auth.user.roles.some((v: any) => v.name === 'teacher')
    },
    {
        label: 'Attendance',
        icon: 'pi pi-check-circle',
        show: page.props.auth.user.roles.some((v: any) => v.name === 'teacher')
    },
    {
        label: 'Sections',
        icon: 'pi pi-list',
        show: page.props.auth.user.roles.some((v: any) => v.name === 'admin')
    }
])
const option = ref()

const toggleOption = (event: HTMLElement) => {
    option.value.toggle(event)
}

</script>
