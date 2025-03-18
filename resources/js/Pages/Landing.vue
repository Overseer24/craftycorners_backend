<template>
    <div>
        <button
            @click="openModal('login')"
            class="bg-blue-500 px-4 py-2 text-white"
        >
            Login
        </button>
        <button
            @click="openModal('register')"
            class="bg-green-500 px-4 py-2 text-white ml-2"
        >
            Register
        </button>

        <AuthModal
            :show="showModal"
            :mode="mode"
            @close="showModal = false"
            @switch-mode="toggleMode"
        />
    </div>
</template>

<script setup>
import { ref } from "vue";
import AuthModal from "@/Components/AuthModal.vue";

const showModal  = ref(false);
const mode = ref("null");

const openModal = (authMode) => {
    mode.value = authMode;
    showModal.value = true;
};

const toggleMode = () => {
    mode.value = mode.value === "login" ? "register" : "login";
};
</script>
