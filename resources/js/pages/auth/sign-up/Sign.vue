<script lang="ts">
import { getAssetPath } from "@/core/helpers/assets";
import { defineComponent, ref } from "vue";
import { useAuthStore } from "@/stores/auth";
import router from "@/router";
import * as Yup from "yup";
import axios from "@/libs/axios";
import { toast } from "vue3-toastify"
import { blockBtn, unblockBtn } from "@/libs/utils"
import { useSetting } from "@/services";


export default defineComponent({

    setup() {
        const { data: setting = {} } = useSetting()
        const store = useAuthStore();

        const submitButton = ref(null);

        //Create form validation object
        const regis = Yup.object().shape({
            name: Yup.string()
                .required("Please enter a Name")
                .label("Name"),
            email: Yup.string()
                .email('Invalid Email')
                .required("Please enter Email").label("Email"),
            password: Yup.string()
                .min(8, 'The password must consist of a minimum of 8 characters')
                .required('Please enter the password').label("Password"),
            password_confirmation: Yup.string()
                .oneOf(
                    [Yup.ref("password")],
                    "Confirmation Password must be the same"
                )
                .required("Password confirmation is required")
                .label("Confirm Password"),
            phone: Yup.string()
                .matches(/^08[0-9]\d{8,11}$/, "Invalid Phone Number")
                .required("Please fill in the column")
                .label("Phone")
        });

        return {
            regis,
            submitButton,
            getAssetPath,
            store, router,
            setting
        };
    },
    data() {
        return {
            user: {
                name: '',
                phone: '',
                email: '',
                password: '',
                password_confirmation: '',
            },
        }
    },
    methods: {
        submit() {
            blockBtn(this.submitButton);

            axios.post("/auth/register", this.user)
                .then(res => {
                    toast.success("Account created successfully");
                    router.push({ name: "sign-in" });
                })
                .catch((err) => {
                    if (err.response && err.response.data && err.response.data.errors) {
                        const errors = err.response.data.errors;
                        for (const key in errors) {
                            if (errors.hasOwnProperty(key)) {
                                toast.error(errors[key][0]);
                            }
                        }
                    } else {
                        toast.error("An unknown error occurred");
                    }
                })
                .finally(() => {
                    unblockBtn(this.submitButton);
                });
        }
    }
})
</script>

<template>
    <div class="w-lg-500px w-100">
        <div class="text-center mb-10">
            <router-link to="/">
                <img :src="setting?.logo" :alt="setting?.app" class="w-200px mb-8">
            </router-link>

            <h1 class="mb-3">
                Register Account <span class="text-primary">{{ setting?.app }}</span>
            </h1>
        </div>

        <VForm class="form w-100 fv-plugins-bootstrap5 fv-plugins-framework pb-4 pt-4" @submit="submit"
            :validation-schema="regis" id="form-user" ref="formRef">
            <ul class="nav nav-tabs nav-line-tabs mb-5 fs-6"></ul>
            <div class="card-body">
                <div class="row">
                    <div class="fv-row mb-2">
                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <label class="form-label fw-bold fs-6">
                                Name
                            </label>
                            <Field tabindex="1" class="form-control form-control-lg form-control-solid" type="text"
                                name="name" autocomplete="off" v-model="user.name" placeholder="Enter your Name" />
                            <div class="fv-plugins-message-container">
                                <div class="fv-help-block">
                                    <ErrorMessage name="name" />
                                </div>
                            </div>
                        </div>
                        <!--end::Input group-->
                    </div>
                    <div class="fv-row mb-2">
                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <label class="form-label fw-bold fs-6">
                                Email
                            </label>
                            <Field tabindex="2" class="form-control form-control-lg form-control-solid" type="text"
                                name="email" autocomplete="off" v-model="user.email" placeholder="Enter your Email" />
                            <div class="fv-plugins-message-container">
                                <div class="fv-help-block">
                                    <ErrorMessage name="email" />
                                </div>
                            </div>
                        </div>
                        <!--end::Input group-->
                    </div>
                    <div class="fv-row mb-2">
                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <label class="form-label fw-bold fs-6">
                                Password
                            </label>
                            <Field tabindex="3" class="form-control form-control-lg form-control-solid" type="password"
                                name="password" autocomplete="off" v-model="user.password"
                                placeholder="Enter your Password" />
                            <div class="fv-plugins-message-container">
                                <div class="fv-help-block">
                                    <ErrorMessage name="password" />
                                </div>
                            </div>
                        </div>
                        <!--end::Input group-->
                    </div>
                    <div class="fv-row mb-2">
                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <label class="form-label fw-bold fs-6">
                                Confirm Password
                            </label>
                            <Field tabindex="4" class="form-control form-control-lg form-control-solid" type="password"
                                name="password_confirmation" autocomplete="off" v-model="user.password_confirmation"
                                placeholder="Confirm your Password" />
                            <div class="fv-plugins-message-container">
                                <div class="fv-help-block">
                                    <ErrorMessage name="password_confirmation" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="fv-row mb-2">
                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <label class="form-label fw-bold fs-6">
                                Phone Number
                            </label>
                            <Field tabindex="5" class="form-control form-control-lg form-control-solid" type="text"
                                name="phone" autocomplete="off" v-model="user.phone" oninput="this.value = this.value.replace(/[^0-9]/g, '')"

                                placeholder="Enter your Phone Number" />
                            <div class="fv-plugins-message-container">
                                <div class="fv-help-block">
                                    <ErrorMessage name="phone" />
                                </div>
                            </div>
                        </div>
                        <!--end::Input group-->
                    </div>
                </div>
            </div>
            <div class="card-footer mt-2 d-flex">
                <button tabindex="6" type="submit" ref="submitButton" class="btn btn-lg btn-primary w-100 mb-5">
                    Register
                </button>
            </div>

        </VForm>

        <div class="border-bottom border-gray-300 w-100 mt-5 mb-10"></div>

        <div class="text-gray-400 fw-semobold fs-4 text-center mt-10">
            {{ $t('Already have an account?') }}

            <router-link to="/sign-in" class="link-primary fw-bold">
                {{ $t('Sign In Now!') }}
            </router-link>
        </div>
    </div>
</template>