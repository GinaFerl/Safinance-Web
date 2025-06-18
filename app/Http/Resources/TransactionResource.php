<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            'title' => $this->title,
            'amount' => $this->amount,
            'type' => $this->type,
            'category' => $this->category,
            'date' => $this->date->format('Y-m-d H:i:s'),
            'description' => $this->description,
            'ticket_number' => $this->ticket_number,
            'user_id' => $this->user_id,
            'user' => new UserResource($this->whenLoaded('user')),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
