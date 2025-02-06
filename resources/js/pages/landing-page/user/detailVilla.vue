<script setup lang="ts">
import { onMounted, ref, defineComponent, computed } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "@/libs/axios";
import { Splide, SplideSlide } from '@splidejs/vue-splide';
import '@splidejs/vue-splide/css/default';

const route = useRoute();
const router = useRouter();
const villa = ref({});
const Images = ref([]);
const fasilitas = ref([]);
const city = ref({});

const carouselOptions = {
    rewind: true,
    autoplay: true,
    interval: 3000,
    pagination: false,
    arrows: true,
    height: '500px'
};

const fetchVilla = async (id) => {
    try {
        const response = await axios.get(`/master/villa/${id}`)
        villa.value = response.data.Villa
        await fetchFas(response.data.VillaFas)
        await fetchCity(villa.value.kota_id)
    } catch (err){
        console.error(err);
    }
}

const fetchFas = async (VillaFas) => {
    try {
        for (const fas of VillaFas) {
            const response = await axios.get(`/master/villaFasilitas/${fas}`)
            fasilitas.value.push(response.data.VillaFasilitas)
        }
    } catch (err) {
        console.error(err)
    }
}

const fetchCity = async (cityId) => {
    try {
        const response = await axios.get(`/indonesia/cities`)
        const data = response.data.data
        city.value = data.find((item) => item.code == cityId)
    } catch (err) {
        console.error(err)
    }
}

const fetchVillaImage = async (uuid) => {
    try {
        const response = await axios.post('/master/villa_image', { uuid })
        Images.value = response.data.data
    } catch (err)  {
        console.error(err)
    } 
}

const getUrl = () => {
    localStorage.setItem('lastWeb', window.location.href)
}

onMounted(() => {
    fetchVilla(route.params.uuid)
    fetchVillaImage(route.params.uuid)
})

const uuid = route.params.uuid
</script>

<template>
    <div class="villa-detail-container">
        <div class="container py-5">
            <div class="card shadow-lg rounded-4">
                <div class="card-body p-4 p-lg-5">
                    <!-- Navigation and Booking Section -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <router-link :to="{name: 'landing.villa', query: { uuid: villa.kota_id }}" 
                            class="btn btn-light rounded-pill hover-shadow">
                            <i class="las la-chevron-circle-left fs-4 me-2"></i>
                            <span>Back</span>
                        </router-link>
                        <router-link :to="'/user/confirm/' + route.params.uuid" @click="getUrl">
                            <button class="btn btn-primary rounded-pill px-4 py-2 hover-shadow"
                                style="background-color: #87CDEE; border: none;">
                                <i class="fas fa-calendar-check me-2"></i>
                                Book Now
                            </button>
                        </router-link>
                    </div>

                    <!-- Villa Information -->
                    <div class="villa-info mb-5">
                        <h1 class="display-5 fw-bold mb-3">{{ villa.nama }}</h1>
                        <div class="location-info d-flex align-items-center text-muted mb-4">
                            <i class="fa-solid fa-location-dot fs-5 me-2"></i>
                            <span>{{ villa.alamat }}, {{ city.name }}</span>
                        </div>
                        
                        <!-- Quick Info Cards -->
                        <div class="row g-3 mb-4 justify-content-center">
                            <div class="col-md-3">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <i class="fas fa-money-bill-wave mb-2 fs-4"></i>
                                        <h6 class="mb-0">Price</h6>
                                        <p class="mb-0 fw-bold">{{ villa.price }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <i class="fas fa-bed mb-2 fs-4"></i>
                                        <h6 class="mb-0">Rooms</h6>
                                        <p class="mb-0 fw-bold">{{ villa.total_kamar }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <i class="fas fa-users mb-2 fs-4"></i>
                                        <h6 class="mb-0">Capacity</h6>
                                        <p class="mb-0 fw-bold">{{ villa.kapasitas }}</p>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="col-md-3">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <i class="fas fa-star mb-2 fs-4"></i>
                                        <h6 class="mb-0">Rating</h6>
                                        <p class="mb-0 fw-bold">4.5/5</p>
                                    </div>
                                </div>
                            </div> -->
                        </div>
                    </div>

                    <!-- Image Carousel -->
                    <div class="villa-images mb-4">
                        <div v-if="Images.length > 0" class="carousel-container">
                            <Splide :options="carouselOptions" aria-label="Villa Images">
                                <SplideSlide v-for="(image, index) in Images" :key="index">
                                    <div class="carousel-image-wrapper">
                                        <img :src="`/storage/${image.image}`" class="carousel-image" :alt="`Villa image ${index + 1}`">
                                    </div>
                                </SplideSlide>
                            </Splide>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="villa-description">
                        <h4 class="mb-3">
                            <i class="fa-solid fa-pen"></i>
                            About this Villa</h4>
                        <p class="text-muted">{{ villa.deskripsi }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.villa-detail-container {
    background-color: #f8f9fa;
    min-height: 100vh;
}

.hover-shadow {
    transition: all 0.3s ease;
}

.hover-shadow:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.carousel-container {
    border-radius: 1rem;
    overflow: hidden;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
}

.carousel-image-wrapper {
    width: 100%;
    height: 500px;
    overflow: hidden;
}

.carousel-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
}

.villa-info .card {
    transition: all 0.3s ease;
}

.villa-info .card:hover {
    transform: translateY(-5px);
}

.location-info {
    color: #6c757d;
}

@media (max-width: 768px) {
    .carousel-image-wrapper {
        height: 300px;
    }
}
</style>