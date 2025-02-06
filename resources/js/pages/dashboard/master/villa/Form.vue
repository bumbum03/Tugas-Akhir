<script setup lang="ts">
import { block, unblock } from "@/libs/utils";
import { onMounted, ref, watch, computed } from "vue";
import * as Yup from "yup";
import axios from "@/libs/axios";
import { toast } from "vue3-toastify";
import type { Villa } from "@/types";
import ApiService from "@/core/services/ApiService";
import { useCity } from "@/services/useCity";
import { useVillaFasilitas } from "@/services/useVillaFasilitas";
import Select2 from "@/components/Select2.vue";

const props = defineProps({
    selected: {
        type: String,
        default: null,
    },
});

const emit = defineEmits(["close", "refresh"]);

const villa = ref<Villa>({
    fasilitas_id: [],
    villaImage: [],
} as Villa);
const formRef = ref();
const image = ref<any>([]);
const fileTypes = ref(["image/jpeg", "image/png", "image/jpg"]);

const city = useCity();
const cities = computed(() =>
    city.data.value?.map((item: any) => ({
        id: item.code,
        text: item.name,
    }))
);

const villaFasilitas = useVillaFasilitas();
const villa_fasilitas = computed(() =>
    villaFasilitas.data.value?.map((item: any) => ({
        id: item.id,
        text: item.nama,
    }))
);

const formSchema = Yup.object().shape({
    nama: Yup.string().required("Nama harus diisi"),
    deskripsi: Yup.string().required("Deskripsi harus diisi"),
    alamat: Yup.string().required("Alamat harus diisi"),
    email: Yup.string()
        .email("Email must be valid")
        .required("Email harus diisi"),
    nomor: Yup.string()
        .matches(/^0\d{9,13}$/, "Nomor Telepone harus valid")
        .required("Nomor harus diisi"),
    tahun_dibangun: Yup.string().nullable(),
    total_kamar: Yup.number().required("Total kamar harus diisi"),
    kapasitas: Yup.number().required("Kapasitas harus diisi"),
    kota_id: Yup.string().required("Kota harus diisi"),
    price: Yup.number().required("Harga harus diisi"),
    fasilitas_id: Yup.array()
        .of(Yup.string().required("Fasilitas harus diisi"))
        .min(1, "Fasilitas harus diisi"),
    image: Yup.string(),
});

function getEdit() {
    block(document.getElementById("form-villa"));
    ApiService.get("master/villa", props.selected)
        .then(({ data }) => {
            villa.value = data.Villa;
            console.log(data)
            image.value = data.VillaImg.map(VillaImg => `/storage/${VillaImg}`)
        })
        .catch((err: any) => {
            toast.error(err.response.data.message);
        })
        .finally(() => {
            unblock(document.getElementById("form-villa"));
        });
}

function submit() {
    const formData = new FormData();
    formData.append("nama", villa.value.nama);
    formData.append("deskripsi", villa.value.deskripsi);
    formData.append("alamat", villa.value.alamat);
    formData.append("email", villa.value.email);
    formData.append("nomor", villa.value.nomor);
    formData.append("tahun_dibangun", villa.value.tahun_dibangun);
    formData.append("total_kamar", villa.value.total_kamar);
    formData.append("kapasitas", villa.value.kapasitas);
    formData.append("check_in", villa.value.check_in);
    formData.append("check_out", villa.value.check_out);
    formData.append("price", villa.value.price);
    formData.append("kota_id", villa.value.kota_id);

    villa.value.fasilitas_id.forEach((item: any) => {
        formData.append("fasilitas_id[]", item);
    });

    if (image.value.length) {
        image.value.forEach(VillaImg => {
            formData.append("image[]", VillaImg.file);
        })
    }

    if (props.selected) {
        formData.append("_method", "PUT");
    }

    block(document.getElementById("form-villa"));
    axios({
        method: "post",
        url: props.selected
            ? `/master/villa/${props.selected}`
            : "/master/villa/store",
        data: formData,
        headers: {
            "Content-Type": "multipart/form-data",
        },
    })
        .then(() => {
            emit("close");
            emit("refresh");
            toast.success("Data berhasil disimpan");
            formRef.value.resetForm();
        })
        .catch((err: any) => {
            formRef.value.setErrors(err.response.data.errors);
            toast.error(err.response.data.message);
        })
        .finally(() => {
            unblock(document.getElementById("form-villa"));
        });
}

onMounted(async () => {
    if (props.selected) {
        getEdit();
    }
});

watch(
    () => props.selected,
    () => {
        if (props.selected) {
            getEdit();
        }
    }
);
</script>

