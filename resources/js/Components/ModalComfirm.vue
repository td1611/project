<template>
    <div class="modal" v-show="showModal" aria-modal="true">
        <div class="modal-content">
            <p>{{ message }}</p>
            <button @click="handleConfirmation" type="button" aria-label="Xác nhận">
                {{ confirmLabel || "Xác nhận" }}
            </button>
            <button @click="handleCancellation" type="button" aria-label="Hủy">
                {{ cancelLabel || "Hủy" }}
            </button>
        </div>
    </div>
</template>
  
<script setup>
import { ref, computed } from 'vue';

const showModal = ref(false);
const message = ref('');
const confirmLabel = ref('');
const cancelLabel = ref('');
const onConfirm = ref(() => { });
const onCancel = ref(() => { });

const isConfirmDisabled = computed(() => {
    // Logic để xác định nút xác nhận có bị vô hiệu hóa hay không
    return false;
});

const isCancelDisabled = computed(() => {
    // Logic để xác định nút hủy có bị vô hiệu hóa hay không
    return false;
});

const handleConfirmation = () => {
    // Xử lý logic xác nhận
    if (!isConfirmDisabled.value) {
        onConfirm.value();
        showModal.value = false;
    }
};

const handleCancellation = () => {
    // Xử lý logic hủy
    if (!isCancelDisabled.value) {
        onCancel.value();
        showModal.value = false;
    }
};
</script>
  
<style scoped>
/* ... your styles ... */
</style>
  