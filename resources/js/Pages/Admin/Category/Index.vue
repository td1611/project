
<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ModalComfirm from '@/Components/ModalComfirm.vue';
import { ref, onMounted, watchEffect, computed, watch } from 'vue';
import axios from 'axios'
import { Delete, Edit, } from '@element-plus/icons-vue'
import { ElMessage } from 'element-plus'
import { FwbButton } from 'flowbite-vue'
import { initFlowbite } from 'flowbite'

// fetch data
const categories = ref([])
const showData = ref(false);


// trash
const showTrash = ref(false);
const toggleShowTrash = () => {
    showTrash.value = !showTrash.value;
    fetchCategories();
};

// sorting
const sorting = ref({
    column: null,
    order: 'desc',
});

const fetchCategories = async () => {
    try {
        const response = await axios.get(route('admin.categories.getList'), {
            params: {
                page: currentPage.value,
                limit: 10,
                search: searchKey.value,
                status: showTrash.value ? 'trashed' : '',
                sortColumn: sorting.value.column,
                order: sorting.value.order,
            },
        });
        // console.log(response.data.meta);
        if (response.data.data.length === 0) {
            categories.value = [];
            showData.value = false;

        } else {
            categories.value = response.data.data;
            totalPages.value = response.data.meta.last_page;
            showData.value = true;
        }
    } catch (error) {
        ElMessage({
            showClose: true,
            message: 'Error fetching categories' + error,
            type: 'error',
            grouping: true,
        });
    }
};


const handleSort = (column) => {
    if (sorting.value.column === column) {
        sorting.value.order = sorting.value.order === 'asc' ? 'desc' : 'asc';
        // console.log(sorting.value.order)
    } else {
        sorting.value.column = column;
        sorting.value.order = 'desc';
    }

    fetchCategories();
};



// search data
const searchKey = ref('');
let debounceTimeout = null;

const debounceSearch = () => {
    clearTimeout(debounceTimeout);
    debounceTimeout = setTimeout(() => {
        fetchCategories();
    }, 1000);
};

const onSearch = () => {
    currentPage.value = 1;
    debounceSearch();
};
// end search data

// paginate
const currentPage = ref(1);
const totalPages = ref(1);

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
// end paginate



// watch(() => sorting.value, fetchCategories);


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
        // console.log(response.data);
        ElMessage({
            showClose: true,
            message: `${response.data.message} ${selectedCategoryTitle.value}`,
            type: 'success',
            grouping: true,
        });
        fetchCategories();
    } catch (error) {
        console.log(error);
        ElMessage({
            showClose: true,
            message: `Error deleting category:  ${error.response.data.message}`,
            type: 'error',
            grouping: true,
        });
    }
};

const forceDelete = async () => {
    try {
        const response = await axios.post(route('admin.categories.forceDelete', selectedCategoryId.value));
        console.log(response.data);
        ElMessage({
            showClose: true,
            message: `${response.data.message} ${selectedCategoryTitle.value}`,
            type: 'success',
            grouping: true,
        });
        fetchCategories();
    } catch (error) {
        console.log(error);
        ElMessage({
            showClose: true,
            message: `Error deleting category:  ${error.response.data.message}`,
            type: 'error',
            grouping: true,
        });
    }
};

const acceptDelete = () => {
    deleteCategory()
    closeModal()
};

const acceptForceDelete = () => {
    forceDelete()
    closeModal()
};


// restore 
const restore = async (categoryId) => {
    try {
        const res = await axios.post(route('admin.categories.restore', categoryId))
        console.log(res.data.message);
        ElMessage({
            showClose: true,
            message: res.data.message,
            type: 'success',
            grouping: true,
        });
        fetchCategories();
    } catch (error) {
        console.log(error);
        ElMessage({
            showClose: true,
            message: `Error `,
            type: 'error',
            grouping: true,
        });
    }
};

watchEffect(() => {
    fetchCategories();
});