<template>
    <VForm
        class="form card mb-10"
        @submit="submit"
        :validation-schema="formSchema"
        id="form-villa"
        ref="formRef"
    >
        <div class="card-header align-items-center">
            <h2 class="mb-0">{{ selected ? "Edit" : "Tambah" }} Villa</h2>
            <button
                type="button"
                class="btn btn-sm btn-light-danger ms-auto"
                @click="emit('close')"
            >
                Cancel
                <i class="la la-times-circle p-0"></i>
            </button>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">
                            Nama Villa
                        </label>
                        <Field
                            class="form-control form-control-lg form-control-solid"
                            type="text"
                            name="nama"
                            autocomplete="off"
                            v-model="villa.nama"
                            placeholder="Masukkan Nama Villa"
                        />
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="nama" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">
                            Deskripsi
                        </label>
                        <Field
                            class="form-control form-control-lg form-control-solid"
                            type="text"
                            name="deskripsi"
                            autocomplete="off"
                            v-model="villa.deskripsi"
                            placeholder="Masukkan Deskripsi"
                        />
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="deskripsi" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">
                            Alamat
                        </label>
                        <Field
                            class="form-control form-control-lg form-control-solid"
                            type="text"
                            name="alamat"
                            autocomplete="off"
                            v-model="villa.alamat"
                            placeholder="Masukkan Alamat"
                        />
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="alamat" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-5">Email</label>
                        <div class="input-group mb-3">
                            <span
                                class="input-group-text bg-primary-subtle"
                                id="basic-addon1"
                                >@</span
                            >
                            <Field
                                class="form-control form-control-lg form-control-solid"
                                type="text"
                                name="email"
                                autocomplete="off"
                                v-model="villa.email"
                                placeholder="Input Email"
                                aria-describedby="basic-addon1"
                            />
                        </div>
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="email" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">
                            Nomor Telepon
                        </label>
                        <Field
                            class="form-control form-control-lg form-control-solid"
                            type="text"
                            name="nomor"
                            autocomplete="off"
                            v-model="villa.nomor"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            placeholder="Masukkan Nomor Telepon"
                        />
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="nomor" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">
                            Tahun Dibangun
                        </label>
                        <Field
                            class="form-control form-control-lg form-control-solid"
                            name="tahun_dibangun"
                            autocomplete="off"
                            v-model="villa.tahun_dibangun"
                            placeholder="Masukkan Tahun Dibangun"
                        >
                        <date-picker v-model="villa.tahun_dibangun" placeholder="Input Tahun Dibangun" ></date-picker>
                        </Field>
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="tahun_dibangun" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">
                            Total Kamar
                        </label>
                        <Field
                            class="form-control form-control-lg form-control-solid"
                            type="text"
                            name="total_kamar"
                            autocomplete="off"
                            v-model="villa.total_kamar"
                            placeholder="Masukkan Total Kamar"
                        />
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="total_kamar" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">
                            Kapasitas Villa
                        </label>
                        <Field
                            class="form-control form-control-lg form-control-solid"
                            type="text"
                            name="kapasitas"
                            autocomplete="off"
                            v-model="villa.kapasitas"
                            placeholder="Masukkan Kapasitas"
                        />
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="kapasitas" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-5"
                            >Check-In Time</label
                        >
                        <div class="input-group mb-3">
                            <span
                                class="input-group-text fa-regular fa-clock bg-primary-subtle"
                                id="basic-addon1"
                            ></span>
                            <field
                                class="form-control form-control-lg form-control-solid"
                                name="check_in"
                                autocomplete="off"
                                aria-describedby="basic-addon1"
                            >
                                <date-picker
                                    v-model="villa.check_in"
                                    placeholder="Input Check-In Time"
                                    :config="{
                                        enableTime: true,
                                        noCalendar: true,
                                        format: 'H:i',
                                    }"
                                />
                            </field>
                        </div>
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="check_in" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-5"
                            >Check-Out Time</label
                        >
                        <div class="input-group mb-3">
                            <field
                                class="form-control form-control-lg form-control-solid"
                                name="check_out"
                                autocomplete="off"
                                aria-describedby="basic-addon1"
                            >
                                <date-picker
                                    v-model="villa.check_out"
                                    placeholder="Input Check-Out Time"
                                    :config="{
                                        enableTime: true,
                                        noCalendar: true,
                                        format: 'H:i',
                                    }"
                                />
                            </field>
                            <span
                                class="input-group-text fa-regular fa-clock bg-primary-subtle"
                                id="basic-addon1"
                            ></span>
                        </div>
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="check_out" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6">
                            Harga Villa
                        </label>
                        <div class="input-group mb-3">
                            <span
                                class="input-group-text bg-primary-subtle"
                                id="basic-addon1"
                                >Rp</span
                            >
                            <Field
                                class="form-control form-control-lg form-control-solid"
                                type="number"
                                name="price"
                                autocomplete="off"
                                v-model="villa.price"
                                placeholder="Input Room Price"
                            />
                        </div>
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="price" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">
                            Kota
                        </label>
                        <Field
                            type="hidden"
                            name="kota_id"
                            v-model="villa.kota_id"
                        >
                            <select2
                                placeholder="Pilih Kota"
                                class="form-select-solid"
                                :options="cities"
                                name="kota_id"
                                v-model="villa.kota_id"
                            ></select2>
                        </Field>
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="kota_id" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">
                            Fasilitas
                        </label>
                        <Field
                            type="text"
                            name="fasilitas_id[]"
                            autocomplete="off"
                            v-model="villa.fasilitas_id"
                        >
                            <select2
                                placeholder="pilih fasilitas"
                                class="form-select-solid"
                                name="fasilitas_id[]"
                                multiple
                                :settings="{ allowClear: true, multiple: true }"
                                :options="villa_fasilitas"
                                v-model="villa.fasilitas_id"
                            >
                            </select2>
                        </Field>
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="fasilitas_id" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6">
                            Image 
                        </label>
                        <span> (max: 3) </span>
                        <file-upload :files="image" :accepted-file-types="fileTypes" required v-on:updatefiles="(file) => (image = file)" allowMultiple=true maxFiles="3"></file-upload>
                        <div class="fv-plugins-message-container"> 
                            <div class="fv-help-block">
                                <ErrorMessage name="image"/>
                            </div>
                        </div>
                        
                    </div>
                </div>

            </div>
        </div>
        <div class="card-footer d-flex">
            <button type="submit" class="btn btn-primary btn-sm ms-auto">
                Simpan
            </button>
        </div>
    </VForm>
</template>
