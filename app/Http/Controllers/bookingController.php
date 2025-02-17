<?php

namespace App\Http\Controllers;

use App\Models\Villa;
use App\Models\Booking;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Requests\bookingStoreRequest;
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
    
        $validatedData['booking_number'] = 'BOOK' . rand(100000, 999999);
        $validatedData['payment_status'] = 'Draft';
    
        $villa = Villa::find($validatedData['villa_id']);
    
        DB::beginTransaction(); 
    
        try {
            $data = Booking::create($validatedData);
    
            if ($request->payment_type == '1') {
                $villa->update(['status' => 'Non Active']);
            }
    
            if ($request->payment_type == '2') {
                Config::$serverKey = config('midtrans.serverKey');
                Config::$isProduction = false;
                Config::$isSanitized = true;
                Config::$is3ds = true;
    
                $params = [
                    'transaction_details' => [
                        'order_id' => $data->id,
                        'gross_amount' => $validatedData['total_price'],
                    ]
                ];
    
                $snapToken = Snap::getSnapToken($params);
    
                DB::commit(); 
    
                return response()->json([
                    'success' => true,
                    'message' => 'Sukses menyimpan data',
                    'booking_room' => $data,
                    'villa' => $villa,
                    'token' => $snapToken
                ]);
            }
    
            DB::commit(); 
    
            return response()->json([
                'success' => true,
                'message' => 'Sukses menyimpan data',
                'booking_room' => $data
            ]);
        } catch (\Exception $e) {
            DB::rollback(); 
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
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


    public function history(Request $request) {
        $per = $request->per ?? 10;
        $page = $request->page ? $request->page - 1 : 0;
    
        DB::statement('set @no=0+' . $page * $per);
        $data = Booking::whereNotNull('user_id')
            ->with(['villa'])
            ->when($request->search, function (Builder $query, string $search) {
                $query->where(function($q) use ($search) {
                    $q->where('booking_number', 'like', "%$search%")
                      ->orWhere('payment_type', 'like', "%$search%")
                      ->orWhereHas('villa', function ($query) use ($search) {
                          $query->where('name', 'like', "%$search%");
                      });
                });
            })
            ->where('payment_status', '=', 'Completed')
            ->paginate($per, ['*', DB::raw('@no := @no + 1 AS no')]);
    
        return response()->json($data);
    }

    public function status(Request $request, $uuid) 
    {
        $data = Villa::findByUuid($uuid);
        $data->update(['status' => 'Non Active']);

        $booking = Booking::findByUuid($request->uuid);
        $booking->update(['payment_status' => 'Completed']);

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

    public function changeStatus(Request $request,$uuid)
    {
        $data = Booking::where('uuid',$uuid)->firstOrFail();
        
        $data->payment_status = 'Completed';
        $data->save();

        return response()->json([
            'message' => 'Status updated successfully',
            'success' => true,
            'data' => $data
        ], 200);
    }
}
