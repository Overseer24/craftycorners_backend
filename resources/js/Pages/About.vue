<!-- <script setup>
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
</template> -->
<script setup></script>

<template>
    <div>
        <!-- Hero Section -->
        <div
            class="relative flex items-center justify-center h-screen bg-gray-900"
        >
            <!-- SVG Background -->
            <div class="absolute inset-0 -z-10">
                <svg
                    class="w-full h-full"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 1440 320"
                >
                    <path
                        fill="#4f46e5"
                        fill-opacity="1"
                        d="M0,64L40,69.3C80,75,160,85,240,117.3C320,149,400,203,480,213.3C560,224,640,192,720,170.7C800,149,880,139,960,122.7C1040,107,1120,85,1200,101.3C1280,117,1360,171,1400,197.3L1440,224L1440,0L1400,0C1360,0,1280,0,1200,0C1120,0,1040,0,960,0C880,0,800,0,720,0C640,0,560,0,480,0C400,0,320,0,240,0C160,0,80,0,40,0L0,0Z"
                    ></path>
                </svg>
            </div>

            <!-- Weather App Container -->
            <div
                class="bg-white/80 backdrop-blur-lg shadow-2xl border border-gray-300 rounded-2xl p-6 max-w-md w-full text-center"
            >
                <h1 class="text-2xl font-bold text-gray-800">Weather App</h1>
                <p class="text-gray-600">Current temperature: 25°C</p>
            </div>
        </div>

        <!-- Next Section -->
        <div
            ref="nextSection"
            class="min-h-screen flex flex-col items-center justify-center bg-gray-100 py-10"
        >
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
            <p class="text-2xl font-semibold text-gray-800">
                Next Section Content
            </p>
        </div>
    </div>
</template>
