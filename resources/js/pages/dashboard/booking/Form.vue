<script setup lang="ts">
import {block ,unblock } from "@/libs/utils";
import { onMounted, ref, watch, computed } from "vue";
import * as Yup from "yup";
import axios from "@/libs/axios";
import { toast } from "vue3-toastify";
import type { Booking, Villa } from "@/types";
import { useVilla } from "@/services/useVilla";
import ApiService from "@/core/services/ApiService";

const props = defineProps({
    selected: {
        type: String,
        default: null,
    },
});

const emit = defineEmits(["close", "refresh"]);

const booking_room = ref<Booking>({} as Booking);
const image = ref<any>([]);
const fileTypes = ref (["image/jpeg", "image/png", "image/jpg"]);
const formRef = ref();

const formSchema = Yup.object().shape({
    booking_number: Yup.string().required("Booking Number must be filled"),
    payment_type: Yup.string().required("Payment Type must be chosen"),
    total_price: Yup.number().required("Total Price must be filled"),
    email: Yup.string().email("Email must be valid").required("Email must be filled"),
    name: Yup.string().required("Name must be filled"),
    phone: Yup.number().required("Phone Number must be filled"),
    payment_status: Yup.string(),
    villa_id: Yup.string(),
});

function getEdit() {
    block(document.getElementById("form-booking_room"));
    ApiService.get("master/booking_room", props.selected)
    .then(({ data }) => {
        booking_room.value = data.booking_room;
        console.log(booking_room.value)
    })
    .catch((err: any) => {
        toast.error(err.response.data.message);
    })
    .finally(() => {
        unblock(document.getElementById("form-booking_room"));
    });
}

function submit(){
    const formData = new FormData();
    formData.append("booking_number", booking_room.value.booking_number);
    formData.append("payment_type", booking_room.value.payment_type);
    formData.append("total_price", booking_room.value.total_price);
    formData.append("email", booking_room.value.email);
    formData.append("name", booking_room.value.name);
    formData.append("phone", booking_room.value.phone);
    formData.append("payment_status", booking_room.value.payment_status);
    formData.append("booking_date", booking_room.value.booking_date);
    formData.append("end_date", booking_room.value.end_date);
    formData.append("villa_id", booking_room.value.villa_id);

    if (props.selected) {
        formData.append("_method", "PUT");
    }

    block(document.getElementById("form-booking_room"));
    axios({
        method: "post",
        url: props.selected
            ? `/master/booking_room/${props.selected}`
            : "/master/booking_room/store",
        data: formData,
        headers: {
            "content-type": "multipart/form-data"
        },
    })
        .then(() => {
            emit("close");
            emit("refresh");
            toast.success("Data saved successfully");
            formRef.value.resetForm();
        })
        .catch((err: any) => {
            formRef.value.setErrors(err.response.data.errors);
            toast.error(err.response.data.message);
        })
        .finally(() => {
            unblock(document.getElementById("form-booking_room"));
        });
}

const villa = useVilla();
const villas = computed(() => 
    villa.data.value?.map((item: Villa) => ({
        id: item.id,
        text: item.nama,
    }))
);

const computedMinDate = computed(() => {
    if (booking_room.value.booking_date) {
        const startDate = new Date(booking_room.value.booking_date);
        return startDate.fp_incr(1);
    }
    return null;
});

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
    id="form-booking_room"
    ref="formRef"
>
    <div class="card-header align-items-center">
        <h2 class="mb-0">{{ selected ? "Bukti" : "Add" }} Booking Room</h2>
        <button type="button" class="btn btn-sm btn-light-danger ms-auto" @click="emit('close')">
            Cancel
            <i class="la la-times-circle p-0"></i>
        </button>
    </div>
    <div class="card-body">
        <div class="row">

            <div class="col-md-6 d-done">
                <div class="fv-row mb-7">
                    <label class="form-label fw-bold fs-6 required">
                        Booking Number
                    </label>
                    <Field class="form-control form-control-lg form-control-solid" type="text" 
                    name="booking_number" autocomplete="off" v-model="booking_room.booking_number" placeholder="Input Booking Number"/>
                    <div class="fv-plugins-message-container">
                        <div class="fv-help-block">
                            <ErrorMessage name="booking_number" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 d-done">
                <div class="fv-row mb-7">
                    <label class="form-label fw-bold fs-6 required">
                        Total Price 
                    </label>
                    <Field class="form-control form-control-lg form-control-solid" type="number" 
                    name="total_price" autocomplete="off" v-model="booking_room.total_price" placeholder="Input Total Price"/>
                    <div class="fv-plugins-message-container">
                        <div class="fv-help-block">
                            <ErrorMessage name="total_price" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 d-none">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">
                            Payment Type
                        </label>
                        <Field name="payment_type" type="hidden" v-model="booking_room.payment_type">
                            <select2 placeholder="Choose Payment Type" class="form-select-solid" name="payment_type" v-model="booking_room.payment_type" :options="[
                            {id: '1', text: 'Offline'},{id: '2', text: 'Online'}
                            ]">
                            </select2>
                        </Field>
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="payment_type" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 d-none">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">
                            Payment Status
                        </label>
                        <Field class="form-control form-control-lg form-control-solid" type="text" name="payment_status" autocomplete="off" v-model="booking_room.payment_status" placeholder="Input Payment Status"
                        />
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="payment_status" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 d-none">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">
                            Name
                        </label>
                        <Field class="form-control form-control-lg form-control-solid" type="text" name="name" autocomplete="off" v-model="booking_room.name" placeholder="Input Name"/>
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="name" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 d-none">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">
                            Email
                        </label>
                        <Field class="form-control form-control-lg form-control-solid" type="text" name="email" autocomplete="off" v-model="booking_room.email" placeholder="Input Email"
                        />
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="email" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 d-none">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">
                            Phone Number
                        </label>
                        <Field class="form-control form-control-lg form-control-solid" type="text" name="phone" autocomplete="off" v-model="booking_room.phone" placeholder="Input Phone Number"
                        />
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="phone" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 d-none">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6">
                            Start Booking Date 
                        </label>
                        <Field class="form-control form-control-lg form-control-solid" name="booking_date" autocomplete="off"> <date-picker v-model="booking_room.booking_date" placeholder="Input Start Booking Date" :config="{
                            enableTime: true, format: 'Y:m:d H:i', minDate: 'today', minTime: checkIn, maxTime: checkIn
                        }"/> </Field>
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="booking_date" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 d-none">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6">
                            End Booking Date 
                        </label>
                        <Field class="form-control form-control-lg form-control-solid" name="end_date" autocomplete="off"> <date-picker v-model="booking_room.end_date" placeholder="Input End Date" :config="{
                            enableTime: true, format: 'Y:m:d H:i', minDate: computedMinDate, minTime: checkOut, maxTime: checkOut
                        }"/> </Field>
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="end_date" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 d-none">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-5">
                            Villa
                        </label>
                        <Field name="villa_id" type="hidden" v-model="booking_room.villa_id">
                            <select2 placeholder="Choose Villa" class="form-select-solid" name="villa_id" v-model="booking_room.villa_id" :options="villa">
                            </select2>
                        </Field>
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="villa_id" />
                            </div>
                        </div>
                    </div>
                </div>

        </div>
    </div>
    <div class="card-footer d-flex">
        <button type="submit" class="btn btn-primary btn-sm ms-auto">
            Save
        </button>
    </div>
</VForm>
</template>