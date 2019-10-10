<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Magazine extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'publisher_id' => $this->publisher_id,
            //'publisher_name' => $this->publisher->name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
