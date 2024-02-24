<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import { Head, useForm, } from "@inertiajs/vue3";
import { ElMessage, ElNotification } from 'element-plus'
import axios from "axios";
import { ref } from 'vue';

import { router } from '@inertiajs/vue3'


const props = defineProps({
    category: {
        type: Object,
        default: () => ({}),
    },
});


const form = useForm({
   
    title: props.category.data.title,
    image: props.category.data.image,
});


async function submit() {
    try {
        const response = await axios.put(route('admin.categories.update', props.category.data.id), form);
        console.log(response.data);
        ElNotification({
            title: 'Success',
            message: response.data.message,
            type: 'success',
        })
        const indexUrl = route('admin.categories.index');
        router.replace(indexUrl);

    } catch (error) {
        console.log(error);
        const errorMessage = error.response?.data?.message || 'Something went wrong.';
        ElMessage({
            showClose: true,
            message: errorMessage,
            type: 'error',
            grouping: true,
        })
    }
}
const selectedImage = ref('');

function handleShowImage(event) {
    const file = event.target.files[0];
    file ? selectedImage.value = URL.createObjectURL(file) : selectedImage.value = null

}



</script>

<template>
    <AdminLayout title="Category Edit">

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Category
            </h2>
        </template>

        <template #content>
            <form class="mt-14" @submit.prevent="submit">
                <div class="mb-5">
                    <InputLabel for="title" value="Title" />
                    <TextInput id="title" type="text" class="mt-1 block w-full" v-model="form.title" autofocus
                        name="title" />
                    <InputError class="mt-2" :message="form.errors.title" />
                </div>
                <div class="mb-5">
                    <InputLabel for="image" value="Image" />
                    <input id="image" type="file" class="mt-1 block w-full" @input="form.image = $event.target.files[0]"
                        @change="handleShowImage" />

                    <InputError class="mt-2" :message="form.errors.image" />
                </div>
                <div style="width:30%">
                    <img :src="selectedImage ? selectedImage : form.image" alt="Selected Image">
                </div>
                <button type="submit" :disabled="form.processing"
                    class="mt-3 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    Edit
                </button>
            </form>
        </template>
    </AdminLayout>
</template>
