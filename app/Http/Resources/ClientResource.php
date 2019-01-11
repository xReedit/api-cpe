<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'company_name' => $this->client->company_name,
            'company_number' => $this->client->company_number,
            'soap_type_id' => $this->client->soap_type_id,
            'soap_username' => $this->client->soap_username,
            'soap_password' => $this->client->soap_password,
            'certificate' => $this->client->certificate,
        ];
    }
}