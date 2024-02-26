<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import { Head, useForm, } from "@inertiajs/vue3";
import { ElMessage, ElNotification } from 'element-plus'
import { ref, onMounted } from 'vue';
import axios from "axios";
import { router } from '@inertiajs/vue3'



const props = defineProps(['id']);
const category = ref({

});


// show image
const selectedImage = ref('');

function handleShowImage(event) {
    const file = event.target.files[0];
    file ? selectedImage.value = URL.createObjectURL(file) : selectedImage.value = null

}




function handleFileChange(event) {
    category.value.image = event.target.files[0];
}

const handleChangeImage = (event) => {
    handleFileChange(event)
    handleShowImage(event)
}


// get data
onMounted(async () => {
    try {
        const response = await axios.get(route('admin.categories.show', props.id));
        category.value = response.data.data
        console.log(category.value);
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
});


async function submit() {


    try {
        const formData = new FormData();
        formData.append('title', categoryTitle.value);

        if (category.value.image) {
            formData.append('image', category.value.image);
        }

        const response = await axios.put(route('admin.categories.update', props.id), formData);
        console.log(response.data);
        ElNotification({
            title: 'Success',
            message: response.data.message,
            type: 'success',
        })
        const indexUrl = route('admin.categories.index');
        // router.replace(indexUrl);

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

</script>

<template>
    <AdminLayout title="Category Edit">

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Category
            </h2>

        </template>

        <template #content>
            <form class="mt-14" @submit.prevent="submit" enctype="multipart/form-data">
                <div class="mb-5">
                    <InputLabel for="title" value="Title" />
                    <TextInput id="title" type="text" class="mt-1 block w-full"  v-model="category.title" autofocus />
                    {{ category.title }}
                    <!-- <InputError class="mt-2" :message="form.errors.title" /> -->
                </div>
                <div class="mb-5">
                    <InputLabel for="image" value="Image" />
                    <input id="image" type="file" class="mt-1 block w-full" @change="handleChangeImage" />

                    <!--  -->
                    <!-- <InputError class="mt-2" :message="form.errors.image" /> -->
                </div>
                <div style="width:30%">
                    <img :src="selectedImage ? selectedImage : category.image" alt="Selected Image">
                </div>
                <button type="submit"
                    class="mt-3 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    Edit
                </button>
            </form>
        </template>
    </AdminLayout>
</template>
