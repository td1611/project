
<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ModalComfirm from '@/Components/ModalComfirm.vue';
import { ref, onMounted, watchEffect, computed } from 'vue';
import axios from 'axios'
import { ElMessage } from 'element-plus'
import { FwbButton } from 'flowbite-vue'


// fetch data
const categories = ref([])

// paginate
const currentPage = ref(1);
const totalPages = ref(1);

// getCategories
// const fetchCategories = async () => {
//     try {
//         const response = await axios.get(route('admin.categories.getList'));
//         categories.value = response.data.data;
//         console.log(response.data.meta);


//     } catch (error) {
//         ElMessage({
//             showClose: true,
//             message: 'Error fetching categories' + error,
//             type: 'error',
//         })
//     }
// };


const fetchCategories = async () => {
    try {
        const response = await axios.get(route('admin.categories.getList'), {
            params: {
                page: currentPage.value,
            },
        });
        // console.log(response.data.meta);
        categories.value = response.data.data;
        totalPages.value = response.data.meta.last_page;
    } catch (error) {
        ElMessage({
            showClose: true,
            message: 'Error fetching categories' + error,
            type: 'error',
        });
    }
};



const prevPage = () => {
    if (currentPage.value > 1) {
        currentPage.value--;
        fetchCategories();
    }
};

const goToPage = (page) => {
    if (page >= 1 && page <= totalPages.value) {
        currentPage.value = page;
        fetchCategories();
    }
};

const nextPage = () => {
    if (currentPage.value < totalPages.value) {
        currentPage.value++;
        fetchCategories();
    }
};

const canGoPrev = computed(() => currentPage.value > 1);
const canGoNext = computed(() => currentPage.value < totalPages.value);

watchEffect(() => {
    fetchCategories();
});

// detele
const showModal = ref(false);
const selectedCategoryId = ref(null);
const selectedCategoryTitle = ref(null);

const closeModal = () => {
    showModal.value = false;
};

const setSelectedCategoryIdAndShowModal = (categoryId, categoryTitle) => {
    showModal.value = true;
    selectedCategoryId.value = categoryId;
    selectedCategoryTitle.value = categoryTitle;
};

const deleteCategory = async () => {
    try {
        const response = await axios.delete(route('admin.categories.destroy', selectedCategoryId.value));
        console.log(response.data);
        ElMessage({
            showClose: true,
            message: `Category deleted successfully ${selectedCategoryTitle.value}`,
            type: 'success',
            grouping: true,
        });
        fetchCategories();
    } catch (error) {
        console.log(error);
        ElMessage({
            showClose: true,
            message: `Error deleting category ${error.response.data.message}`,
            type: 'error',
            grouping: true,
        });
    }
};

const acceptDelete = () => {
    deleteCategory()
    closeModal()
};

onMounted(() => {

    fetchCategories();
})


</script>

