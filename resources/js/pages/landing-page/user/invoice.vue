<script setup lang="ts">
import { onMounted, ref, computed } from "vue";
import { useRouter, useRoute } from "vue-router";
import axios from "axios";
import jsPDF from "jspdf";
import autoTable from 'jspdf-autotable'
import { currency } from "@/libs/utils"
import { auto } from "@popperjs/core";


const route = useRoute();
const datas = ref([]);
const router = useRouter();

const formatPrice = (value: number): string => {
  return new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
  }).format(value);
};

const formatted = computed(() => {
  return formatPrice(datas.value.total_price)
});

const getBooking = async (uuid) => {
    try {
        const response = await axios.get(`/master/booking_room/${uuid}`)
    datas.value = response.data.booking_room
  } catch (err) {
    console.error(err)
    }
}






</script>

<template>

</template>