<template>
    <div> {{ tokens }}!</div>
</template>

<script setup>
    defineProps({
        tokens: String
    });
</script>