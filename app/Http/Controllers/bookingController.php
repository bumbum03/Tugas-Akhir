<?php

namespace App\Http\Controllers;

use App\Models\Villa;
use App\Models\Booking;
use App\Http\Requests\bookingStoreRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Midtrans\Config;
use Midtrans\Snap;

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
        $page = $request-> page ? $request->page - 1 : 0;

        DB::statement('set @no=0+' . $page * $per);
        $data = Booking::with(['villa'])->when($request->search, function(Builder $query, string $search) {
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

        $validatedData['booking_number'] = 'BOOK' . rand(100000, 999999);
        $validatedData['payment_status'] = 'Draft';

        Config::$serverKey = getenv('MIDTRANS_SERVER_KEY');
        Config::$clientKey = getenv('MIDTRANS_CLIENT_KEY');
        Config::$isProduction = false;

        $villa = Villa::find($validatedData['villa_id']);
        $villa->update(['status' => 'Non Active']);

        // $book = $validatedData['booking_number'];
        // $end = $validatedData['end_date'];

        $data = Booking::create($validatedData);

        if($request->payment_type == '2') {
            $midtransParams = [
                'transaction_details' => [
                    'order_id' => $data->booking_number,
                    'gross_amount' => (int) $request->total_price,
                ],
            ];
            try {
                $snapToken = Snap::getSnapToken($midtransParams);
                return response()->json([
                    'success' => true,
                    'message' => 'Sukses menyimpan data',
                    'booking_room' => $data,
                    'token' => $snapToken
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating midtrans transaction:' . $e->getMessage(), 
                ], 500);
            }
        }



        if(!$data){
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Sukses menyimpan data',
            'booking_room' => $data,
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

    /**
     * Update the specified resource in storage.
     */
    public function update(bookingStoreRequest $request,$uuid)
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

    public function changeStatus($uuid) {
        $data = Booking::findByUuid($uuid);
        $data->status = 'Non Active';
        $data->save();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
