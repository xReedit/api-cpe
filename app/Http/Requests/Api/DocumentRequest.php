<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class DocumentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
//        dd(request()->all());
        return [
            'document.document_type_code' => [
                'required'
                ]
//            ],
//            'document.date_of_issue' => [
//                'required'
//            ],
//            'document.series' => [
//                'required'
//            ],
//            'document.number' => [
//                'required'
//            ],
//            'document.currency_type_code' => [
//                'required'
//            ],
//            'document.total_exportation' => [
//                'numeric'
//            ],
//            'document.total_taxed' => [
//                'numeric'
//            ],
//            'document.total_unaffected' => [
//                'numeric'
//            ],
//            'document.total_exonerated' => [
//                'numeric'
//            ],
//            'document.total_igv' => [
//                'required',
//                'numeric'
//            ],
//            'document.total_isc' => [
//                'numeric'
//            ],
//            'document.total_other_taxes' => [
//                'numeric'
//            ],
//            'document.total_other_charges' => [
//                'numeric'
//            ],
//            'document.total_discount' => [
//                'numeric'
//            ],
//            'document.subtotal' => [
//                'numeric'
//            ],
//            'document.total' => [
//                'required',
//                'numeric'
//            ],
        ];
//        $this->companyRules()+
//        $this->establishmentRules()+
//        $this->customerRules()+
//        $this->detailsRules()+
//        $this->invoiceRules();
    }

    private function companyRules()
    {
        return [
            'document.company' => [
                'required',
                'array'
            ],
            'document.company.name' => [
                'required'
            ],
            'document.company.trade_name' => [
                'required'
            ],
            'document.company.number' => [
                'required'
            ]
        ];
    }

    private function establishmentRules()
    {
        return [
            'document.establishment' => [
                'required',
                'array'
            ],
            'document.establishment.country_code' => [
                'required'
            ],
            'document.establishment.location_code' => [
                'required'
            ],
            'document.establishment.address' => [
                'required'
            ],
            'document.establishment.department' => [
                'required'
            ],
            'document.establishment.province' => [
                'required'
            ],
            'document.establishment.district' => [
                'required'
            ],
        ];
    }

    private function customerRules()
    {
        return [
            'document.customer' => [
                'required',
                'array'
            ],
            'document.customer.identity_document_type_code' => [
                'required'
            ],
            'document.customer.number' => [
                'required'
            ],
            'document.customer.name' => [
                'required'
            ],
            'document.customer.trade_name' => [

            ],
        ];
    }

    private function detailsRules()
    {
        return [
            'document.details.*.item_id' => [
                'required'
            ],
            'document.details.*.item_description' => [
                'required'
            ],
            'document.details.*.item_code' => [

            ],
            'document.details.*.unit_type_code' => [
                'required'
            ],
            'document.details.*.quantity' => [
                'required',
                'integer'
            ],
            'document.details.*.unit_value' => [
                'required',
                'numeric'
            ],
            'document.details.*.price_type_code' => [
                'required'
            ],
            'document.details.*.unit_price' => [
                'required',
                'numeric'
            ],
            'document.details.*.affectation_igv_type_code' => [
                'required',
            ],
            'document.details.*.total_igv' => [
                'required',
                'numeric'
            ],
            'document.details.*.system_isc_type_code' => [

            ],
            'document.details.*.total_isc' => [
                'numeric'
            ],
//            'details.*.charge_or_discount_percentage' => [
//                'numeric'
//            ],
//            'details.*.total_charge_or_discount' => [
//                'numeric'
//            ],
            'document.details.*.subtotal' => [
                'required',
                'numeric'
            ],
            'document.details.*.total' => [
                'required',
                'numeric'
            ]
        ];
    }

    private function invoiceRules()
    {
        return [
            'invoice' => [
                'required',
                'array'
            ],
            'invoice.operation_type_code' => [
                'required'
            ],
            'invoice.date_of_due' => [
                'required'
            ],
            'invoice.total_global_discount' => [
                'numeric'
            ],
//            'invoice.total_discount' => [
//                'numeric'
//            ],
            'invoice.total_free' => [
                'numeric'
            ],
            'invoice.total_prepayment' => [
                'numeric'
            ],
            'invoice.purchase_order' => [

            ],
        ];
    }

}
