<template>
    <fwb-modal>
        <template #header>
            <div class="flex items-center text-lg">
                <slot name="title"></slot>
            </div>
        </template>
        <template #body>
            <slot name="content" />
        </template>
        <template #footer>
            <div class="flex justify-end">
                <slot name="footer" />
            </div>
        </template>
    </fwb-modal>
</template>
  
<script setup>
import { FwbButton, FwbModal } from 'flowbite-vue'

// const emits = defineEmits(['accept', 'close']);

// const accept = () => {
//     emits('accept');
// };

// const close = () => {
//     emits('close');
// };

</script>