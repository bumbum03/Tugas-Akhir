<script setup lang="ts">
import { block, unblock } from "@/libs/utils";
import { onMounted, ref, watch, computed } from "vue";
import * as Yup from "yup";
import axios from "@/libs/axios";
import { toast } from "vue3-toastify";
import type { Fasilitas } from "@/types";
import ApiService from "@/core/services/ApiService";

const props = defineProps({
    selected: {
        type: String,
        default: null,
    },
});

const emit = defineEmits(["close", "refresh"]);

const fasilitasVilla = ref<Fasilitas>({} as Fasilitas);
const formRef = ref();

const formSchema = Yup.object().shape({
    nama: Yup.string().required("Nama Fasilitas harus diisi"),
    // priority: Yup.string().required("Priority must be filled"),
    // icon: Yup.string().nullable(),
});

function getEdit() {
    block(document.getElementById("form-fasilitasVilla"));
    ApiService.get("master/villaFasilitas", props.selected)
        .then(({ data }) => {
            fasilitasVilla.value = data.villaFasilitas;
        })
        .catch((err: any) => {
            toast.error(err.response.data.message);
        })
        .finally(() => {
            unblock(document.getElementById("form-fasilitasVilla"));
        });
}

function submit() {
    const formData = new FormData();
    formData.append("nama", fasilitasVilla.value.nama);
    // formData.append("priority", fasilitasVilla.value.priority);
    // formData.append("icon", fasilitasVilla.value.icon);

    if (props.selected) {
        formData.append("_method", "PUT");
    }

    block(document.getElementById("form-fasilitasVilla"));
    axios({
        method: "post",
        url: props.selected
            ? `/master/villaFasilitas/${props.selected}`
            : "/master/villaFasilitas/store",
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
            unblock(document.getElementById("form-fasilitasVilla"));
        });
}

onMounted(async () => {
    if (props.selected) {
        getEdit();
    }
})

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
        id="form-fasilitasVilla"
        ref="formRef"
    >
        <div class="card-header align-items-center">
            <h2 class="mb-0">{{ selected ? "Edit" : "Tambah" }} Fasilitas Villa</h2>
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
                <div class="col-md-12">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">
                            Nama Fasilitas Villa
                        </label>
                        <Field
                            class="form-control form-control-lg form-control-solid"
                            type="text"
                            name="nama"
                            autocomplete="off"
                            v-model="fasilitasVilla.nama"
                            placeholder="Masukkan Nama fasilitas villa"
                        />
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="nama" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">
                            Prioritas fasilitas
                        </label>
                        <Field
                            class="form-control form-control-lg form-control-solid"
                            type="number"
                            name="priority"
                            autocomplete="off"
                            v-model="fasilitasVilla.priority"
                            placeholder="Input Priority"
                        />
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="priority" />
                            </div>
                        </div>
                    </div>
                </div> -->

                <!-- <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6">
                            Icon Fasilitas
                        </label>
                        <Field
                            class="form-control form-control-lg form-control-solid"
                            type="text"
                            name="icon"
                            autocomplete="off"
                            v-model="fasilitasVilla.icon"
                            placeholder="Input Icon"
                        />
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="icon" />
                            </div>
                        </div>
                    </div>
                </div> -->
            </div>
        </div>
        <div class="card-footer d-flex">
            <button type="submit" class="btn btn-primary btn-sm ms-auto">
                Save
            </button>
        </div>
    </VForm>
</template>
