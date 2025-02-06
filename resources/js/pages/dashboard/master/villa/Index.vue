<script setup lang="ts">
import { h, ref, watch} from "vue";
import { useDelete } from "@/libs/hooks";
import Form from "./Form.vue";
import { createColumnHelper } from "@tanstack/vue-table";
import type { Villa } from "@/types";
import { useRoute } from "vue-router";
import { useRouter } from "vue-router";
import axios from "@/libs/axios";
import {block, unblock} from "@/libs/utils";
import { toast } from "vue3-toastify";


const column = createColumnHelper<Villa>();
const paginateRef = ref<any>(null);
const selected = ref<string>("");
const openForm = ref<boolean>(false);

const { delete: deleteUser } = useDelete({
    onSuccess: () => paginateRef.value.refetch(),
});

const  route = useRoute();
const router = useRouter();

const live = ref(true)

const togglefeat = () => {
    live.value = !live.value
}

const columns = [
    column.accessor("no", {
        header: "No",
    }),
    column.accessor("nama", {
        header: "Nama",
    }),
    column.accessor("email", {
        header: "Email",
    }),
    column.accessor("Alamat", {
        header: "Alamat", 
    }),
    column.accessor("uuid", {
        header: "Aksi", 
        cell: (cell) => 
            h("div", { class: "d-flex gap-3"}, [
                h(
                    "button",
                    {
                        class: "btn btn-sm btn-icon btn-primary w-20 px-4",
                        onClick: () => {
                            selected.value = cell.getValue();
                            openForm.value = true;
                        },
                    },
                    h("i", { class: "la la-pencil fs-3" })
                ),
                h(
                    "button",
                    {
                        class: "btn btn-sm btn-icon btn-danger w-20 px-4",
                        onClick: () => 
                            deleteUser(`/master/villa/${cell.getValue()}`),
                    },
                    h("i", { class: "la la-trash fs-2" })
                ),
            ]),
    }),
];

const refresh = () => paginateRef.value.refetch();

watch(openForm, (val) => {
    if (!val) {
        selected.value = "";
    }
    window.scrollTo(0,0);
});
</script>

<template>
 <Form 
    :selected="selected"
    @close="openForm = false"
    v-if="openForm"
    @refresh="refresh"
 />   

 <div class="card">
    <div class="card-header align-items-center">
        <h2 class="mb-0">Villa list</h2>
        <button
            type="button"
            class="btn btn-sm btn-primary ms-auto"
            v-if="!openForm"
            @click="openForm = true" 
        >
            Add
            <i class="la la-plus"></i>
        </button>
    </div>
    <div class="card-body">
        <paginate
            ref="paginateRef"
            id="table-villa"
            url="/master/villa"
            :columns="columns"
        ></paginate>
    </div>
 </div>
</template>