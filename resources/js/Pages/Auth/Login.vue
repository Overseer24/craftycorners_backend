<script setup>
import { useForm } from "@inertiajs/vue3";
import TextInput from "../../Components/TextInput.vue";
import { route } from "../../../../vendor/tightenco/ziggy/src/js";

const form = useForm({
    email: null,
    password: null,
    remember: null,
});

const submit = () => {
    form.post(route("login"), {
        onError: () => form.reset("password", "remember"),
    });
};
</script>

<template>
    <Head title="Register" />
    <h1 class="title">Login to your account</h1>
    <div class="w-full max-w-xl mx-auto">
        <form @submit.prevent="submit">
            <small>{{ form.errors }}</small>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- First Name -->
                <div class="mb-4 md:col-span-2">
                    <TextInput
                        name="Email"
                        label="Email"
                        v-model="form.email"
                        :message="form.errors.email"
                        id = "email"
                    />
                </div>

                <!-- Middle Name -->
                <div class="mb-4 md:col-span-2">
                    <TextInput
                        name="Password"
                        label="Password"
                        v-model="form.password"
                        :message="form.errors.password"
                        type="password"
                        id = "password"
                    />
                </div>
            </div>
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                    <input type="checkbox" v-model="form.remember" id="remember" />
                    <label for="remember">Remember me</label>
                </div>

                <p>
                    Need an account?
                    <a :href="route('register')" class="text-blue-500"
                        >Register</a
                    >
                </p>
            </div>

            <!-- Submit Button -->
            <div class="mb-4">
                <button
                    :disabled="form.processing"
                    type="submit"
                    class="primary-btn"
                >
                    Login
                </button>
            </div>
        </form>
    </div>
</template>
