<script setup lang="ts">
import { block, currency, unblock } from '@/libs/utils';
import { onMounted, ref, computed, } from 'vue';
import * as Yup from 'yup';
import axios from '@/libs/axios';
import { toast } from 'vue3-toastify';
import { useAuthStore } from '@/stores/auth';
import type { Booking, Villa, User } from '@/types';
import { useVilla } from '@/services/useVilla';
import { useRoute, useRouter } from "vue-router";
import { ErrorMessage } from 'vee-validate';


const { user } = useAuthStore();
const router = useRouter();
const booking_room = ref<Booking>({
  ...user
} as Booking);
const formRef = ref();
const formSchema = Yup.object().shape({
  payment_type: Yup.string().required("Payment Type harus dipilih"),
    total_price: Yup.string(),
    email: Yup.string()
        .email("Email harus valid")
        .required("Email harus diisi"),
    name: Yup.string().required("Nama harus diisi"),
    id: Yup.string(),
    phone: Yup.string().required("Nomor Telepon harus diisi"),
    villa_id: Yup.string(),
});

const route = useRoute();
const totalPrice = ref(0);
const available = ref([]);
const villaz = ref({});

const fetchVilla = async (id) => {
    try {
      const response = await axios.get(`/master/villa/${id}`)
      villaz.value = response.data.Villa
    } catch (err) {
      console.error(err);
    }
}

const computedMinDate = computed(() => {
    if (booking_room.value.booking_date) {
        const startDate = new Date(booking_room.value.booking_date);
        return startDate.fp_incr(1); // Menambahkan satu hari menggunakan fp_incr
    }
    return null;
});

const dateDisabled = computed(() => {
  return !booking_room.value.booking_date;
});

const isValidDate = (dateString) => {
  const date = new Date(dateString);
  return !isNaN(date.getTime());
};

onMounted(() => fetchVilla(route.params.uuid));

onMounted(() => {
    fetchVilla(route.params.uuid).then(() => {
        // Set villa_id dari villaz yang sudah di-fetch
        booking_room.value.villa_id = villaz.value.id;
    });
});

onMounted(async () => {
  const midtransScript = document.createElement("script");
  midtransScript.setAttribute(
    "src",
    "https://app.sandbox.midtrans.com/snap/snap.js"
    );
    midtransScript.setAttribute(
      "data-client-key",
      "SB-Mid-client-lrA_38wnpKudxLx1",
    );
    document.head.appendChild(midtransScript);

    midtransScript.onload = () => {
      console.log("Midtrans Snap loaded successfully");
    };

    fetchVilla(route.params.uuid);
});

const formatDate = (dateString) => {
    if (isValidDate(dateString)) {
        const [datePart, timePart] = dateString.split(' ');
        const [year, month, day] = datePart.split('-');
        const [hours, minutes] = timePart.split(':');
        const date = new Date(year, parseInt(month, 10) - 1, day, hours, minutes);
        return date.toLocaleDateString('en-EN', { weekday: 'long' });
    }
    return 'Invalid Date';
};

const formData = new FormData();
function processPayment() {

    formData.append("payment_type", booking_room.value.payment_type);
    formData.append("total_price", villaz.value.price * formatted.value);
    formData.append("email", booking_room.value.email);
    formData.append("name", booking_room.value.name);

    if (booking_room.value.id) {
        formData.append("user_id", booking_room.value.id);
    }
    formData.append("phone", booking_room.value.phone);
    formData.append("booking_date", booking_room.value.booking_date);
    formData.append("end_date", booking_room.value.end_date);
    formData.append("villa_id", booking_room.value.villa_id);

    block(document.getElementById("form-booking_room"));

    if (booking_room.value.payment_type === '1') {
      axios({
        method: "post",
        url: "/master/booking_room/store",
        data: formData,
        headers: {
          "Content-Type": "multipart/form-data",
        },
      })

        .then((data) => {
          if (data.data.success) {
            toast.success(data.data.message);
            formRef.value.resetForm();
            router.push(`/user/invoice/${data.data.booking_room.uuid}`)
            setTimeout(() => {
              localStorage.removeItem('lastWeb')
            }, 3000);
          } else {
            toast.error("Gagal menyimpan data");
          }
        })
        .catch((err: any) => {
          console.error(err);
          toast.error(err.response.data.message);
        })
        .finally(() => {
          unblock(document.getElementById("form-booking_room"));
        });
    } else {
      // Jika pembayaran menggunakan metode Online,
      axios({
        method: "post",
        url: "/master/booking_room/store",
        data: formData,
        headers: {
          "Content-Type": "multipart/form-data",
        },
      })
        .then ((data) => {
          if (data.data.success) {
            const datas = new FormData();
            datas.append('name', booking_room.value.name)
            datas.append('email', booking_room.value.email)
            datas.append('phone', booking_room.value.phone)
            datas.append('booking_room_id', data.data.booking_room.id)

            axios.post('/master/guest/store', datas, {
              headers: {"Content-Type": "multipart/form-data"}
            }).then(() => {
              if(window.snap && data.data.token) {
                window.snap.pay(data.data.token, {
                  onSucces: function(result) {
                    toast.success("Pembayaran berhasil");
                    router.push(`/user/invoice/${data.data.booking_room.uuid}`);
                  },
                  onPending: function(result) {
                    toast.info("Menunggu pembayaran");
                    router.push(`/user/invoice/${data.data.booking_room.uuid}`);
                  },
                  onError: function(result) {
                    toast.error("Pembayaran gagal");
                },
                onClose: function(result) {
                    toast.error("Pembayaran dibatalkan");
                }
            });
          } else {
            toast.error("Gagal memproses pembayaran")
          }
        })
          }
        })
        .catch((err: any) => {
          console.error(err);
          formRef.value.setErrors(err.response.data.errors);
          toast.error("Terjadi kesalahan saat memproses pembayaran");
        })
        .finally(() => {
          unblock(document.getElementById("form-booking_room"));
          localStorage.removeItem('lastWeb');
        });
    }
}

