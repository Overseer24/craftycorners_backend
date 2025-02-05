<script setup>
const model = defineModel({
    type:null,
    required:true
});
defineProps({
    name: {
        type: String,
        required: true,
    },
    type: {
        type: String,
        default: "text",
    },
    message: {
        type: String,
    },
    label: {
        type: String,
        default: "value",
    },
});
</script>

<template>
    <div class="mb-4 md:col-span-2">
        <label class="block text-sm font-medium text-gray-700">
            {{ name }}
        </label>
        <input
            :type="type"
            v-model="model"
            class="w-full px-3 py-2 border rounded-lg"
            :class="{'!border-red-500': message}"
           :placeholder="`Enter your ${label.toLowerCase()}`"


        />
        <small class="error" v-if="message">{{ message }}</small>
    </div>
</template>
