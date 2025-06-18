<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MonthlyReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
         return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user' => new UserResource($this->whenLoaded('user')),
            'month' => $this->month,
            'total_income' => $this->total_income,
            'total_expense' => $this->total_expense,
            'cash_balance' => $this->cash_balance,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
