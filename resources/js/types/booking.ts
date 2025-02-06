export interface Booking{
    id: BigInteger;
    uuid: string;
    booking_number: string;
    payment_type: string;
    // total_price: number;
    email: string;
    name: string;
    phone: string;
    payment_status: string;
    booking_date: string;
    end_date: string;
    villa_id: BigInteger;
    // user_id: BigInteger;
    status: string;
}