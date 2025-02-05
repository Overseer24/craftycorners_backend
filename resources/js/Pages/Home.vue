<script setup>
import Layout from "../Layouts/Layout.vue";

defineOptions({
    layout: Layout,
});
</script>

<template>
    <Head :title="` | ${$page.component}`" />
    <div>
        <h1>Welcome to your new Inertia app!</h1>
        <p>
            This is the default welcome page for your application. You may
            customize it to fit your needs.
        </p>
    </div>
</template>
