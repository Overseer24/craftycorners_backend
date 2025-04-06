<script setup>
import { useForm } from "@inertiajs/vue3";
import TextInput from "@/Components/TextInput.vue";
import SelectInput from "@/Components/SelectInput.vue";

// Props for controlling modal and mode
const props = defineProps({
    show: Boolean,
    mode: String,
});
const emit = defineEmits(["close", "switch-mode"]);

// Form data
const form = useForm({
    first_name: "",
    middle_name: "",
    last_name: "",
    user_name: "",
    email: "",
    password: "",
    password_confirmation: "",
    phone_number: "",
    student_id: "",
    sex: "",
    program: "",
});

// Close modal when clicking outside
const closeModal = (event) => {
    if (event.target.id === "modal-overlay") {
        emit("close");
    }
};

// Submit form
const submit = () => {
    if (props.mode === "login") {
        form.post(route("login"), {
            onError: () => form.reset("password"),
        });
    } else {
        form.post(route("register"), {
            onError: () => form.reset("password", "password_confirmation"),
        });
    }
};
</script>

<template>
    <div
        v-if="show"
        id="modal-overlay"
        @click="closeModal"
        class="fixed inset-0 bg-black/75 flex justify-center items-center p-4"
    >
        <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md sm:max-w-lg md:max-w-2xl" @click.stop>
            <!-- Close button -->
            <div class="flex justify-end">
                <button @click="$emit('close')" class="text-gray-500 hover:text-gray-700">
                    X
                </button>
            </div>

            <!-- Dynamic Title -->
            <h2 class="text-xl font-bold text-center mb-4">
                {{ mode === "login" ? "Login" : "Register" }}
            </h2>

            <!-- Form -->
            <form @submit.prevent="submit">
                <!-- Registration Fields (Only for Register Mode) -->
                <div v-if="mode === 'register'" class="space-y-4">
                    <!-- Full Name Section -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <TextInput v-model="form.first_name" name="First Name" label="First Name" :message="form.errors.first_name" />
                        <TextInput v-model="form.middle_name" name="Middle Name" label="Middle Name" :message="form.errors.middle_name" />
                        <TextInput v-model="form.last_name" name="Last Name" label="Last Name" :message="form.errors.last_name" class="md:col-span-2" />
                    </div>

                    <!-- Contact Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <TextInput v-model="form.phone_number" name="Phone Number" label="Phone Number" :message="form.errors.phone_number" />
                        <TextInput v-model="form.student_id" name="Student ID" label="Student ID" :message="form.errors.student_id" />
                    </div>

                    <!-- Username & Program -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <TextInput v-model="form.user_name" name="Username" label="Username" :message="form.errors.user_name" />
                        <TextInput v-model="form.program" name="Program" label="Program" :message="form.errors.program" />
                    </div>

                    <!-- Select Input for Sex -->
                    <SelectInput
                        name="Sex" v-model="form.sex"
                        :options="[
                            { value: 'male', label: 'Male' },
                            { value: 'female', label: 'Female' },
                            { value: 'other', label: 'Other' }
                        ]"
                        :message="form.errors.sex"
                    />
                </div>

                <!-- Common Fields (Login + Register) -->
                <div class="space-y-4 mt-4">
                    <TextInput v-model="form.email" name="Email" label="Email" type="email" :message="form.errors.email" />
                    <TextInput v-model="form.password" name="Password" label="Password" type="password" :message="form.errors.password" />

                    <!-- Confirm Password (Only for Register) -->
                    <TextInput
                        v-if="mode === 'register'"
                        v-model="form.password_confirmation"
                        name="Confirm Password"
                        label="Confirm Password"
                        type="password"
                        :message="form.errors.password_confirmation"
                    />
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded mt-4">
                    {{ mode === "login" ? "Login" : "Register" }}
                </button>
            </form>

            <!-- Switch Mode -->
            <p class="text-sm mt-4 text-center">
                {{ mode === "login" ? "Not yet a user?" : "Already have an account?" }}
                <button @click="$emit('switch-mode')" class="text-blue-500 underline cursor-pointer">
                    {{ mode === "login" ? "Register" : "Login" }}
                </button>
            </p>
        </div>
    </div>
</template>



