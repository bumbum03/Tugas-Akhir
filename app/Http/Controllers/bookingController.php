<?php

namespace App\Http\Controllers;

use App\Models\Villa;
use App\Models\Booking;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Requests\bookingStoreRequest;

class bookingController extends Controller
{

    public function get(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => Booking::all()
        ]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $per = $request->per ?? 10;
        $page = $request->page ? $request->page - 1 : 0;

        DB::statement('set @no=0+' . $page * $per);
        $data = Booking::with(['villa'])->when($request->search, function (Builder $query, string $search) {
            $query->whereHas('villa', function ($query) use ($search) {
                $query->where('booking_code', 'like', "%$search%")
                    ->orWhere('name', 'like', "%$search%")
                    ->orWhere('payment_type', 'like', "%$search%");
            });
        })->paginate($per, ['*', DB::raw('@no := @no + 1 AS no')]);

        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(bookingStoreRequest $request)
    {
        $validatedData = $request->validated();

        $validatedData['booking_number'] = 'BOOK' . Str::uuid();
        $validatedData['payment_status'] = 'Draft';

        $villa = Villa::find($validatedData['villa_id']);

        // $book = $validatedData['booking_number'];
        // $end = $validatedData['end_date'];

        $data = Booking::create($validatedData);

        if ($request->payment_type == '2') {
            \Midtrans\Config::$serverKey = config('midtrans.serverKey');
            \Midtrans\Config::$isProduction = false;
            \Midtrans\Config::$isSanitized = true;
            \Midtrans\Config::$is3ds = true;
            // \Midtrans\Config::$curlOptions = array(
            //     CURLOPT_SSL_VERIFYHOST => 0,
            //     CURLOPT_SSL_VERIFYPEER => 0,
            //     CURLOPT_RETURNTRANSFER => 1,
            //     CURLOPT_HEADER => 0,
            //     CURLOPT_HTTPHEADER => array(
            //         'Content-Type: application/json',
            //         'Accept: application/json',
            //         'Authorization: Basic ' . base64_encode(config('midtrans.serverKey') . ':')
            //     )
            // );
    
            $params = array(
                'transaction_details' => array(
                    'order_id' => $data->id,
                    'gross_amount' => $validatedData['total_price'],
                )
            );

            $snapToken = \Midtrans\Snap::getSnapToken($params);

            return response()->json([
                'success' => true,
                'message' => 'Sukses menyimpan data',
                'booking_room' => $data,
                'villa' => $villa,
                'token' => $snapToken
            ]);
        }



        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Sukses menyimpan data',
            'booking_room' => $data->with(['villa'])

        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($uuid)
    {
        $data = Booking::findByUuid($uuid);
        return response()->json([
            'Booking' => $data
        ]);
    }


    public function status($uuid) 
    {
        $data = Villa::findByUuid($uuid);
        $data->update(['status' => 'Non Active']);
        return response()->json([
            'message' => 'Success'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(bookingStoreRequest $request, $uuid)
    {
        $data = Booking::findByUuid($uuid);
        $validatedData = $request->validated();
        $validatedData['payment_status'] = 'Completed';

        $data->update($validatedData);
        return response()->json([
            'success' => true,
            'booking' => $data
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        $booking = Booking::findByUuid($uuid);
        $booking->delete();

        return response()->json([
            'success' => true
        ]);
    }

    public function changeStatus($uuid)
    {
        $data = Booking::findByUuid($uuid);
        $data->status = 'Non Active';
        $data->save();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
