<script setup lang="ts">
import { useSetting } from '@/services';
import { useVilla } from '@/services/useVilla';
import * as Yup from 'yup';
import { onMounted, ref, watch, computed, defineComponent } from 'vue';
import { useCity } from '@/services/useCity';
import { useRouter, useRoute } from 'vue-router';
import Select2 from '@/components/Select2.vue';

const { data: setting = {} } = useSetting();

const search = ref('');

const formSchema = Yup.object().shape({
  search: Yup.string().required("isi"),
});

const city = useCity();
const cities = computed(() => 
  city.data.value?.map((item: any) => ({
      id: item.code,
      text: item.name,
  }))
);

const villa = useVilla();

const cityId = ref('');

</script>


<template>
  <div class="homepage">
    <!-- Gambar sebagai background -->
    <img :src="setting?.bg_auth" class="hero-section" alt="auth" />

    <!-- Konten utama -->
    <div class="container position-relative z-index-2 ">
      <div class="row align-items-center min-vh-100">
        <!-- Kolom Teks -->
        <div class="col-lg-6 text-content">
          <h1 class="display-4 fw-bold mb-4 text-white">Villo Accomodation</h1>
          <p class="lead mb-4 text-white">
            Discover our handpicked collection of stunning villas for your perfect getaway
          </p>
        </div>

        <!-- Kolom Form -->
        <div class="col-xl-4 col-lg-5 offset-lg-2">
          <div class="search-form p-4 bg-white rounded-4 shadow-lg pt-5 px-8">
            <div class="row gap-1">
              <h3 class="py-5">Booking Your Villa</h3>
              <div class="col-12">
                <div class="col-md-12 mb-6 py-5">
                  <label class="form-label fw-bold fs-6 py-4 required"  style="color: #dfa974;" >
                    <i class="fa-solid fa-location-dot me-2 fs-4 text-primary"></i>
                    <span style="color: black;">City</span>
                  </label>
                  <Field
                    type="hidden"
                    name="name"
                    v-model="cityId"
                  >
                    <select2 placeholder="Select City" class="form-select-solid" :options="cities"
                      v-model="cityId"></select2>
                  </Field>
                </div>
              </div>
              <div class="col-12">
                <router-link :to="{name: 'landing.villa', query: {uuid: cityId}  }" class="btn btn-primary w-100 btn-lg">
                  Search Villa 
                  <i class="fa-solid fa-magnifying-glass ms-1"></i>
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.hero-section {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  z-index: -1;
}

.container {
  position: relative;
  z-index: 2;
}

.search-form {
  max-width: 400px;
  backdrop-filter: blur(10px);
  background: rgba(255, 255, 255, 0.95);
  border-radius: 12px;
}

h1,
h2,
h3,
h4,
h5,
h6 {
  margin: 0;
  color: #111111;
  font-weight: 400;
  font-family: 'Lora', serif;
}

h3 {
  font-size: 30px;
}

.text-content {
  z-index: 2;
}

.form-control {
  border: 1px solid #dee2e6;
  height: 3rem;
  font-size: 0.9rem;
}

.form-control:focus {
  border-color: #3498db;
  box-shadow: 0 0 0 0.25rem rgba(52, 152, 219, 0.25);
}

.btn-primary {
  background-color: #3498db;
  border-color: #3498db;
}

.text-white {
  color: white;
}
</style>