onMounted(() => {
    fetchCategories();
    initFlowbite();
})

</script>

<template>
    <AdminLayout title="Category Index">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight" v-if="!showTrash">
                Index
            </h2>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight" v-if="showTrash">
                Index <span class="text-red-200">(Trash)</span>
            </h2>
        </template>

        <template #content>
            <div class="flex flex-col">
                <div class="-m-1.5 overflow-x-auto">
                    <div class="p-1.5 min-w-full inline-block align-middle">
                        <div class="border rounded-lg divide-y divide-gray-200 dark:border-gray-700 dark:divide-gray-700">
                            <div class="py-3 px-4 flex justify-between">
                                <div class="relative max-w-xs">
                                    <label class="sr-only">Search</label>
                                    <input type="text" name="hs-table-with-pagination-search" v-model="searchKey"
                                        @input="onSearch" id="hs-table-with-pagination-search"
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

                                <div>
                                    <template v-if="showTrash">
                                        <button type="button" @click="toggleShowTrash"
                                            class="bg-gray-800 text-white rounded-l-md border-r border-gray-100 py-2 hover:bg-red-700 hover:text-white px-3">
                                            <div class="flex flex-row align-middle">
                                                <svg class="w-5 mr-2" fill="currentColor" viewBox="0 0 20 20"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd"
                                                        d="M7.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l2.293 2.293a1 1 0 010 1.414z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                                <p class="ml-2">Back</p>
                                            </div>
                                        </button>
                                    </template>
                                    <!-- Modal toggle -->

                                    <template v-else>
                                        <el-button type="primary" :icon="Delete" @click="toggleShowTrash" />
                                    </template>

                                </div>

                            </div>
                            <div class="overflow-hidden" v-if="showData">
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
                                            <th scope="col" @click="handleSort('title')"
                                                class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">
                                                <span v-if="sorting.column === 'title'">{{ sorting.order === 'asc' ? '▲' :
                                                    '▼' }}</span>
                                                Title
                                            </th>
                                            <th scope="col"
                                                class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">
                                                Slug
                                            </th>
                                            <th scope="col"
                                                class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">
                                                Image
                                            </th>
                                            <th scope="col" @click="handleSort('created_at')"
                                                class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">
                                                <span v-if="sorting.column === 'created_at'">{{ sorting.order === 'asc' ?
                                                    '▲' :
                                                    '▼' }}</span>
                                                Created_at
                                            </th>
                                    
                                            <th scope="col" v-if="showTrash" @click="handleSort('deleted_at')"
                                                class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">
                                                <span v-if="sorting.column === 'deleted_at'">{{ sorting.order === 'asc' ?
                                                    '▲' : '▼' }}</span>
                                                Deleted_at
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
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-200">
                                               <img :src="category.image" :alt="category.title">
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-200">
                                                {{ category.created_at }}
                                            </td>
                                      
                                            <td v-if="showTrash"
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-200">
                                                {{ category.deleted_at }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                                <template v-if="!showTrash">
                                                    <Link :href="route('admin.categories.edit', category.id)">
                                                    <el-button type="primary" :icon="Edit" />
                                                    </Link>
                                                    <el-button class="ml-3"
                                                        @click="setSelectedCategoryIdAndShowModal(category.id, category.title)"
                                                        type="danger" :icon="Delete" />
                                                </template>

                                                <template v-else>
                                                    <button type="button"
                                                        @click="setSelectedCategoryIdAndShowModal(category.id, category.title)"
                                                        class="text-white bg-gradient-to-r from-red-400 via-red-500 to-red-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm px-3 py-2.5 text-center me-2 mb-2">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                            fill="currentColor" class="w-6 h-6">
                                                            <path fill-rule="evenodd"
                                                                d="M16.5 4.478v.227a48.816 48.816 0 0 1 3.878.512.75.75 0 1 1-.256 1.478l-.209-.035-1.005 13.07a3 3 0 0 1-2.991 2.77H8.084a3 3 0 0 1-2.991-2.77L4.087 6.66l-.209.035a.75.75 0 0 1-.256-1.478A48.567 48.567 0 0 1 7.5 4.705v-.227c0-1.564 1.213-2.9 2.816-2.951a52.662 52.662 0 0 1 3.369 0c1.603.051 2.815 1.387 2.815 2.951Zm-6.136-1.452a51.196 51.196 0 0 1 3.273 0C14.39 3.05 15 3.684 15 4.478v.113a49.488 49.488 0 0 0-6 0v-.113c0-.794.609-1.428 1.364-1.452Zm-.355 5.945a.75.75 0 1 0-1.5.058l.347 9a.75.75 0 1 0 1.499-.058l-.346-9Zm5.48.058a.75.75 0 1 0-1.498-.058l-.347 9a.75.75 0 0 0 1.5.058l.345-9Z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                    <button type="button" v-if="showTrash" @click="restore(category.id)"
                                                        class="text-white bg-gradient-to-r from-green-400 via-green-500 to-green-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-green-300 dark:focus:ring-green-800 font-medium rounded-lg text-sm px-3 py-2.5 text-center me-2 mb-2">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                            fill="currentColor" class="w-6 h-6">
                                                            <path fill-rule="evenodd"
                                                                d="M2.25 2.25a.75.75 0 0 0 0 1.5H3v10.5a3 3 0 0 0 3 3h1.21l-1.172 3.513a.75.75 0 0 0 1.424.474l.329-.987h8.418l.33.987a.75.75 0 0 0 1.422-.474l-1.17-3.513H18a3 3 0 0 0 3-3V3.75h.75a.75.75 0 0 0 0-1.5H2.25Zm6.54 15h6.42l.5 1.5H8.29l.5-1.5Zm8.085-8.995a.75.75 0 1 0-.75-1.299 12.81 12.81 0 0 0-3.558 3.05L11.03 8.47a.75.75 0 0 0-1.06 0l-3 3a.75.75 0 1 0 1.06 1.06l2.47-2.47 1.617 1.618a.75.75 0 0 0 1.146-.102 11.312 11.312 0 0 1 3.612-3.321Z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                </template>


                                            </td>
                                        </tr>
                                    </tbody>



                                    <ModalComfirm v-if="showModal" @close="closeModal"
                                        @accept="deleteCategory, forceDelete">
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
                                            <fwb-button v-if="!showTrash" color="pink"
                                                @click="acceptDelete()">Accept</fwb-button>
                                            <fwb-button v-if="showTrash" color="pink"
                                                @click="acceptForceDelete()">Accept</fwb-button>

                                        </template>
                                    </ModalComfirm>

                                </table>


                            </div>
                            <div class="py-1 px-4" v-if="showData">
                                <nav class="flex items-center space-x-1">
                                    <button @click="prevPage" :disabled="!canGoPrev"
                                        class="p-2.5 inline-flex items-center gap-x-2 text-sm rounded-full text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-white/10 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">
                                        <span aria-hidden="true">«</span>
                                        <span class="sr-only">Previous</span>
                                    </button>

                                    <button v-for="page in totalPages" :key="page" @click="goToPage(page)"
                                        :class="{ 'bg-red-300': currentPage === page, }"
                                        class="min-w-[40px] flex justify-center items-center text-gray-800 hover:bg-gray-100 py-2.5 text-sm rounded-full disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-white/10 bg-gray-100"
                                        aria-current="page">{{ page }}
                                    </button>

                                    <button @click="nextPage" :disabled="!canGoNext"
                                        class="p-2.5 inline-flex items-center gap-x-2 text-sm rounded-full text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-white/10 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">
                                        <span class="sr-only">Next</span>
                                        <span aria-hidden="true">»</span>
                                    </button>
                                </nav>
                            </div>

                            <div v-else>
                                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">
                                    No data
                                </th>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>

    </AdminLayout>
</template>