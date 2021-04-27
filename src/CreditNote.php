<?php

declare(strict_types=1);

namespace Veronica;

class CreditNote extends Document\Contract
{

    public function toArray(): array
    {
        return [
            'infoTributaria' => [
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
            ],
            'infoNotaCredito' => [
                'fechaEmision' => $this->date,
                'dirEstablecimiento' => $this->supplier->address->location,
                // 'contribuyenteEspecial' => '',
                'obligadoContabilidad' => $this->required_accounting ? 'SI' : 'NO',
                'codDocModificado' => $this->reference->type,
                'numDocModificado' => $this->reference->id,
                'fechaEmisionDocSustento' => $this->reference->date,
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
                'propina' => $this->tip ?? '0.00',
                // <fleteInternacional>50.00</fleteInternacional>
                // <seguroInternacional>50.00</seguroInternacional>
                // <gastosAduaneros>50.00</gastosAduaneros>
                // <gastosTransporteOtros>50.00</gastosTransporteOtros>
                'valorModificacion' => $this->total, // preguntar
                'moneda' => $this->currency, // hay que preguntar si es un currency code
                // <placa>placa0</placa>
                'pagos' => [
                    'pago' => $this->payments,
                ],
                'valorRetIva' => $this->withholding->vat ?? '0.00',
                'valorRetRenta' => $this->withholding->renta ?? '0.00',
            ],
            'detalles' => [
                'detalle' => $this->items
            ],
            'retenciones' => $this->witholdings,
        ];
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