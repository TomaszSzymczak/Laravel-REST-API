<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait RequestHelperTrait
{
    /**
     * @param array $data
     * @param array $rules
     * @return array
     * @throws ValidationException
     */
    public function validateOrFail(array $data, array $rules = [])
    {
        $commonRules = [
            'per_page' => 'required_with:page|integer',
            'page' => 'integer',
            'order' => 'in:asc,desc',
        ];
        
        $mergedRules = array_merge(
            $rules,
            $commonRules
        );
        
        $validator = Validator::make(
            $data,
            $mergedRules
        );
        
        if ($validator->fails()) {
            throw new ValidationException(
                $validator
            );
        }
        
        return $validator->validated();
    }
    
    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param array $validatedParams
     * @return Illuminate\Contracts\Support\Arrayable
     */
    public function maybePaginate($builder, $validatedParams)
    {
        if (!isset($validatedParams['per_page'])) {
            return $builder->get();
        }
        
        unset($validatedParams['page']);
        
        $paginator = $builder->paginate($validatedParams['per_page']);
        $paginator->setPath(
            url()->current() . '?' . http_build_query($validatedParams)
        );
        
        return $paginator;
    }
    
    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param array $validatedParams
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function maybeOrderBy($builder, $validatedParams)
    {
        // for convenience
        $orderBy = $validatedParams['order_by'] ?? null;
        $order = $validatedParams['order'] ?? null;
        
        if (!$orderBy && !$order) {
            return $builder;
        }
        
        if ($orderBy) {
            $builder->orderBy($orderBy, $order ?: 'asc');
        }
        else if ($order) {
            $builder->orderBy('id', $order);
        }
        
        return $builder;
    }
}

