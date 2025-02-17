<?php

namespace App\Http\Controllers;

use App\Http\Requests\VillaStoreRequest;
use App\Models\Villa;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use KodePandai\Indonesia\Models\City;
use App\Models\Booking;

class VillaController extends Controller
{
    public function get(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => Villa::all()
        ]);
    }


    public function index(Request $request)
    {
        $per = $request->per ?? 10;
        $page = $request->page ? $request->page - 1 : 0;

        DB::statement('set @no=0+' . $page * $per);
        $data = Villa::when($request->search, function (Builder $query, string $search) {
            $query->where('name', 'like', "%$search%")
                ->orWhere('kota_id', 'like', "%$search%")
                ->orWhere('alamat', 'like', "%$search%");
        })->latest()->paginate($per, ['*', DB::raw('@no := @no + 1 AS no')]);

        return response()->json($data);
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(VillaStoreRequest $request)
    {
        $validatedData = $request->validated();

        $villa = Villa::create($validatedData);
        $villa->villaFasilitas()->sync($request->fasilitas_id);

        if ($request->hasFile('image')) {
            $images = [];

            foreach ($request->file('image') as $image) {
                $images[] = ['image' => $image->store('image', 'public')];
            }

            $villa->villaImage()->createMany($images);
        }

        return response()->json([
            'success' => true,
            'villa' => $villa
        ]);
    }
    
    /**
     * Display the specified resource.
     */
    public function show($uuid)
    {
        $villa = Villa::with(['villaFasilitas','villaImage'])->where('uuid', $uuid)->first();
        $villa->fasilitas_id = $villa->villaFasilitas()->pluck('villa_fasilitas_id')->toArray();
        $villaImg = $villa->villaImage->pluck('image');
        return response()->json([
            'Villa' => $villa,
            'VillaImg' => $villaImg,
        ]);
    }

    public function showByCity (Request $request) {
        $data = Villa::with(['villaFasilitas','villaImage'])->where('kota_id', $request->city)->where('status', 'Active')->get();

        $city = City::where('code', $request->city)->first();

        return response()->json([
            'message' => 'Success fetching data',
            'data' => $data,
            'city' => $city
        ]);
    }
    
    /**
     * Update the specified resource in storage.
     */
    public function update(VillaStoreRequest $request, $uuid)
    {
        $Villa = Villa::findByUuid($uuid);

        $validatedData = $request->validated();
        $Villa->update($validatedData);

        if ($request->hasFile('image')) {
            $images = [];

            foreach ($request->file('image') as $image) {
                $images[] = ['image' => $image->store('image', 'public')];
            }

            $Villa->villaImage()->delete();
            $Villa->villaImage()->createMany($images);
        }


        return response()->json([
            'success' => true,
            'villa' => $Villa
        ]);
    }


    public function toggleStatus(Request $request, $uuid){
        $item  = Villa::where('uuid',$uuid)->firstOrFail();

        $item->status = $item->status === 'Active' ? 'Non Active' : 'Active';
        $item->save();

        return response()->json(['message' => 'Status updated successfully'], 200);
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy( $uuid)
    {
        $Villa = Villa::findByUuid($uuid);

        $Villa->delete();
        return response()->json([
            'success' => true
        ]);
    }
}
