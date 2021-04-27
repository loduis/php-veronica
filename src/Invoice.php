<?php

declare(strict_types=1);

namespace Veronica;

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

    protected function getName(): string
    {
        return 'factura';
    }

    protected function getType(): string
    {
        return '01';
    }
}
