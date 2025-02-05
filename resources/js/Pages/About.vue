<script setup>
    import Layout from '../Layouts/Layout.vue';

    defineOptions({
        layout: Layout,
    });

</script>



<template>
    <Head :title = "`| ${$page.component}`"/>
    <div>
        <h1>About</h1>
        <p>
            This is the about page for your application. You may customize it to fit your needs.
        </p>
    </div>
</template>