const villa = useVilla();
const villas = computed(() => 
    villa.data.value?.map((item: Villa) => ({
      id: item.id,
      text: item.nama,
    }))
  )

const formatPrice = (value: number): string => {
  return new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
  }).format(value);
};

const formatted = computed(() => {
  const startDate = new Date(booking_room.value.booking_date);
  const endDate = new Date(booking_room.value.end_date);
  const timeDifference = endDate.getTime() - startDate.getTime();
  const daysDifference = Math.floor(timeDifference / (1000 * 60 * 60 * 24));
  return daysDifference;
});

</script>

<template>
    <div class="container py-5">
      <div class="row justify-content-center">
        <div class="col-md-8">
          <VForm class="card" @submit="processPayment">
            <div class="card-header bg-primary text-white">
              <h3 class="mb-0 mt-8">Villa Reservation Form</h3>
            </div>
            <div class="card-body">
              <!-- <form @submit.prevent="submitForm"> -->
                <div class="mb-3">
                  <label class="form-label fw-bold fs-6 required">Payment Type</label>
                  <Field name="payment_type" type="hidden" v-model="booking_room.payment_type">
                    <select2 placeholder="Choose Payment Type" class="form-select-solid" name="payment_type"
                      v-model="booking_room.payment_type" :options="[
                      {id: '1', text: 'Offline'}, 
                      {id: '2', text: 'Online'},
                      ]"></select2>
                  </Field>
                  <div class="fv-plugins-message-container">
                    <div class="fv-help-block">
                      <ErrorMessage name="payment_type" />
                    </div>
                  </div>
                </div>
  
                <div class="mb-3">
                  <label class="form-label fw-bold fs-6 required">Nama</label>
                 <Field class="form-control form-control-lg form-control-solid" type="text" name="name"
                    autocomplete="off" v-model="booking_room.name" placeholder="Input Nama" />
                    <div class="fv-plugins-message-container"> 
                        <div class="fv-help-block">
                            <ErrorMessage name="name"/>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                  <label class="form-label fw-bold fs-6 required">Email</label>
                 <Field class="form-control form-control-lg form-control-solid" type="text" name="email"
                    autocomplete="off" v-model="booking_room.email" placeholder="Input Email"/>
                    <div class="fv-plugins-message-container">
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="email"/>
                            </div>
                        </div>
                    </div>
                </div>
  
                <div class="mb-3">
                  <label class="form-label fw-bold fs-6 required">Phone Number</label>
                  <Field class="form-control form-control-lg form-control-solid" type="text" name="phone"
                      autocomplete="off" v-model="booking_room.phone" placeholder="Input Phone Number" />
                      <div class="fv-plugins-message-container">
                        <div class="fv-help-block">
                            <ErrorMessage name="phone" />
                        </div>
                      </div>
                </div>
  
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold fs-6">Start Booking Date</label>
                    <Field class="form-control form-control-lg form-control-solid" name="booking_date"
                      autocomplete="off"> <date-picker v-model="booking_room.booking_date"
                       placeholder="Input Start Booking Date" 
                       :config="{
                        enableTime: false, dateFormat: 'Y-m-d', minDate: 'today',  
                       }"/>
                    </Field>
                    <div class="fv-plugins-message-container">
                      <div class="fv-help-block">
                        <ErrorMessage name="booking_date" />
                      </div>
                    </div>
                  </div>
  
                  <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold fs-6">End Booking Date</label>
                   <Field class="form-control form-control-lg form-control-solid" name="end_date"
                      autocomplete="off" :disabled="dateDisabled"> <date-picker v-model="booking_room.end_date"
                        placeholder="Input End Booking Date"
                        :config="{
                          enableTime: false, dateFormat: 'Y-m-d', minDate: computedMinDate,
                        }" :disabled="dateDisabled"/>
                   </Field>
                   <div class="fv-plugins-message-container">
                      <div class="fv-help-block">
                          <ErrorMessage name="end_date"/>
                      </div>
                   </div>
                  </div>
                </div>
  
                <div class="mb-3">
                  <label class="form-label fw-bold fs-5">Villa</label>
                  <Field name="villa_id" type="hidden" v-model="booking_room.villa_id" />
                  <div class="form-control form-control-lg form-control-solid" disabled>
                   {{ villaz.nama }}
                  </div>
                </div>

                <div class="row gap-2">
                  <span v-if="booking_room.end_date" class="text-end fs-4 fw-semibold">{{ currency(villaz.price * formatted) }} </span>
                <button type="submit" class="btn btn-primary">Submit Reservation</button>
                </div>
              <!-- </form> -->
            </div>
          </VForm>
        </div>
      </div>
    </div>
  </template>