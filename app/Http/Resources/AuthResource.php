<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string $token
 */
class AuthResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user' => [
                'id'    => $this->id,
                'name'  => $this->name,
                'mobile'=> $this->mobile,
                'email' => $this->email,
            ],
            'token' => $this->token,
        ];
    }
}
