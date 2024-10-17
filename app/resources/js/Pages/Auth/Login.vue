<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Logo from '../../../assets/logo.png'
import Card from 'primevue/card';

defineProps<{
    canResetPassword?: boolean;
    status?: string;
}>();

const form = useForm({
    username: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onSuccess: () => {
            form.reset('password');
        },
    });
};
</script>

<template>
    <GuestLayout>

        <Head title="Log in" />

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>
        <div class="rounded overflow-hidden m-2">
            <img :src="Logo" alt="" class="w-20">
        </div>
        <Card class="px-4">
            <template #title>
                <h1 class="font-bold text-xl">Attendance System</h1>
                <small class=" text-sm text-gray-500">QR Code-Based Attendance Tracking System</small>
            </template>
            <template #content>
                <form @submit.prevent="submit">
                    <div class="flex flex-col gap-2">
                        <label for="username">Username</label>
                        <InputText id="username" v-model="form.username" aria-describedby="username-help" v:bind />
                        <InputError :message="form.errors.username" />
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="username">Password</label>
                        <InputText type="password" id="password" v-model="form.password" aria-describedby="username-help" />
                        <InputError :message="form.errors.password" />
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <Button type="submit" class="w-full" :disabled="form.processing">Log in</Button>
                    </div>
                </form>
            </template>
        </Card>

    </GuestLayout>
</template>
