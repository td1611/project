<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { ElMessage, ElNotification } from 'element-plus'
import axios from "axios";

const props = defineProps({
    categories: Object,
});

const form = useForm({
    title: "",
});

async function submit() {
    try {
        const response = await axios.post(route('admin.categories.store'), form);
        console.log(response);
        form.reset();
        ElNotification({
            title: 'Success',
            message: response.data.message,
            type: 'success',
            grouping: true
        })

    } catch (error) {

        ElMessage({
            showClose: true,
            message: error.response.data.message,
            type: 'error',
            grouping: true,
        })
    }
}
</script>

<template>
    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create Category
            </h2>
        </template>

        <template #content>
            <form class="mt-14" @submit.prevent="submit">
                <div class="mb-5">
                    <InputLabel for="title" value="Title" />
                    <TextInput id="title" type="text" class="mt-1 block w-full" v-model="form.title" autofocus />
                    <InputError class="mt-2" :message="form.errors.title" />
                    {{ }}
                </div>

                <button type="submit"
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    ADD
                </button>
            </form>
        </template>
    </AdminLayout>
</template>
