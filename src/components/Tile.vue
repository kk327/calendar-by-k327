<script setup>
    const props = defineProps([
        "dayNumber"
    ]);

</script>

<template>
    <button @click="$emit('editNote', dayNumber - 1)">
        {{ dayNumber }}
    </button>
</template>

<style scoped>
    button {
        width: calc(100% / 7);
        height: calc(100% / var(--rows));
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 175%;
        font-weight: 600;
        background-color: white;
        cursor: pointer;
        border: 1px solid rgb(220, 220, 220);
        color: rgb(32, 32, 32);
        -webkit-text-stroke: 1.5px white;
    }

    button:disabled {
        background-color: rgb(32, 32, 32);
        color: rgb(120, 120, 120);
        cursor: default;
        -webkit-text-stroke: 1.5px rgb(32, 32, 32);
    }
</style>