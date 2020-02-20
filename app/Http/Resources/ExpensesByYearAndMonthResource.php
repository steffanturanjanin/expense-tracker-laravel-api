<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExpensesByYearAndMonthResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data = [];

        $timeline = $this->resource;

        foreach ($timeline as $year => $months) {
            $data[$year] = [];

            foreach ($months as $month) {
                $data[$year][] = [
                    'number' => $month['number'],
                    'name' => $month['name'],
                    'expenses' => Expense::collection($month['expenses'])
                ];
            }
        }

        return $data;
    }
}
