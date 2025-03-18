<script setup>
import AuthLayout from "../Layouts/AuthLayout.vue";
defineOptions({
    layout: AuthLayout,
});


</script>


<template>
    <div class="">
        <h1 class="title">
            Welcome back {{ $page.props.auth.user.first_name }}
        </h1>
    </div>
    <div v-if="$page.props.auth.user">
        <Link :href="route('logout')" class="nav-link" method="post" as="button"
            >Logout</Link
        >
    </div>
</template>
