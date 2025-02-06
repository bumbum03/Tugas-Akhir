import { useQuery } from "@tanstack/vue-query";
import axios from "@/libs/axios";

export function useVillaFasilitas(options = {}) {
    return useQuery({
        queryKey: ["villa_fasilitas"],
        queryFn: async () => 
            await axios.get("/master/villaFasilitas").then((res: any) => res.data.data),
        ...options,
    });
}