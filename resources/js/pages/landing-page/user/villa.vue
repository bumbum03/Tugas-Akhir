<script setup lang="ts">
import axios from "@/libs/axios";
import type { Villa } from "@/types";
import { useRouter, useRoute } from "vue-router";
import { ref, onMounted, computed } from "vue";
import { useQuery } from "@tanstack/vue-query";
import { currency } from "@/libs/utils";

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
    <div class="container py-5 mt-20">
        <!-- Loading State -->
        <div v-if="isLoading" class="d-flex justify-content-center align-items-center" style="height: 300px;">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

        <div v-else>
            <!-- Header Section -->
            <div class="mb-10">
                <h1 class="display-5 fw-bold mb-4">Villa Di {{ data.city.name }}</h1>
                
                <!-- Search Bar -->
                <div class="row d-flex  mb-5">
                    <div class="col-md-6 justify-content-end">
                        <div class="input-group">
                            <input
                                type="text"
                                v-model="searchQuery"
                                class="form-control form-control-lg"
                                placeholder="Search villas..."
                            />
                            <button
                                v-if="searchQuery"
                                @click="searchQuery = ''"
                                class="btn btn-outline-secondary"
                                type="button"
                            >
                                Clear
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Villa Cards -->
            <div v-if="searchVilla.length > 0" class="row g-4">
                <div v-for="(item, index) in searchVilla" :key="index" class="col-md-6 col-lg-4">
                    <router-link 
                        :to="{ name: 'landing.detail', query: { id_city: route.query.uuid }, params: { uuid: item.uuid }}"
                        class="text-decoration-none"
                    >
                        <div class="card h-100 shadow-sm hover-shadow">
                            <!-- Image -->
                            <div class="position-relative">
                                <img
                                    :src="`/storage/${item.villa_image[0]?.image}`"
                                    class="card-img-top"
                                    style="height: 200px; object-fit: cover;"
                                    alt="Villa Image"
                                />
                            </div>

                            <!-- Card Body -->
                            <div class="card-body">
                                <h5 class="card-title mb-3 text-primary">
                                    <i class="fa-solid fa-building-user me-2"></i>
                                    {{ item.nama }}
                                </h5>
                                
                                <p class="card-text text-muted mb-3">
                                    <i class="fa-solid fa-pen me-2"></i>
                                    {{ item.deskripsi }}
                                </p>

                                <p class="card-text text-muted">
                                    <i class="fa-solid fa-location-dot me-2"></i>
                                    {{ item.alamat }}
                                </p>

                                <h4 class="text-primary mt-3 mb-3">
                                    <i class="fa-solid fa-tag me-2"></i>
                                    {{ currency(item.price) }}
                                </h4>

                                <!-- Features -->
                                <div class="border-top pt-3">
                                    <div class="row text-muted">
                                        <div class="col-6">
                                            <i class="fa-solid fa-users me-2"></i>
                                            {{ item.kapasitas }} guests
                                        </div>
                                        <div class="col-6 text-end">
                                            <i class="fa-solid fa-door-closed me-2"></i>
                                            {{ item.total_kamar }} rooms
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </router-link>
                </div>
            </div>

            <!-- No Results -->
            <div v-else class="text-center py-5">
                <h3 class="text-muted">No villas found matching your search criteria</h3>
            </div>
        </div>
    </div>
</template>

<style scoped>
.hover-shadow {
    transition: all 0.3s ease;
}

.hover-shadow:hover {
    transform: translateY(-5px);
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
}

.card {
    border: none;
    border-radius: 0.5rem;
}

.card-img-top {
    border-top-left-radius: 0.5rem;
    border-top-right-radius: 0.5rem;
}
</style>