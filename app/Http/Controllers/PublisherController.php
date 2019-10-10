<?php

namespace App\Http\Controllers;

use App\Publisher;
use Illuminate\Http\Request;
use App\Http\Resources\Publisher as PublisherResource;
use App\Http\Controllers\Traits\RequestHelperTrait;

class PublisherController extends Controller
{
    use RequestHelperTrait;
    
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        /** @var array */
        $validatedParams = $this->validateOrFail($request->all(), [
            'order_by' => 'in:id,name',
        ]);
        
        if (0 === sizeof($validatedParams)) {
            return PublisherResource::collection(
                Publisher::all()
            );
        }
        
        $builder = Publisher::query();
        
        $builder = $this->maybeOrderBy($builder, $validatedParams);
        $results = $this->maybePaginate($builder, $validatedParams);
        
        return PublisherResource::collection($results);
    }
}
