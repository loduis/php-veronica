<?php

declare(strict_types=1);

namespace Veronica;

class Invoice extends Http\Request
{
    protected const TYPE = '01';

    protected string $path = 'comprobantes/facturas';

    public function send(iterable $doc)
    {
        $doc = arr_obj($doc);
        $customer = $doc->customer;
        $supplier = $doc->supplier;
        $doc->type = static::TYPE;

        $data = [
            'infoTributaria' => [
                'ambiente' => $this->environment,
                'tipoEmision' => 1, // normal es una constante por ahora
                'razonSocial' => $supplier->name,
                'nombreComercial' => $supplier->tradename,
                'ruc' => $supplier->identification->number,
                'claveAcceso' => $this->getKeyAccess($doc),
            ],
            'infoFactura' => [
                'fechaEmision' => $doc->date,
                'moneda' => $doc->currencyCode,
                'totalSinImpuestos' => $doc->net,
                'totalDescuento' => $doc->discount,
                // supplier
                'dirEstablecimiento' => $supplier->address->main,
                // customer
                'razonSocialComprador' => $customer->name,
                'identificacionComprador' => $customer->identification->number,
                'tipoIdentificacionComprador' => $customer->identification->type,
                'direccionComprador' => $customer->address->main,
                'pagos' => $this->preparePayments($doc->payments),
            ],
            'detalles' => $this->prepareItems($doc->items),
            'retenciones' => $this->prepareTaxes($doc->withholdings),

        ];

        return $data;
    }

    protected function prepareTaxes(?iterable $taxes): iterable
    {
        $data = [];
        foreach ($taxes ?? [] as $tax) {
            $taxes[] = [
                'codigo' => $tax->code,
                'tarifa' => $tax->rate,
                'baseImponible' => $tax->base,
                'valor' => $tax->amount,
            ];
        }

        return $data;
    }

    protected function prepareItems(iterable $items)
    {
        $data = [];
        foreach ($items as $item) {
            $line = [
                'codigoPrincipal' => $item->code,
                'descripcion' => $item->description,
                'unidadMedida' => $item->unit,
                'cantidad' => $item->qty,
                'precioUnitario' => $item->price,
                'descuento' => $item->discount,
                'precioTotalSinImpuesto' => $item->netPrice,
                'impuestos' => $this->prepareTaxes($item['taxes'] ?? []),
            ];
            $data[] = $line;
        }

        return $data;
    }

    protected function preparePayments(?iterable $payments)
    {
        $data = [];
        foreach ($payments ?? [] as $payment) {
            $data[] = [
                'formaPago' => $payment->method,
                'total' => $payment->amount,
                'plazo' => $payment->due,
                'unidadTiempo' => 'unidadTiem',
            ];
        }

        return $data;
    }

    protected function getKeyAccess(iterable $doc)
    {
        $entries = [
            str_replace('/', '', $doc->date),
            $doc->type,
        ];
    }
}
