<?php

declare(strict_types=1);

namespace Veronica;

use XML\Support\Element;
use XML\Support\Single;

class Invoice extends Document\Contract
{
    public function __construct(array $data = [])
    {
        parent::init($data, [
            'reason' => 'string',
            'reference' => 'string',
            'discount' => 'float',
            'tip' => 'float',
            'payments' => 'array',
            'withholding' => Element::class,
            'withholdings' => 'array',
        ]);
    }

    public function toArray(): array
    {
        return [
            'infoTributaria' => $this->taxInfo,
            'infoFactura' => [
                'fechaEmision' => $this->date,
                'dirEstablecimiento' => $this->supplier->address->location,
                'contribuyenteEspecial' => $this->supplier->specialTaxpayer,
                'obligadoContabilidad' => $this->requiredAccounting,
                'tipoIdentificacionComprador' => $this->customer->identification->type,
                'guiaRemision' => $this->reference,
                'razonSocialComprador' => $this->customer->name,
                'identificacionComprador' => $this->customer->identification->number,
                'direccionComprador' => $this->customer->address->main,
                'totalSinImpuestos' => $this->net,
                'totalDescuento' => $this->discount,
                'totalConImpuestos' => $this->taxes,
                'propina' => $this->tip ?? '0.00',
                'importeTotal' => $this->total, // preguntar
                'moneda' => $this->currency, // hay que preguntar si es un currency code
                'pagos' => $this->payments,
                'valorRetIva' => $this->withholding->vat ?? '0.00',
                'valorRetRenta' => $this->withholding->renta ?? '0.00',
            ],
            'detalles' => $this->items,
            'retenciones' => $this->witholdings,
            'infoAdicional' => $this->extraInfo,
        ];
    }

    protected function getExtraInfo(): array
    {
        return [
            'campoAdicional' => [
                new Single($this->customer->phone, ['nombre' => 'Telefono']),
                new Single($this->customer->email, ['nombre' => 'Email']),
                new Single($this->comments, ['nombre' => 'Observaciones' ])
            ]
        ];
    }

    protected function getItems(iterable $items): array
    {
        return $this->mapItems('codigoPrincipal', $items);
    }

    protected function mapTaxes(iterable $taxes): array
    {
        return $this->map($taxes, function ($tax) {
            return [
                'codigo' => $tax->code,
                'codigoPorcentaje' => $tax->rate_code,
                'descuentoAdicional' => $tax->discount,
                'baseImponible' => $tax->base,
                'tarifa' => $tax->rate,
                'valor' => $tax->amount,
                'valorDevolucionIva' => $tax->return,
            ];
        });
    }

    protected function mapLineTaxes(iterable $taxes): array
    {
        return $this->map($taxes, function ($tax) {
            return [
                'codigo' => $tax->code,
                'codigoPorcentaje' => $tax->rate_code,
                'tarifa' => $tax->rate,
                'baseImponible' => $tax->base,
                'valor' => $tax->amount,
            ];
        });
    }

    protected function getPayments(?iterable $payments): array
    {
        return [
            'pago' => $this->map($payments ?? [], function (iterable $payment) {
                return [
                    'formaPago' => $payment->method,
                    'total' => $payment->amount,
                    'plazo' => $payment->due_days,
                    'unidadTiempo' => $payment->due_days !== null ? 'dias' : null,
                ];
            })
        ];
    }

    protected function  getWitholdings(?iterable $taxes)
    {
        return [
            'retencion' => $this->map($taxes ?? [], function ($tax) {
                return [
                    'codigo' => $tax->code,
                    'codigoPorcentaje' => $tax->rate_code,
                    'tarifa' => $tax->rate,
                    'valor' => $tax->amount,
                ];
            })
        ];
    }

    protected function getName(): string
    {
        return 'factura';
    }

    protected function getType(): string
    {
        return DOC_INVOICE;
    }
}
