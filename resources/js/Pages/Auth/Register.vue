<script setup>
import { useForm } from "@inertiajs/vue3";
import TextInput from "../../Components/TextInput.vue";
import SelectInput from "../../Components/SelectInput.vue";

const form = useForm({
    first_name: null,
    middle_name: null,
    last_name: null,
    user_name: null,
    email: null,
    password: null,
    password_confirmation: null,
    phone_number: null,
    student_id: null,
    sex: null,
    program: null, // Add this line to include program in the form data
});

const submit = () => {
    form.post(route("register"),{
        onError: () => form.reset("password", "password_confirmation"),
    });
};
</script>

<template>
    <Head title="Register" />
    <h1 class="title">Register a New Account</h1>
    <div class="w-full max-w-xl mx-auto">
        <form @submit.prevent="submit">
            <small>{{ form.errors }}</small>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- First Name -->
                <div class="mb-4 md:col-span-2">
                    <TextInput
                        name="First Name"
                        label="First Name"
                        v-model="form.first_name"
                        :message="form.errors.first_name"
                    />
                </div>

                <!-- Middle Name -->
                <div class="mb-4 md:col-span-2">
                    <TextInput
                        name="Middle Name"
                        label="Middle Name"
                        v-model="form.middle_name"
                        :message="form.errors.middle_name"
                    />
                </div>

                <!-- Last Name -->
                <div class="mb-4 md:col-span-2">
                    <TextInput
                        name="Last Name"
                        label="Last Name"
                        v-model="form.last_name"
                        :message="form.errors.last_name"
                    />
                </div>

                <!-- Username -->
                <div class="mb-4">
                    <TextInput
                        name="Username"
                        label="Username"
                        v-model="form.user_name"
                        :message="form.errors.user_name"
                    />
                </div>

                <!-- Email -->
                <div class="mb-4">
                    <TextInput
                        name="Email"
                        label="Email"
                        type="email"
                        v-model="form.email"
                        :message="form.errors.email"
                    />
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <TextInput
                        name="Password"
                        label="Password"
                        type="password"
                        v-model="form.password"
                        :message="form.errors.password"
                    />
                </div>

                <!-- Confirm Password -->
                <div class="mb-4">
                    <TextInput
                        name="Confirm Password"
                        label="Confirm Password"
                        type="password"
                        v-model="form.password_confirmation"
                    />
                </div>

                <!-- Sex -->
                <div class="mb-4">
                    <SelectInput
                    name="Sex"
                    v-model="form.sex"
                    :message="form.errors.sex"

                    :options="[
                        {value: 'male' ,label: 'Male' },
                        {value:'female', label:'Female'},
                        {value:'other', label:'Other'}
                    ]"
                    />

                </div>

                <!-- Phone Number -->
                <div class="mb-4">
                    <TextInput
                        name="Phone Number"
                        label="Phone Number"
                        v-model="form.phone_number"
                        :message="form.errors.phone_number"
                    />
                </div>

                <!-- Student ID -->
                <div class="mb-4">
                    <TextInput
                        name="Student ID"
                        label="Student ID"
                        v-model="form.student_id"
                        :message="form.errors.student_id"
                    />
                </div>

                <!-- Program -->
                <div class="mb-4">
                    <TextInput
                        name="Program"
                        label="Program"
                        v-model="form.program"
                        :message="form.errors.program"
                    />
                </div>
            </div>
            <div>
                <p>
                    Already have an account?
                    <a :href=" route('login')" class="text-blue-500"
                        >Login</a
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
                    Register
                </button>
            </div>
        </form>
    </div>
</template>
