<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputText from 'primevue/inputtext';
import { useForm, usePage } from '@inertiajs/vue3';
import Button from 'primevue/button';

defineProps<{
    mustVerifyEmail?: Boolean;
    status?: String;
}>();

const user = usePage().props.auth.user;

const form = useForm<any>({
    username: user.username,
    first_name: user.first_name,
    last_name: user.last_name,
});
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">Profile Information</h2>

            <p class="mt-1 text-sm text-gray-600">
                Update your account's profile information.
            </p>
        </header>

        <form @submit.prevent="form.patch(route('profile.update'))" class="mt-6 space-y-6">
            <div class="flex flex-col gap-2">
                <label for="username">Username</label>
                <InputText id="username" v-model="form.username" aria-describedby="username-help" />
                <InputError :message="form.errors.username" />
            </div>
            <div class="flex flex-col gap-2">
                <label for="first_name">First Name</label>
                <InputText id="first_name" v-model="form.first_name" aria-describedby="first_name-help" />
                <InputError :message="form.errors.first_name" />
            </div>
            <div class="flex flex-col gap-2">
                <label for="last_name">Last name</label>
                <InputText id="last_name" v-model="form.last_name" aria-describedby="last_name-help" />
                <InputError :message="form.errors.last_name" />
            </div>
            <div class="flex items-center gap-4">
                <Button type="submit" label="Save" :disabled="form.processing || !form.isDirty" />

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">Saved.</p>
                </Transition>
            </div>
        </form>
    </section>
</template>
