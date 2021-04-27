<?php

declare(strict_types=1);

namespace Veronica;

use XML\Support\Element;

class CreditNote extends Document\Contract
{

    public function __construct(array $data = [])
    {
        parent::init($data, [
            'reason' => 'string',
            'reference' => Element::class
        ]);
    }

    public function toArray(): array
    {
        return [
            'infoTributaria' => $this->taxInfo,
            'infoNotaCredito' => [
                'fechaEmision' => $this->date,
                'dirEstablecimiento' => $this->supplier->address->location,
                'contribuyenteEspecial' => $this->supplier->specialTaxpayer,
                'obligadoContabilidad' => $this->requiredAccounting,
                'codDocModificado' => $this->reference->type,
                'numDocModificado' => $this->reference->id,
                'fechaEmisionDocSustento' => $this->reference->date,
                'tipoIdentificacionComprador' => $this->customer->identification->type,
                'razonSocialComprador' => $this->customer->name,
                'identificacionComprador' => $this->customer->identification->number,
                'totalSinImpuestos' => $this->net,
                'totalConImpuestos' => $this->taxes,
                'valorModificacion' => $this->total, // preguntar
                'moneda' => $this->currency, // hay que preguntar si es un currency code
                'motivo' => $this->reason
            ],
            'detalles' => $this->items,
            'infoAdicional' => $this->extraInfo,
        ];
    }

    protected function getItems(iterable $items): array
    {
        return $this->mapItems('codigoInterno', $items);
    }

    protected function mapTaxes(iterable $taxes): array
    {
        return $this->map($taxes, function ($tax) {
            return [
                'codigo' => $tax->code,
                'codigoPorcentaje' => $tax->rate_code,
                'baseImponible' => $tax->base,
                'valor' => $tax->amount,
                'valorDevolucionIva' => $tax->return,
            ];
        });
    }

    protected function getName(): string
    {
        return 'notaCredito';
    }

    protected function getType(): string
    {
        return '04';
    }
}
