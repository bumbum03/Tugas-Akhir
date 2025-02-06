<?php

namespace App\Http\Controllers;

use App\Models\VillaImages;
use App\http\Requests\VillaImageStoreRequest;
use App\Models\Villa;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class villaImageController extends Controller
{

    public function get(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => VillaImages::all()
        ]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request){
        $per = $request->per ?? 10;
        $page = $request->page ? $request->page - 1 : 0;

        DB::statement('set @no=0+' . $page * $per);
        $data = VillaImages::when($request->search, function(Builder $query, string $search){
            $query->where('image', 'like', "%$search%")
            ->orWhere('villa_id', 'like', "%$search%");
        })->whereHas('villa', function ($q) use ($request) {
            $q->where('uuid', $request->uuid);
        })->paginate($per, ['*', DB::raw('@no := @no + 1 AS no')]);

        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validation = $request->validated();

        if($request->hasFile('image')){
            $validatedData['image'] = $request->file('image')->store('image', 'public');
        }
        $VillaImage = VillaImages::create($validatedData);
        return response()->json([
            'success' => true,
            'VillaImage' => $VillaImage
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($uuid)
    {
        $data = VillaImages::findByUuid($uuid);
        return response()->json([
            'VillaImage' => $data
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VillaImageStoreRequest $request, $uuid)
    {
        $VillaImage = VillaImages::findByUuid($uuid);

        $ValidatedData = $request->validated();

        if($request->hasFile('image')){
            if($VillaImage->image){
                Storage::disk('public')->delete($VillaImage->image);
            }
            $ValidatedData['image'] = $request->file('image')->store('image', 'public');
        } else {
            if($VillaImage->image){
                Storage::disk('public')->delete($VillaImage->image);
                $validatedData['image'] = null;
            }
        }

        $VillaImage->update($ValidatedData);

        return response()->json([
            'success' => true,
            'VillaImage' => $VillaImage
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        $VillaImage = VillaImages::findByUuid($uuid);

        if($VillaImage->image){
            Storage::disk('public')->delete($VillaImage->image);
        }

        $VillaImage->delete();

        return response()->json([
            'success' => true
        ]);
    }
}