<template>
    <AdminLayout title="Category Index">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Index
            </h2>

        </template>

        <template #content>
            <div class="flex flex-col">
                <div class="-m-1.5 overflow-x-auto">
                    <div class="p-1.5 min-w-full inline-block align-middle">
                        <div class="border rounded-lg divide-y divide-gray-200 dark:border-gray-700 dark:divide-gray-700">
                            <div class="py-3 px-4">
                                <div class="relative max-w-xs">
                                    <label class="sr-only">Search</label>
                                    <input type="text" name="hs-table-with-pagination-search"
                                        id="hs-table-with-pagination-search"
                                        class="py-2 px-3 ps-9 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:z-10 focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-gray-400 dark:focus:ring-gray-600"
                                        placeholder="Search for items">
                                    <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-3">
                                        <svg class="size-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24"
                                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="11" cy="11" r="8" />
                                            <path d="m21 21-4.3-4.3" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="overflow-hidden">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th scope="col" class="py-3 px-4 pe-0">
                                                <div class="flex items-center h-5">
                                                    <input id="hs-table-pagination-checkbox-all" type="checkbox"
                                                        class="border-gray-200 rounded text-blue-600 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                                                    <label for="hs-table-pagination-checkbox-all"
                                                        class="sr-only">Checkbox</label>
                                                </div>
                                            </th>
                                            <th scope="col"
                                                class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">

                                            </th>
                                            <th scope="col"
                                                class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">

                                            </th>

                                            <th scope="col"
                                                class="px-6 py-3 text-end text-xs font-medium text-gray-500 uppercase">
                                                Action
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">

                                        <tr v-for="category in categories" :key="category.id">
                                            <td class="py-3 ps-4">
                                                <div class="flex items-center h-5">
                                                    <input id="hs-table-pagination-checkbox-1" type="checkbox"
                                                        class="border-gray-200 rounded text-blue-600 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                                                    <label for="hs-table-pagination-checkbox-1"
                                                        class="sr-only">Checkbox</label>
                                                </div>
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-gray-200">
                                                {{ category.title }}
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-200">
                                                {{ category.slug }}
                                            </td>

                                            <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                                <Link :href="route('admin.categories.edit', category.id)"> <el-button
                                                    type="primary">Edit</el-button> </Link>

                                                <el-button
                                                    @click="setSelectedCategoryIdAndShowModal(category.id, category.title)">Delete
                                                    Category
                                                </el-button>

                                            </td>

                                        </tr>
                                    </tbody>
                                    <ModalComfirm v-if="showModal" @close="closeModal" @accept="deleteCategory">

                                        <template #title>
                                            Delte Category {{ selectedCategoryTitle }}
                                        </template>
                                        <template #content>
                                            Are you ssure
                                        </template>
                                        <template #footer>
                                            <fwb-button color="alternative" class="mr-3" @click="closeModal">
                                                Decline
                                            </fwb-button>
                                            <fwb-button color="pink" @click="acceptDelete()">Accept</fwb-button>
                                        </template>
                                    </ModalComfirm>
                                </table>


                            </div>
                            <div class="py-1 px-4">
                                <nav class="flex items-center space-x-1">
                                    <button @click="prevPage" :disabled="!canGoPrev"
                                        class="p-2.5 inline-flex items-center gap-x-2 text-sm rounded-full text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-white/10 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">
                                        <span aria-hidden="true">«</span>
                                        <span class="sr-only">Previous</span>
                                    </button>

                                    <!-- Hiển thị các nút trang -->
                                    <button v-for="page in totalPages" :key="page" @click="goToPage(page)"
                                        :class="{ 'bg-red-300': currentPage === page, }"
                                        class="min-w-[40px] flex justify-center items-center text-gray-800 hover:bg-gray-100 py-2.5 text-sm rounded-full disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-white/10 bg-gray-100"
                                        aria-current="page">{{ page }}</button>

                                    <button @click="nextPage" :disabled="!canGoNext"
                                        class="p-2.5 inline-flex items-center gap-x-2 text-sm rounded-full text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-white/10 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">
                                        <span class="sr-only">Next</span>
                                        <span aria-hidden="true">»</span>
                                    </button>
                                </nav>

                                <!-- <nav class="flex items-center space-x-1">
                                    <button type="button"
                                        class="p-2.5 inline-flex items-center gap-x-2 text-sm rounded-full text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-white/10 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">
                                        <span aria-hidden="true">«</span>
                                        <span class="sr-only">Previous</span>
                                    </button>
                                    <button type="button"
                                        class="min-w-[40px] flex justify-center items-center text-gray-800 hover:bg-gray-100 py-2.5 text-sm rounded-full disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-white/10"
                                        aria-current="page">1</button>
                                 
                               
                                    <button type="button"
                                        class="p-2.5 inline-flex items-center gap-x-2 text-sm rounded-full text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-white/10 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">
                                        <span class="sr-only">Next</span>
                                        <span aria-hidden="true">»</span>
                                    </button>
                                </nav> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>

    </AdminLayout>
</template>