<script setup lang="ts">
import { h, ref, watch } from "vue";
import Form from "./Form.vue";
import { createColumnHelper } from "@tanstack/vue-table";
import { useDelete } from "@/libs/hooks";
import type { Booking } from "@/types";
import { useRouter } from "vue-router";
import type { User } from "@/types";
import axios from "@/libs/axios";
import {block, unblock} from "@/libs/utils";
import { toast } from "vue3-toastify";

const column = createColumnHelper<User>();
const paginateRef = ref<any>(null);
const selected = ref<string>("");
const openForm = ref<boolean>(false);

const { delete: deleteUser } = useDelete({
    onSuccess: () => paginateRef.value.refetch(),
});

const router = useRouter();

const columns = [
    column.accessor("no", {
        header: "No",
    }),
    column.accessor("name", {
        header: "Name",
    }),
    column.accessor("booking_number", {
        header: "Booking Number",
    }),
    column.accessor("villa.nama", {
        header: "Villa",
    }),
    column.accessor("payment_type", {
        header: "Payment Type",
    }),
    column.accessor("uuid", {
        header: "Status",
        cell: (cell) => 
            h("div", { class: "d-flex gap-3"}, [
                h(
                    "input",
                    {
                        type: "button",
                        class: cell.row.original.payment_status == 'Completed' ? "btn btn-sm btn-icon btn-success w-100 px-4"  : "btn btn-sm btn-icon btn-secondary w-100 px-4",
                        onClick: () => updateStatus(cell.getValue()),
                        value: cell.row.original.payment_status == 'Completed' ? "Completed" : "Draft",
                    },
                    [
                        h("i", { class: "&nbsp"})
                    ]
                ),
                // cell.row.original.payment_type === 'Offline' ? (
                //     h(
                //         "button",
                //         {
                //             class: "btn btn-sm btn-icon btn-info w-20 px-4",
                //             onClick: cell.row.original.payment_type === 'Offline' ? () => {
                //                 selected.value = cell.getValue();
                //                 openForm.value = true;
                //             } : null,
                //         },
                //         h("i", { class: "fa-solid fa-camera fs-3" })
                //     )
                // ) : null,
            ])
    }),
];

function updateStatus(uuid) {
    block(document.querySelector("table"));
    axios({
        method: "post",
        url: `/master/booking_room/${uuid}/change-status`,
    })
    .then(() => {
        refresh()
        toast.success("Data berhasil disimpan");
    })
    .catch((err: any) => {
        toast.error(err.response.data.message);
    })
    .finally(() => {
        unblock(document.querySelector("table"));
    });
}

const refresh = () => paginateRef.value.refetch();

watch(openForm, (val) => {
    if (!val) {
        selected.value = "";
    }
    window.scrollTo(0, 0);
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
        <h2 class="mb-0">Booking List</h2>
    </div>
    <div class="card-body">
        <paginate
            ref="paginateRef"
            id="table-booking"
            url="/master/booking_room"
            :columns="columns"
        ></paginate>
    </div>
</div>

</template>