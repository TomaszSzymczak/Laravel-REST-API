<?php

namespace App\Http\Controllers;

use App\Magazine;
use Illuminate\Http\Request;
use App\Http\Resources\Magazine as MagazineResource;
use App\Http\Controllers\Traits\RequestHelperTrait;

class MagazineController extends Controller
{
    use RequestHelperTrait;
    
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $validatedParams = $this->validateOrFail($request->all(), [
            'publisher_id' => 'integer',
            'name_part' => 'string|min:3',
        ]);
        
        $builder = Magazine::query();

        if ($request->name_part) {
            $builder->where('name', 'like', '%' . $request->name_part . '%');
        }

        if ($request->publisher_id) {
            $builder->where('publisher_id', (int) $request->publisher_id);
        }

        $builder->orderBy('name', 'asc');

        $results = $this->maybePaginate($builder, $validatedParams);

        return MagazineResource::collection($results);
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $magazine = Magazine::findOrFail($id);
        return new MagazineResource($magazine);
    }
}
