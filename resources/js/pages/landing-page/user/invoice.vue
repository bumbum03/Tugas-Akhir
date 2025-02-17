<script setup lang="ts">
import { onMounted, ref, computed } from "vue";
import { useRouter, useRoute } from "vue-router";
import axios from "axios";
import jsPDF from "jspdf";
import autoTable from 'jspdf-autotable';
import { currency } from "@/libs/utils";
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
    datas.value = response.data.Booking
  } catch (err) {
    console.error(err)
    }
}
  
const printPdf = () => {
  const doc = new jsPDF();
  let yPos = 20;

// Set header invoice
doc.setFont('helvetica');
doc.setFontSize(25);
doc.setTextColor(40); // Warna teks hitam
doc.text('Invoice', 105, yPos, { align: "center" });

yPos += 15;

// Set Data Booking
doc.setFont('times');
doc.setFontSize(15);
doc.setTextColor(0); // Warna teks hitam

const tableRows = [
  ["Booking Number", datas.value.booking_number],
  ["Name", datas.value.name],
  ["Email", datas.value.email],
  ["Phone Number", datas.value.phone],
  ["Check In Date", datas.value.booking_date],
  ["Check Out Date", datas.value.end_date],
  ["Payment Type", datas.value.payment_type],
  ["Price", currency(datas.value.total_price)],
];

doc.autoTable({
    startY: yPos + 10,
    head: [['Information', 'Details']],
    body: tableRows,
    theme: 'grid',
    styles: {
      fontStyle: 'medium',
      cellWidth: auto,
    },
    headStyles: {
      fillColor: [70, 70, 70],
      textColor: 255,
      fontStyle: 'bold',
      cellPadding: 4
    },
    bodyStyles: {
      cellPadding: 3
    },
    margin: { top: 100 },
  });

  // Simpan dokumen PDF
  doc.save('invoice.pdf');
};

onMounted(() => {
  getBooking(route.params.uuid)
})
</script>

<template>
<div class="bg-secondary main">
  <div class="container w-100 shadow-lg">
    <div class="text-center">
      <h1 class="invoice py-5">Invoice</h1>
    </div>
    <div class="table-responsive">
      <table class="table table-bordered">
        <thead class="" style="background-color: #454545;">
          <tr>
            <th scope="col" style="color: white;">Information</th>
            <th scope="col" style="color: white;">Details</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Booking Number</td>
            <td>{{ datas.booking_number }}</td>
          </tr>
          <tr>
            <td>Name</td>
            <td>{{ datas.name }}</td>
          </tr>
          <tr>
            <td>Email</td>
            <td>{{ datas.email }}</td>
          </tr>
          <tr>
            <td>Phone Number</td>
            <td>{{ datas.phone }}</td>
          </tr>
          <tr>
            <td>Check-in Date</td>
            <td>{{ datas.booking_date }}</td>
          </tr>
          <tr>
            <td>Check-out Date</td>
            <td>{{ datas.end_date }}</td>
          </tr>
          <tr>
            <td>Payment Status</td>
            <td>{{ datas.payment_status }}</td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center fw-bold text-dark-emphasis">
      <div class="final ps-4">
          <span>Total Price: {{ formatted }}</span>
      </div>

      <button class="btn btn-dark-secondary my-5">
        <a href="/landing/page" class="text-dark-emaphasis">Return to the page</a>
      </button>
    </div>
  </div>
</div>
<button class="btn btn-md text-light-emphasis bg-gray-300 mt-10 text-capitalize font-sans" @click="printPdf">
  Print as Pdf
</button>
</template>

<style>
body {
  font-family: Arial, sans-serif;
  justify-content: center;
  align-items: center;
  width: 100%;
}

.main {
  width: 50vw;
}

@media (max-width: 884px) {
  .main {
    width: 75vw;
  }

  .card-footer {
    display: flex;
    flex-direction: row;
  }
}
</style>