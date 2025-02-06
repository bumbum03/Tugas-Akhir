import { useQuery } from "@tanstack/vue-query";
import axios from "@/libs/axios";

export function useVilla(options = {}) {
    return useQuery({
        queryKey:["villa"],
        queryFn: async () => 
            await axios.get("/master/villa").then((res:any) => res.data.data),
        ...options,
    });
}