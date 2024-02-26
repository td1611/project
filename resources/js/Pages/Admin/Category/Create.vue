<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import { ref } from 'vue';
import { Head, useForm } from "@inertiajs/vue3";
import { ElMessage, ElNotification } from 'element-plus'
import axios from "axios";

const props = defineProps({
    categories: Object,
});

const form = useForm({
    title: "",
    image: ""
});


const selectedImage = ref('');

function handleShowImage(event) {
    const file = event.target.files[0];
    file ?  selectedImage.value = URL.createObjectURL(file) :   selectedImage.value = null
   
}


async function submit() {
    try {
        const formData = new FormData();
        formData.append('title', form.title);

        if (form.image) {
            formData.append('image', form.image);
        }
        const response = await axios.post(route('admin.categories.store'), formData);
        console.log(response.data);
        selectedImage.value = null
        form.reset();
        ElNotification({
            title: 'Success',
            message: response.data.message,
            type: 'success',
            grouping: true
        })

    } catch (error) {
        console.log(error.response.data);
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
    <AdminLayout title="Create">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create Category
            </h2>
        </template>

        <template #content>
            <form class="mt-14" @submit.prevent="submit" enctype="multipart/form-data">
                <div class="mb-5">
                    <InputLabel for="title" value="Title" />
                    <TextInput id="title" type="text" class="mt-1 block w-full" v-model="form.title" autofocus />
                    <InputError class="mt-2" :message="form.errors.title" />
                </div>

                <div class="mb-5">
                    <InputLabel for="image" value="Image" />
                    <input id="image" type="file" class="mt-1 block w-full" @input="form.image = $event.target.files[0]"
                        @change="handleShowImage" />
                    <InputError class="mt-2" :message="form.errors.image" />
                </div>
                <div v-if="selectedImage" style="width:30%">
                    <h3>Selected Image</h3>
                    <img :src="selectedImage" alt="Selected Image">
                </div>
                <button type="submit"
                    class="mt-3 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    ADD
                </button>
            </form>
        </template>
    </AdminLayout>
</template>
