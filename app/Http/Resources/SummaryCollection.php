<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class SummaryCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function toArray($request)
    {
        return $this->collection->transform(function($row, $key) {
            $check_ticket = false;
            if($row->ticket) {
                $check_ticket = true;
            }
            return [
                'id' => $row->id,
                'date_of_issue' => $row->date_of_issue->format('Y-m-d'),
                'date_of_reference' => $row->date_of_reference->format('Y-m-d'),
                'state_type_id' => $row->state_type_id,
                'state_type_description' => $row->state_type->description,
                'ticket' => $row->ticket,
                'identifier' => $row->identifier,
                'download_xml' => $row->download_xml,
                'download_cdr' => $row->download_cdr,
                'check_ticket' => $check_ticket,
                'created_at' => $row->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $row->updated_at->format('Y-m-d H:i:s'),
            ];
        });
    }
}