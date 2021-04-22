<?php

declare(strict_types=1);

namespace Veronica;

class Document
{
    private int $environment;

    private string $type;

    private iterable $customer;

    private iterable $supplier;

    private ?iterable $withholdings = null;

    private iterable $items;

    private iterable $payments;

    private iterable $taxes;

    private string $date;

    private float $discount;

    private int $software_code;

    private string $currency;

    private string $prefix;

    private string $number;

    private float $net;

    private float $total;

    private ?string $reference = null;

    private ?string $withholding_agent = null;

    private float $tip = 0;

    private bool $required_accounting = false;

    public string $key;

    protected function __construct(iterable $data)
    {
        foreach ($data as $key => $value) {
            if (!property_exists($this, $key)) {
                throw new \OutOfBoundsException('Not found property: ' . $key);
            }
            if (is_iterable($value)) {
                $this->$key = arr_obj($value);
            } else {
                $this->$key = $value;
            }
        }
        if (!isset($this->supplier->address->location)) {
            $this->supplier->address->location = $this->supplier->address->main;
        }
        $this->key = $this->calculateKey();
    }

    public static function fromArray(iterable $data)
    {
        return new static($data);
    }

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
                // 'estab' => '',
                // 'ptoEmi' => '',
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
                'totalConImpuestos' => $this->prepareTaxes($this->taxes),
                // <compensaciones></compensaciones>
                'propina' => $this->tip,
                // <fleteInternacional>50.00</fleteInternacional>
                // <seguroInternacional>50.00</seguroInternacional>
                // <gastosAduaneros>50.00</gastosAduaneros>
                // <gastosTransporteOtros>50.00</gastosTransporteOtros>
                'importeTotal' => $this->total, // preguntar
                'moneda' => $this->currency, // hay que preguntar si es un currency code
                // <placa>placa0</placa>
                'pagos' => $this->preparePayments(),
                // <valorRetIva>50.00</valorRetIva>
                // <valorRetRenta>50.00</valorRetRenta>
            ]),
            'detalles' => $this->prepareItems(),
            'retenciones' => $this->prepareTaxes($this->withholdings),
        ]);
    }

    public function toJson()
    {
        return json_encode(
            $this->toArray(),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES |
            JSON_UNESCAPED_UNICODE
        );
    }

    protected function prepareTaxes(?iterable $taxes): iterable
    {
        $data = [];
        foreach ($taxes ?? [] as $tax) {
            $tax = arr_obj($tax);
            $data[] = $this->filter([
                'codigo' => $tax->code,
                'codigoPorcentaje' => $tax->rate_code,
                'descuentoAdicional' => $tax->discount ?? null,
                'baseImponible' => $tax->base,
                'tarifa' => $tax->rate ?? null,
                'valor' => $tax->amount,
                'valorDevolucionIva' => $tax->return ?? null,
            ]);
        }

        return $data;
    }

    protected function prepareItems()
    {
        $data = [];
        foreach ($this->items as $item) {
            $item = arr_obj($item);
            $data[] = $this->filter([
                'codigoPrincipal' => $item->code,
                'descripcion' => $item->description,
                'unidadMedida' => $item->unit ?? null,
                'cantidad' => $item->qty,
                'precioUnitario' => $item->price,
                'descuento' => $item->discount,
                'precioTotalSinImpuesto' => $item->net_price,
                'impuestos' => $this->prepareTaxes($item->taxes ?? []),
            ]);
        }

        return $data;
    }

    protected function preparePayments()
    {
        $data = [];
        foreach ($this->payments ?? [] as $payment) {
            $payment = arr_obj($payment);
            $data[] = $this->filter([
                'formaPago' => $payment->method,
                'total' => $payment->amount,
                'plazo' => $payment->due_days,
                'unidadTiempo' => $payment->due_days !== null ? 'Dias' : null,
            ]);
        }

        return $data;
    }

    private function calculateKey()
    {
        $key = implode('', [
            str_replace('/', '', $this->date), // 1
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

    protected function filter(iterable $entries)
    {
        $result = [];
        foreach ($entries as $key => $value) {
            if ($value !== null  && (is_scalar($value) || (is_array($value) && count($value) > 0))) {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
