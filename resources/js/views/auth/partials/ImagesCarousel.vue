<template>
    <q-carousel
        animated
        v-model="slide"
        infinite
        :autoplay="autoplay"
        transition-prev="fade"
        transition-next="fade"
        @mouseenter="autoplay = false"
        @mouseleave="autoplay = true"
        class="w-full h-full rounded-lg"
    >
        <template #control>
            <q-btn flat dense round color="black" text-color="primary" />
        </template>
        <q-carousel-slide :name="1" img-src="/img/login-bg2.png" />
        <q-carousel-slide :name="2" img-src="/img/login-bg.png" />
        <q-carousel-slide :name="3" img-src="/img/login-bg3.png" />
    </q-carousel>
</template>
<script setup>
import { ref } from 'vue';


const slide = ref(1);
const autoplay = ref(true);
</script>
