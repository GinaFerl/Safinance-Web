<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'username' => $this->username,
            'email' => $this->email,
            'profile_image' => $this->profileImage,
            'role' => $this->role,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'transactions_count' => $this->whenLoaded('transactions', function() {
                return $this->transactions->count();
            }),
            'monthly_reports_count' => $this->whenLoaded('monthlyReports', function() {
                return $this->monthlyReports->count();
            }),
        ];
    }
}
