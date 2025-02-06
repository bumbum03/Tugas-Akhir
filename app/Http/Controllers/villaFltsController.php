<?php

namespace App\Http\Controllers;

use App\Models\VillaFasilitas;
use App\http\Requests\villaFltsStoreRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class villaFltsController extends Controller
{

    public function get(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => VillaFasilitas::all()
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
        $data = VillaFasilitas::when($request->search, function(Builder $query, string $search){
            $query->where('nama', 'like', "%$search%");
        })->paginate($per, ['*', DB::raw('@no := @no +  1 AS no')]);

        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(villaFltsStoreRequest $request)
    {
        $validatedData = $request->validated();

        $villaFasilitas = VillaFasilitas::create($validatedData);
        return response()->json([
            'success' => true,
            'VillaFasilitas' => $villaFasilitas
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($uuid)
    {
        $villaFasilitas = VillaFasilitas::findByUuid($uuid);
        return response()->json([
            'VillaFasilitas' => $villaFasilitas
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(villaFltsStoreRequest $request, $uuid)
    {
        $villaFasilitas = VillaFasilitas::find($uuid);

        $validatedData = $request->validated();

        $villaFasilitas->update($validatedData);

        return response()->json([
            'success' => true,
            'VillaFasilitas' => $villaFasilitas
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        $villaFasilitas = VillaFasilitas::findByUuid($uuid);

        $villaFasilitas->delete();

        return response()->json([
            'success' => true
        ]);
    }
}
