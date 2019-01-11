<?php

namespace App\Http\Middleware;

use App\Core\Helpers\NumberHelper;
use Closure;

class TransformInputBackup
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $request->replace($this->originalAttribute($request->all()));
        return $next($request);
    }

    private function originalAttribute($inputs)
    {
        // dd($inputs['items']);
        $details = [];
        foreach ($inputs['items'] as $row)
        {
            $additional = null; //v2.1
            if(array_key_exists('DatosAdicionales', $row)) {
                foreach ($row['DatosAdicionales'] as $add)
                {
                    $additional[] = [
                        'code' => $add['Codigo'],
                        'name' => $add['Nombre'],
                        'value' => $add['Valor'],
                        'start_date' => $add['FechaInicio'],
                        'end_date' => $add['FechaFin'],
                        'duration' => $add['Duracion'],
                    ];
                }
            }
            $details[] = [
                'item_id' => $row['datos_del_detalle_o_item']['numero_de_orden_del_item'],
                'item_description' => $row['informacion_adicional_gastos_art_37_renta']['descripcion_detallada_del_servicio_prestado_bien_vendido_o_cedido_en_uso_indicando_las_caracteristicas'],
                'item_code' => $row['datos_del_detalle_o_item']['codigo_producto_de_sunat'],
                'carriage_plate' => array_key_exists('informacion_adicional_gastos_art_37_renta___numero_de_placa_del_vehiculo', $inputs['items'])?$inputs['items']['informacion_adicional_gastos_art_37_renta___numero_de_placa_del_vehiculo']:null,
                'unit_type_code' => $row['datos_del_detalle_o_item']['unidad_de_medida_por_item'], //unidad de medida
                'quantity' => $row['datos_del_detalle_o_item']['cantidad_de_unidades_por_item'],
                'unit_value' => array_key_exists('valor_unitario_por_item', $row['informacion_adicional_gastos_art_37_renta'])?$row['informacion_adicional_gastos_art_37_renta']['valor_unitario_por_item']:0,
                'price_type_code' => $row['informacion_adicional_gastos_art_37_renta']['precio_de_venta_unitario_por_item_y_codigo']['codigo_de_tipo_de_precio'], //CodigoTipoPrecio
                'unit_price' => array_key_exists('monto_de_valor_referencial_unitario', $row['informacion_adicional_gastos_art_37_renta']['valor_referencial_unitario_por_item_en_operaciones_no_onerosas_y_codigo'])?$row['informacion_adicional_gastos_art_37_renta']['valor_referencial_unitario_por_item_en_operaciones_no_onerosas_y_codigo']['monto_de_valor_referencial_unitario']:0,
                'affectation_igv_type_code' => $row['informacion_adicional_gastos_art_37_renta']['afectacion_al_igv_por_item']['afectacion_al_igv'],
                'total_igv' => array_key_exists('monto_de_igv', $row['informacion_adicional_gastos_art_37_renta']['afectacion_al_igv_por_item'])?$row['informacion_adicional_gastos_art_37_renta']['afectacion_al_igv_por_item']['monto_de_igv']:0,
                'percentage_igv' => array_key_exists('PorcentajeIgv', $row['informacion_adicional_gastos_art_37_renta']['afectacion_al_igv_por_item'])?$row['PorcentajeIgv']:0,
                'system_isc_type_code' => array_key_exists('tipo_de_sistema_de_isc', $row['informacion_adicional_gastos_art_37_renta']['sistema_de_isc_por_item'])?$row['informacion_adicional_gastos_art_37_renta']['sistema_de_isc_por_item']['tipo_de_sistema_de_isc']:null,
                'total_isc' => array_key_exists('monto_de_isc', $row['informacion_adicional_gastos_art_37_renta']['sistema_de_isc_por_item'])?$row['informacion_adicional_gastos_art_37_renta']['sistema_de_isc_por_item']['monto_de_isc']:0,
                'charge_type_code' => array_key_exists('CodigoTipoCargo', $row)?$row['CodigoTipoCargo']:null, //v2.1
                'charge_percentage' => array_key_exists('PorcentajeCargo', $row)?$row['PorcentajeCargo']:0, //v2.1
                'total_charge' => array_key_exists('TotalCargo', $row)?$row['TotalCargo']:0, //v2.1
                'discount_type_code' => array_key_exists('CodigoTipoDescuento', $row)?$row['CodigoTipoDescuento']:null, //v2.1
                'discount_percentage' => array_key_exists('PorcentajeDescuento', $row)?$row['PorcentajeDescuento']:0, //v2.1
                'total_discount' => array_key_exists('monto_del_descuento', $row['informacion_adicional_gastos_art_37_renta']['descuentos_por_item'])?$row['informacion_adicional_gastos_art_37_renta']['descuentos_por_item']['monto_del_descuento']:0,
                'subtotal' => array_key_exists('valor_de_venta_por_item', $row['informacion_adicional_gastos_art_37_renta'])?$row['informacion_adicional_gastos_art_37_renta']['valor_de_venta_por_item']:0,
                'total' => array_key_exists('valor_de_venta_por_item', $row['informacion_adicional_gastos_art_37_renta'])?$row['informacion_adicional_gastos_art_37_renta']['valor_de_venta_por_item']:0, //v2.1
                'first_housing_contract_number' => array_key_exists('informacion_adicional_a_nivel_de_item_gastos_intereses_hipotecarios_primera_vivienda', $row)?$row['informacion_adicional_a_nivel_de_item_gastos_intereses_hipotecarios_primera_vivienda']['nro_de_contrato']:null,
                'first_housing_credit_date' => array_key_exists('informacion_adicional_a_nivel_de_item_gastos_intereses_hipotecarios_primera_vivienda', $row)?$row['informacion_adicional_a_nivel_de_item_gastos_intereses_hipotecarios_primera_vivienda']['fecha_de_otorgamiento_del_credito']:null,
            ];
        }

        $prepayments = null;
        if(array_key_exists('informacion_adicional_anticipos', $inputs)) {
                $serie_number = explode('-',$inputs['informacion_adicional_anticipos']['informacion_prepagado_o_anticipado']['serie_y_numero_de_documento_que_se_realizo_el_anticipo']);
                $prepayments[] = [
                    'series' => $serie_number[0],
                    'number' => $serie_number[1],
                    'document_type_code' => $inputs['informacion_adicional_anticipos']['informacion_prepagado_o_anticipado']['tipo_de_comprobante_que_se_realizo_el_anticipo'],
                    'currency_type_code' => $inputs['informacion_adicional_anticipos']['informacion_prepagado_o_anticipado']['tipo_de_documento_del_emisor_del_anticipo'],
                    'total' => array_key_exists('total_anticipos', $inputs['informacion_adicional_anticipos'])?$inputs['informacion_adicional_anticipos']['total_anticipos']:0,
                ];
        }

        $additional_documents = null;
        if(array_key_exists('DocumentosAdicionalesRelacionados', $inputs)) {
            foreach ($inputs['DocumentosAdicionalesRelacionados'] as $row)
            {
                $additional_documents[] = [
                    'number' => $row['NumeroDocumento'],
                    'document_type_code' => $row['CodigoTipoDocumento'],
                ];
            }
        }

        $perception = null;
        if(array_key_exists('informacion_adicional_percepciones', $inputs)) {
            $perception = [
                'account' => $inputs['informacion_adicional_percepciones']['importe_de_la_percepcion_en_moneda_nacional']['codigo_de_tipo_de_monto'],
                'reception_type_code' => $inputs['informacion_adicional_percepciones']['importe_de_la_percepcion_en_moneda_nacional']['codigo_de_regimen_de_percepcion'],
                'base' => $inputs['informacion_adicional_percepciones']['importe_de_la_percepcion_en_moneda_nacional']['base_imponible_percepcion'],
                'total_perception' => $inputs['informacion_adicional_percepciones']['importe_de_la_percepcion_en_moneda_nacional']['monto_de_la_percepcion'],
                'total' => $inputs['informacion_adicional_percepciones']['importe_de_la_percepcion_en_moneda_nacional']['monto_total_incluido_la_percepcion'],
            ];
        }

        $detraction = null; // v2.1
        if(array_key_exists('Detraccion', $inputs)) {
            $detraction = [
                'account' => $inputs['Detraccion']['CuentaBancoNacion'],
                'code' => $inputs['Detraccion']['CodigoBienServicio'],
                'percentage' => $inputs['Detraccion']['PorcentajeDetraccion'],
                'total' => $inputs['Detraccion']['TotalDetraccion'],
            ];
        }

        $optional = [];
        if(array_key_exists('extras', $inputs)) {
            $optional = [
                'logo' => array_key_exists('logo', $inputs['extras'])?$inputs['extras']['logo']:null,
            ];
        } else {
            $optional = [
                'logo' => null,
            ];
        }

        $document_base = [];

        /*
         * Invoice
         */
        if (in_array($inputs['datos_de_la_factura_electronica']['tipo_de_documento'], ['01', '03'])) {
            $document_base = [
                'operation_type_code' => $inputs['informacion_adicional']['tipo_de_operacion'],
                'date_of_due' => array_key_exists('fecha_de_vencimiento', $inputs['datos_de_la_factura_electronica'])?$inputs['datos_de_la_factura_electronica']['fecha_de_vencimiento']:null,
                'base_global_discount' => array_key_exists('descuentos_globales', $inputs['totales'])?$inputs['totales']['descuentos_globales']:0,
                'percentage_global_discount' => array_key_exists('PorcentajeDescuentoGlobal', $inputs)?$inputs['PorcentajeDescuentoGlobal']:0, //v2.1
                'total_global_discount' => array_key_exists('descuentos_globales', $inputs['totales'])?$inputs['totales']['descuentos_globales']:0, //TotalDescuentoGlobal
                'total_free' => array_key_exists('totales', $inputs)?$inputs['totales']['total_valor_de_venta_operaciones_gratuitas']['total_valor_venta_operaciones_gratuitas']:0,
                'total_prepayment' => array_key_exists('total_anticipos', $inputs['informacion_adicional_anticipos'])?$inputs['informacion_adicional_anticipos']['total_anticipos']:0,
                'purchase_order' => array_key_exists('NumeroOrdenCompra', $inputs)?$inputs['NumeroOrdenCompra']:'', //v2.1
                'detraction' => $detraction, // v2.1
                'perception' => $perception,
                'prepayments' => $prepayments,
            ];
        }

        /*
         * Note Credit, Note Debit
         */
        if (in_array($inputs['datos_de_la_nota_de_credito']['tipo_de_documento'], ['07']) OR in_array($inputs['datos_de_la_nota_de_debito']['tipo_de_documento'], ['08'])) {

            if ($inputs['datos_de_la_nota_de_credito']['serie_y_numero_de_documento_afectado'] === '07') {
                $serie_number_note = explode('-',$inputs['datos_de_la_nota_de_credito']['serie_y_numero_de_documento_afectado']);
            } else {
                $serie_number_note = explode('-',$inputs['datos_de_la_nota_de_debito']['serie_y_numero_de_documento_afectado']);
            }


            $document_base = [
                'note_type_code' => ($inputs['datos_de_la_nota_de_credito']['serie_y_numero_de_documento_afectado'] === '07')?$inputs['datos_de_la_nota_de_credito']['codigo_de_tipo_de_nota_de_credito']:$inputs['datos_de_la_nota_de_debito']['codigo_de_tipo_de_nota_de_debito'], //CodigoTipoNotaDebito //CodigoTipoNotaCredito
                'description' => ($inputs['datos_de_la_nota_de_credito']['serie_y_numero_de_documento_afectado'] === '07')?$inputs['datos_del_detalle_de_la_nota_de_credito__motivo_o_sustento']:$inputs['datos_del_detalle_de_la_nota_de_debito__motivo_o_sustento'], //MotivoDeNotaDebito //MotivoDeNotaCredito
                //'affected_document_type_code' => $inputs['CodigoTipoDocumentoAfectado'], //CodigoTipoDocumentoAfectado //v2.1
                'affected_document_series' => $serie_number_note[0], //SerieDocumentoAfectado
                'affected_document_number' => $serie_number_note[1], //NumeroDocumentoAfectado
                'total_global_discount' => array_key_exists('descuentos_globales', $inputs['totales'])?$inputs['totales']['descuentos_globales']:0, //TotalDescuentoGlobal
                'total_prepayment' => array_key_exists('total_anticipos', $inputs['informacion_adicional_anticipos'])?$inputs['informacion_adicional_anticipos']['total_anticipos']:0 //TotalAnticipos
            ];
        }

        $guides = [];
        if (array_key_exists('guia_de_remision_relacionada_con_la_operacion_que_se_factura', $inputs)) {
            foreach ($inputs['guia_de_remision_relacionada_con_la_operacion_que_se_factura'] as $row)
            {
                $guides[] = [
                    'number' => $row['numero_de_guia'],
                    'document_type_code' => $row['tipo_de_documento'],
                ];
            }
        }

        $legends[] = [
            'code' => 1000,
            'description' => NumberHelper::convertToLetter($inputs['totales']['importe_total_de_la_venta_cesion_en_uso_o_del_servicio_prestado'])
        ];
        if (array_key_exists('leyendas', $inputs['informacion_adicional'])) {
            foreach ($inputs['informacion_adicional']['leyendas'] as $row)
            {
                $legends[] = [
                    'code' => $row['codigo_de_la_leyenda'],
                    'description' => $row['descripcion_de_la_leyenda'],
                ];
            }
        }

        $serie_number = explode('-',$inputs['datos_de_la_factura_electronica']['numeracion_conformada_por_serie_y_numero_correlativo']);

        $original_attributes = [
            'document' => [
                'user_id' => auth()->user()->id,
                'external_id' => '',
                'state_type_id' => '01',
                'ubl_version' => $inputs['datos_de_la_factura_electronica']['version_del_ubl'],//$inputs['UblVersion'],
/**/            'soap_type_id' => array_key_exists('CodigoTipoSoap', $inputs)?$inputs['CodigoTipoSoap']:'01',
/**/            'document_type_code' => $inputs['informacion_adicional']['tipo_de_operacion'],
                'date_of_issue' => $inputs['datos_de_la_factura_electronica']['fecha_de_emision'], //$inputs['FechaEmision'],
                'time_of_issue' => $inputs['datos_de_la_factura_electronica']['hora_de_emision'], //$inputs['HoraEmision'],
                'series' => $serie_number[0],//$inputs['SerieDocumento'],
                'number' => $serie_number[1],//$inputs['NumeroDocumento'],
                'currency_type_code' => $inputs['datos_de_la_factura_electronica']['tipo_de_moneda_en_la_cual_se_emite_la_factura_electronica'],//$inputs['CodigoTipoMoneda'],
                'total_exportation' => array_key_exists('total_valor_de_venta_exportacion', $inputs['totales'])?$inputs['totales']['total_valor_de_venta_exportacion']['monto']:0, //export
                'total_taxed'       => array_key_exists('total_valor_de_venta_operaciones_gravadas', $inputs['totales'])?$inputs['totales']['total_valor_de_venta_operaciones_gravadas']['monto']:0, //gravadas
                'total_unaffected'  => array_key_exists('total_valor_de_venta_operaciones_inafectas', $inputs['totales'])?$inputs['totales']['total_valor_de_venta_operaciones_inafectas']['monto']:0, //inafectas
                'total_exonerated'  => array_key_exists('total_valor_de_venta_operaciones_exoneradas', $inputs['totales'])?$inputs['totales']['total_valor_de_venta_operaciones_exoneradas']['monto']:0, //exonerada
                'total_igv' => array_key_exists('sumatoria_igv', $inputs['totales'])?$inputs['totales']['sumatoria_igv']['sumatoria_igv_amount']:0, //SumatoriaIgv
                'total_isc' => array_key_exists('sumatoria_isc', $inputs['totales'])?$inputs['totales']['sumatoria_isc']['sumatoria_isc_amount']:0, //SumatoriaIsc
                'total_other_taxes' => array_key_exists('sumatoria_otros_tributos', $inputs['totales'])?$inputs['totales']['sumatoria_otros_tributos']['sumatoria_otros_tributos_amount']:0, //sumatoria_otros_tributos
                'total_other_charges' => array_key_exists('sumatoria_otros_cargos', $inputs['totales'])?$inputs['totales']['sumatoria_otros_cargos']:0, //sumatoria_otros_cargos
                'total_discount' => array_key_exists('total_descuentos', $inputs['totales'])?$inputs['totales']['total_descuentos']['monto']:0, //TotalDescuentos
/**/            'total_value_sale' => array_key_exists('TotalValorVenta', $inputs)?$inputs['TotalValorVenta']:0,
/**/            'total_price_sale' => array_key_exists('TotalPrecioVenta', $inputs)?$inputs['TotalPrecioVenta']:0,
                'subtotal' => 0,
                'total' => $inputs['totales']['importe_total_de_la_venta_cesion_en_uso_o_del_servicio_prestado'], //importe_total_de_la_venta_cesion_en_uso_o_del_servicio_prestado
                'company' => [
                    'trade_name' => $inputs['datos_del_emisor']['nombre_comercial'],
                    'name' => $inputs['datos_del_emisor']['apellidos_y_nombres_denominacion_o_razon_social'],
                    'identity_document_type_code' => $inputs['datos_del_emisor']['numero_de_ruc']['tipo_de_documento'],
                    'number' => $inputs['datos_del_emisor']['numero_de_ruc']['numero_ruc'],
                ],
                'establishment' => [
                    'code' => array_key_exists('codigo_del_domicilio_fiscal_o_de_local_anexo_del_emisor', $inputs['datos_adicionales_lugar_en_el_que_se_entrega_el_bien_o_se_presta_el_servicio'])?$inputs['datos_adicionales_lugar_en_el_que_se_entrega_el_bien_o_se_presta_el_servicio']['codigo_del_domicilio_fiscal_o_de_local_anexo_del_emisor']:'',
//                    'location_code' => array_key_exists('CodigoUbigeo', $inputs['Emisor'])?$inputs['Emisor']['CodigoUbigeo']:'',
//                    'address' => array_key_exists('Direccion', $inputs['Emisor'])?$inputs['Emisor']['Direccion']:'',
//                    'department' => array_key_exists('Departamento', $inputs['Emisor'])?$inputs['Emisor']['Departamento']:'',
//                    'province' => array_key_exists('Provincia', $inputs['Emisor'])?$inputs['Emisor']['Provincia']:'',
//                    'district' => array_key_exists('Distrito', $inputs['Emisor'])?$inputs['Emisor']['Distrito']:'',
//                    'country_code' => array_key_exists('CodigoPais', $inputs['Emisor'])?$inputs['Emisor']['CodigoPais']:'',
                ],
                'customer' => [
                    'identity_document_type_code' => $inputs['datos_del_cliente_o_receptor']['tipo_y_numero_de_documento_de_identidad_del_adquirente_o_usuario']['tipo_de_documento'],
                    'number' => $inputs['datos_del_cliente_o_receptor']['tipo_y_numero_de_documento_de_identidad_del_adquirente_o_usuario']['numero_de_documento'],
                    'name' => $inputs['datos_del_cliente_o_receptor']['apellidos_y_nombres_denominacion_o_razon_social_del_adquirente_o_usuario'], //cliente
                ],
                'details' => $details,
                'guides' => $guides,
                'additional_documents' => $additional_documents,
                'legends' => $legends,
                'filename' => '',
                'hash' => '',
                'qr' => '',
                'optional' => $optional,
            ],
            'document_base' => $document_base
        ];

        return $original_attributes;
    }


}
