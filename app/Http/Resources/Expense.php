<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Category as CategoryResource;
use App\Category;
use Carbon\Carbon;

class Expense extends JsonResource
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
            'category' => $this->type === 0 ? new CategoryResource(Category::find($this->category_id)) : null,
            'name' => $this->name,
            'amount' => $this->amount,
            'type' => $this->type === 0 ? 'expense' : 'income',
            'date' => Carbon::createFromFormat('Y-m-d H:i:s', $this->date)->format('d.m.Y. H:i')
        ];
    }
}
