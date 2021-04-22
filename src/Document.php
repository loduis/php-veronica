<?php

declare(strict_types=1);

namespace Veronica;

use ArrayObject;
use XML\Support\Element;
use XML\Document\Creator;
use Veronica\Document\Contact;

class Document extends \XML\Document
{
    protected const VERSION = '1.1.0';

    protected $fillable = [
        'environment' => 'int',
        'type' => 'string',
        'date' => 'string',
        'prefix' => 'string',
        'number' => 'string',
        'net' => 'float',
        'discount' => 'float',
        'tip' => 'float',
        'total' => 'float',
        'reference' => 'string',
        'withholding_agent' => 'string',
        'required_accounting' => 'bool',
        'softwareCode' => 'string',
        'currency' => 'string',
        'customer' => Contact::class,
        'supplier' => Contact::class,
        'location' => Element::class,
        'items' => 'array',
        'taxes' => 'array',
        'payments' => 'array',
        'withholding' => Element::class,
        'withholdings' => 'array',
    ];

    public function toArray()
    {
        return $this->filter([
            'infoTributaria' => $this->filter([
                'ambiente' => $this->environment,
                'tipoEmision' => TYPE_EMISSION, // Normal es una constante por ahora
                'razonSocial' => $this->supplier->name,
                'nombreComercial' => $this->supplier->tradename,
                'ruc' => $this->supplier->identification->number,
                'claveAcceso' => $this->key,
                'codDoc' => $this->type,
                'estab' => $this->location->main,
                'ptoEmi' => $this->location->issue,
                'secuencial' => $this->number,
                'dirMatriz' => $this->supplier->address->main,
                // 'regimenMicroempresas' => ''
                'agenteRetencion' => $this->withholding_agent,
            ]),
            'infoFactura' => $this->filter([
                'fechaEmision' => $this->date,
                'dirEstablecimiento' => $this->supplier->address->location,
                // 'contribuyenteEspecial' => '',
                'obligadoContabilidad' => $this->required_accounting ? 'SI' : 'NO',
                // <comercioExterior>EXPORTADOR</comercioExterior>
                // <incoTermFactura>A</incoTermFactura>
                // <lugarIncoTerm>lugarIncoTerm0</lugarIncoTerm>
                // <paisOrigen>000</paisOrigen>
                // <puertoEmbarque>puertoEmbarque0</puertoEmbarque>
                // <puertoDestino>puertoDestino0</puertoDestino>
                // <paisDestino>000</paisDestino>
                // <paisAdquisicion>000</paisAdquisicion>
                'tipoIdentificacionComprador' => $this->customer->identification->type,
                'guiaRemision' => $this->reference,
                'razonSocialComprador' => $this->customer->name,
                'identificacionComprador' => $this->customer->identification->number,
                'direccionComprador' => $this->customer->address->main,
                'totalSinImpuestos' => $this->net,
                // <totalSubsidio>50.00</totalSubsidio>
                // <incoTermTotalSinImpuestos>A</incoTermTotalSinImpuestos>
                'totalDescuento' => $this->discount,
                // <codDocReembolso>00</codDocReembolso>
                // <totalComprobantesReembolso>50.00</totalComprobantesReembolso>
                // <totalBaseImponibleReembolso>50.00</totalBaseImponibleReembolso>
                // <totalImpuestoReembolso>50.00</totalImpuestoReembolso>
                'totalConImpuestos' => [
                    'totalImpuesto' => $this->taxes,
                ],
                // <compensaciones></compensaciones>
                'propina' => $this->tip ?? 0,
                // <fleteInternacional>50.00</fleteInternacional>
                // <seguroInternacional>50.00</seguroInternacional>
                // <gastosAduaneros>50.00</gastosAduaneros>
                // <gastosTransporteOtros>50.00</gastosTransporteOtros>
                'importeTotal' => $this->total, // preguntar
                'moneda' => $this->currency, // hay que preguntar si es un currency code
                // <placa>placa0</placa>
                'pagos' => [
                    'pago' => $this->payments,
                ],
                'valorRetIva' => $this->withholding->vat ?? 0,
                'valorRetRenta' => $this->withholding->renta ?? 0,
            ]),
            'detalles' => [
                'detalle' => $this->items
            ],
            'retenciones' => $this->witholdings,
        ]);
    }

    protected function  getWitholdings(?iteable $taxes)
    {
        return $this->prepareTaxes($taxes);
    }

    protected function getTaxes(iterable $taxes)
    {
        return $this->prepareTaxes($taxes);
    }

    protected function prepareTaxes(?iterable $taxes): iterable
    {
        $data = [];
        foreach ($taxes ?? [] as $tax) {
            $tax = new Element($tax);
            $data[] = $this->filter([
                'codigo' => $tax->code,
                'codigoPorcentaje' => $tax->rate_code,
                'descuentoAdicional' => $tax->discount,
                'baseImponible' => $tax->base,
                'tarifa' => $tax->rate,
                'valor' => $tax->amount,
                'valorDevolucionIva' => $tax->return,
            ]);
        }

        return $data;
    }

    protected function getItems(iterable $items): array
    {
        $data = [];
        foreach ($items as $item) {
            $item = new Element($item);
            $data[] = $this->filter([
                'codigoPrincipal' => $item->code,
                'descripcion' => $item->description,
                'unidadMedida' => $item->unit ?? null,
                'cantidad' => $item->qty,
                'precioUnitario' => $item->price,
                'descuento' => $item->discount,
                'precioTotalSinImpuesto' => $item->net_price,
                'impuestos' => [
                    'impuesto' => $this->prepareTaxes($item->taxes)
                ],
            ]);
        }

        return $data;
    }

    protected function getPayments(?iterable $payments): array
    {
        $data = [];
        foreach ($payments ?? [] as $payment) {
            $payment = new Element($payment);
            $data[] = [
                'formaPago' => $payment->method,
                'total' => $payment->amount,
                'plazo' => $payment->due_days,
                'unidadTiempo' => $payment->due_days !== null ? 'dias' : null,
            ];
        }

        return $data;
    }

    protected function getKey(): string
    {
        $key = implode('', [
            $this->dateNumber, // 1
            $this->type, // 2
            $this->supplier->identification->number, // 3
            $this->environment, // 4
            $this->prefix, // 5
            $this->number, // 6
            $this->software_code, // 7
            TYPE_EMISSION, //
        ]);

        $sum = 0;
        foreach (str_split(strrev($key)) as $i => $val) {
            if ($i % 6 === 0) {
                $factor = 2;
            }
            $sum += $val * $factor;
            $factor ++;
        }
        $result = 11 - ($sum % 11);
        if ($result > 9) {
            $result = 11 - $result;
        }

        return $key . $result;
    }

    protected function filter(iterable $entries): array
    {
        $result = [];
        foreach ($entries as $key => $value) {
            if ($value !== null  && (is_scalar($value) || (is_array($value) && count($value) > 0))) {
                $result[$key] = $value;
            }
        }

        return $result;
    }
    protected function getName(): string
    {
        return 'factura';
    }

    protected function creator(): Creator
    {
        return new Creator($this, [
            'id' => 'comprobante',
            'version' => static::VERSION
        ]);
    }

    protected function setPrefix(string $value): string
    {
        if (strpos($value, '-') === false) {
            throw new \InvalidArgumentException('No separator - is present in prefix: ' . $value);
        }
        [$main, $issue] = explode('-', $value);
        $this->location = [
            'main' => $main,
            'issue' => $issue,
        ];

        return $main . $issue;
    }

    protected function getDateNumber()
    {
        return str_replace('/', '', $this->date);
    }
}
