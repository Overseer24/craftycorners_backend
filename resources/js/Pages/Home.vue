<script setup>
import Layout from "../Layouts/Layout.vue";
import { ref } from "vue";

defineOptions({
    layout: Layout,
});

const hideCursor = ref(false);
let timeout;

const resetTimer = () => {
    hideCursor.value = false;
    console.log("Cursor visible:", hideCursor.value);
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        hideCursor.value = true;
        console.log("Cursor hidden:", hideCursor.value);
    }, 3000);
};
</script>

<template>
    <Head :title="` | ${$page.component}`" />
    <!-- <div :class="{ 'cursor-none': hideCursor, 'bg-amber-100':hideCursor}" @mousemove="resetTimer"
    class="h-screen w-full  "> -->
    <div
        class="h-screen w-full flex  overflow-hidden"
    >
        <h1>Welcome to your new Inertia app!!</h1>
        <p>
            This is the default welcome page for your application. You may
            customize it to fit your needs.
        </p>
    </div>

    <!-- <div class="cursor-none bg-red-500 p-5 text-white">
        If Tailwind is working, this box should have no cursor.
    </div> -->
</template>
