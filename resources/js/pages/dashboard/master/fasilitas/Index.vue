<script setup lang="ts">
import {h, ref, watch} from "vue";
import { useDelete } from "@/libs/hooks";
import Form from "./Form.vue";
import { createColumnHelper } from "@tanstack/vue-table";
import { useRouter } from "vue-router";

const column = createColumnHelper<User>();
const paginateRef = ref<any>(null);
const selected = ref<string>("");
const openForm = ref<boolean>(false);
const router = useRouter();

const { delete: deleteUser } = useDelete({
    onSuccess: () => paginateRef.value.refetch(),
});

const columns = [
    column.accessor("no", {
        header: "No",
    }),
    column.accessor("nama", {
        header: "Nama",
    }),
    column.accessor("uuid", {
        header: "Action",
        cell: (cell) =>
            h("div", { class: "d-flex gap-2" }, [
                h(
                    "button",
                    {
                        class: "btn btn-sm btn-icon btn-info",
                        onClick: () => {
                            selected.value = cell.getValue();
                            openForm.value = true;
                        },
                    },
                    h("i", { class: "la la-pencil fs-2" })
                ),
                h(
                    "button",
                    {
                        class: "btn btn-sm btn-icon btn-danger",
                        onClick: () =>
                            deleteUser(`/master/villaFasilitas/${cell.getValue()}`),
                    },
                    h("i", { class: "la la-trash fs-2" })
                ),
            ]),
    }),
];

const refresh = () => paginateRef.value.refetch();

// const goBack = () => {
//     router.
// }

watch(openForm, (val) => {
    if (!val) {
        selected.value = "";
    }
    window.scrollTo(0, 0);
})
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
        <h2 class="mb-0">Villa fasilitas list</h2>
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
            id="table-villaFasilitas"
            url="/master/villaFasilitas"
            :columns="columns"
        ></paginate>
    </div>
 </div>
</template>