<script setup>
const model = defineModel({
    type: null,
    required: true,
});

defineProps({
    id:{
        type: String
    },

    name: {
        type: String,
        required: true,
    },
    message: {
        type: String,
    },
    label: {
        type: String,
        default: "value",
    },
    options: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <div class="mb-4 md:col-span-2">
        <label class="block text-sm font-medium text-gray-700">
            {{ name }}
        </label>
        <select
            class="w-full px-3 py-2 border rounded-lg"
            v-model="model"
            :class="{ '!border-red-500': message }"
        >
            <!-- <option value="" disabled>{{name}}</option> -->
            <option v-for="option in options" :key="option.value" :value="option.value">
                {{ option.label }}
            </option>

        </select>
        <small class="error" v-if="message">{{ message }}</small>
    </div>
</template>
