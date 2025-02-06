<script setup lang="ts">
import axios from "@/libs/axios";
import type { Villa } from "@/types";
import { useRouter, useRoute } from "vue-router";
import { ref, onMounted, computed } from "vue";
import { useQuery } from "@tanstack/vue-query";

const route = useRoute();
const router = useRouter();
const searchQuery = ref<string>("");

const { data, isLoading } = useQuery({
    queryKey: ['villa', route.query.uuid],
    queryFn: () => axios.post('/master/villa/city', { city: route.query.uuid }).then(res => res.data),
    onError: (err: any) => console.error(err.response.data.message),
    enabled: !!route.query.uuid
})

const searchVilla = computed(() => {
    if (!searchQuery.value) {
        return data.value.data;
    }
    const query = searchQuery.value.toLowerCase();
    return data.value.data.filter(villa => {
        return (
            villa.nama.toLowerCase().includes(query) ||
            villa.deskripsi.toLowerCase().includes(query) ||
            villa.alamat.toLowerCase().includes(query)
        );
    });
});

</script>


<template>
    <div v-if="!isLoading" class="ms-10 mt-20 py-15">
        <div class="pb-10 fs-1 fw-bold">showing villa Search Results in {{ data.city.name }}</div>
        <div class="input-group w-50 mb-10">
            <input class="form-control w-50" type="text" v-model="searchQuery" />
            <button type="button" class="btn btn-danger input-group-text" @click="searchQuery = ''">clear!</button>
        </div>
        <div v-if="searchVilla.length > 0">
            <router-link class="card mb-3 w-75 pb-5" v-for="(item, index) in data.data" :key="index" :to="{name: 'landing.detail', query: { id_city: route.query.uuid }, params: { uuid: item.uuid }}">
             <div class="bg-gray-200 p-15 rounded">
                <div class="col-md-4 h-75 w-50">
                    <img :src="`/storage/${item.villa_image[0]?.image}`" class="img-fluid rounded" alt="Villa Image">
                </div>
                <div class="col-md-10">
                    <div class="card-body max-w-xs rounded overflow-hidden shadow-lg mt-10 bg-secondary">
                        <h3 class="card-title p-2"><i class="fa-solid fa-building-user" style="color: #0000FF"></i> {{
                            item.nama }}</h3>
                        <h5 class="card-text p-2"> <i class="fa-solid fa-pen" style="color: #0000FF"></i> {{
                            item.deskripsi }}</h5>
                        <p class="card-text p-2"> <i class="fa-solid fa-location-dot" style="color: #0000FF"></i> {{
                            item.alamat }}</p>
                    </div>
                </div>
             </div>
            </router-link>
        </div>
    </div>
    <div v-else>
        loading...
    </div>
</template>