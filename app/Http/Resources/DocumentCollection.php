<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class DocumentCollection extends ResourceCollection
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
            $send_xml = false;
            if($row->state_type_id === '01' && $row->external_id !== '') {
                $send_xml = true;
            }
            
            /*if($row->document_type_code === '03') {
                $send_xml = false;
            }*/
            
            return [
                'id' => $row->id,
                'ubl_version' => $row->ubl_version,
                'soap_type_description' => $row->soap_type->description,
                'document_type_code' => $row->document_type_code,
                'document_number' => $row->series.'-'.$row->number,
                'date_of_issue' => $row->date_of_issue->format('Y-m-d'),
                'external_id' => $row->external_id,
                'state_type_id' => $row->state_type_id,
                'state_type_description' => $row->state_type->description,
                'download_pdf' => $row->download_pdf,
                'download_xml' => $row->download_xml,
                'download_cdr' => $row->download_cdr,
                'send_xml' => $send_xml,
                'total' => $row->total,
                'loading' => false,
                'created_at' => $row->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $row->updated_at->format('Y-m-d H:i:s'),
            ];
        });
    }
}