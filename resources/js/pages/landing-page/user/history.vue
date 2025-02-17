<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';
import JwtService from "@/core/services/JwtService";
import type { Booking } from "@/types"
import axios from 'axios';
import { useRoute } from 'vue-router';

const route = useRoute();
const bookings = ref<Booking[]>([]);
const loading = ref(false);
const currentPage = ref(1);
const totalPages = ref(0);
const searchQuery = ref<string>('');
const perPage = ref(10);

const formatPrice = (value: number): string => {    
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
    }).format(value);
};

const getData = async (page = 1) => {
    if (!JwtService.getToken()) {
        console.log('Token not found');
        return;
    }

    loading.value = true;
    try {
        const response = await axios.post(`/master/booking/history`, {
            page,
            per: perPage.value,
            search: searchQuery.value
        });
        
        bookings.value = response.data.data;
        totalPages.value = response.data.last_page;
        currentPage.value = page;
    } catch (error) {
        console.error('Error fetching booking history:', error);
    } finally {
        loading.value = false;
    }
};

// Watch for search query changes
watch(searchQuery, () => {
    getData(1);
});

onMounted(() => {
    getData(1);
});
</script>

<template>
    <main class="bg-light min-vh-100 py-4">
        <!-- Breadcrumb -->
        <div class="container">
            <nav aria-label="breadcrumb" class="bg-white p-3 rounded shadow-sm">
                <h2 class="text-dark">Your Booking History</h2>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <router-link to="/landing/page" class="text-decoration-none text-primary">Home</router-link>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">History</li>
                </ol>
            </nav>
        </div>

        <!-- Search Bar -->
        <div class="container my-4">
            <div class="d-flex justify-content-end">
                <input 
                    type="search" 
                    v-model="searchQuery" 
                    placeholder="Search for your order..." 
                    class="form-control w-50 shadow-sm"
                >
            </div>
        </div>

        <!-- Loading Indicator -->
        <div v-if="loading" class="d-flex justify-content-center align-items-center min-vh-50">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

        <!-- Bookings Table -->
        <div v-else class="container">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-primary">
                                <tr>
                                    <th>Booking Number</th>
                                    <th>Villa Name</th>
                                    <th>Check In</th>
                                    <th>Check Out</th>
                                    <th>Total Price</th>
                                    <th>Payment Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="booking in bookings" :key="booking.id">
                                    <td>{{ booking.booking_number }}</td>
                                    <td>{{ booking.villa?.nama }}</td>
                                    <td>{{ new Date(booking.booking_date).toLocaleDateString() }}</td>
                                    <td>{{ new Date(booking.end_date).toLocaleDateString() }}</td>
                                    <td class="fw-semibold text-primary">{{ formatPrice(booking.total_price) }}</td>
                                    <td>
                                        <span :class="[
                                            'badge px-3 py-2',
                                            { 'bg-warning text-dark': booking.payment_status === 'Draft' },
                                            { 'bg-success text-white': booking.payment_status === 'Completed' },
                                            { 'bg-danger text-white': booking.payment_status === 'Failed' }
                                        ]">
                                            {{ booking.payment_status }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <nav v-if="totalPages > 1" class="d-flex justify-content-center mt-3">
                        <ul class="pagination">
                            <li class="page-item" :class="{ disabled: currentPage === 1 }">
                                <button class="page-link" @click.prevent="getData(currentPage - 1)">Previous</button>
                            </li>
                            <li v-for="page in totalPages" :key="page" class="page-item" :class="{ active: page === currentPage }">
                                <button class="page-link" @click.prevent="getData(page)">{{ page }}</button>
                            </li>
                            <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                                <button class="page-link" @click.prevent="getData(currentPage + 1)">Next</button>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </main>
</template>

<style scoped>
.min-vh-50 {
    min-height: 50vh;
}
</style>