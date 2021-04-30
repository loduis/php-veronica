<?php

declare(strict_types=1);

namespace Veronica\Document;

use XML\Support\Element;
use XML\Document\Creator;
use Veronica\Document\Contact;
use const Veronica\TYPE_EMISSION;
use function Veronica\get_key;

abstract class Contract extends \XML\Document
{
    protected const VERSION = '1.1.0';

    protected $fillable = [
        'environment' => 'int',
        'date' => 'string',
        'prefix' => 'string',
        'number' => 'string',
        'net' => 'float',
        'total' => 'float',
        'id' => 'string',
        'currency' => 'string',
        'customer' => Contact::class,
        'supplier' => Contact::class,
        'location' => Element::class,
        'items' => 'array',
        'taxes' => 'array',
        'comments' => 'string',
    ];

    protected function getTaxInfo()
    {
        return [
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
            'regimenMicroempresas' => $this->supplier->regimeMicroenterprise,
            'agenteRetencion' => $this->withholdingAgent,
        ];
    }

    protected function getTaxes(iterable $taxes)
    {
        return [
            'totalImpuesto' => $this->mapTaxes($taxes)
        ];
    }

    protected function mapItems(string $codeName, iterable $items): array
    {
        return [
            'detalle' => $this->map($items, function ($item) use ($codeName) {
                return [
                    $codeName => $item->code,
                    'descripcion' => $item->description,
                    'unidadMedida' => $item->unit ?? null,
                    'cantidad' => $item->qty,
                    'precioUnitario' => $item->price,
                    'descuento' => $item->discount,
                    'precioTotalSinImpuesto' => $item->net,
                    'impuestos' => [
                        'impuesto' => $this->mapLineTaxes($item->taxes)
                    ],
                ];
            })
        ];
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

    protected function getRequiredAccounting()
    {
        return  $this->supplier->requiredAccounting ? 'SI' : 'NO';
    }

    protected function map(iterable $entries, \Closure $callback)
    {
        $data = [];
        foreach ($entries as $entry) {
            $entry = Element::fromArray($entry);
            $data[] = $callback($entry);
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
            $this->id, // 7
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

    abstract protected function mapTaxes(iterable $taxes): array;
}
