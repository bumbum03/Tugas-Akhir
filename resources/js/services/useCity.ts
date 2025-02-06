import axios from "@/libs/axios";
import { useQuery } from "@tanstack/vue-query";


export function useCity(options = {}){
    return useQuery({
        queryKey: ["cities"],
        queryFn: async () => 
            await axios
        .get("/indonesia/cities")
        .then((res: any) => res.data.data),
        ...options,
    });
}