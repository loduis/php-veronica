<?php

namespace Veronica\Tests;

use Veronica\CreditNote;

class CreditNoteTest extends TestCase
{
    public function testExample1()
    {
        $doc = CreditNote::fromArray([
            'environment' => 1,
            'currency' => 'DOLAR',
            'date' => '21/10/2012',
            'prefix' => '001002', // es la serie
            'number' => '000000001',
            'net' => 295000,
            'discount' => 5005,
            'total' => 347159.4,
            'software_code' => 41261533,
            // 'withholding_agent' => false,
            'supplier' => [
                'name' => 'Distribuidora de Suministros Nacional S.A.',
                'tradename' => 'Empresa Importadora y Exportadora de Piezas',
                'identification' => [
                    'number' => '1792146739001'
                ],
                'address' => [
                    'main' => 'Enrique Guerrero Portilla OE1-34 AV. Galo Plaza Lasso',
                ],
            ],
            'customer' => [
                'name' => 'PRUEBAS SERVICIO DE RENTAS INTERNAS',
                'identification' => [
                    'type' => '04',
                    'number' => '1713328506001',
                ],
                'address' => [
                    'main' => 'salinas y santiago'
                ],
            ],
            'items' => [
                [
                    'code' => '125BJC-01',
                    'description' => 'CAMIONETA 4X4 DIESEL 3.7',
                    'qty' => 1,
                    'price' => 300000,
                    'net_price' => 295000,
                    'discount' => 5000,
                    'taxes' => [
                        [
                            'code' => 3,
                            'rate_code' => 3072,
                            'rate' => 5,
                            'base' => 295000,
                            'amount' => 14750,
                        ],
                        [
                            'code' => 2,
                            'rate_code' => 2,
                            'rate' => 12,
                            'base' => 309750,
                            'amount' => 37170,
                        ],
                        [
                            'code' => 5,
                            'rate_code' => 5001,
                            'rate' => 0.02,
                            'base' => 12000,
                            'amount' => 240,
                        ],
                    ],
                ],
            ],
            'taxes' => [
                [
                    'code' => 3, // ICE
                    'rate_code' => 3072,
                    'rate' => 5,
                    'base' => 295000,
                    'amount' => 14750,
                ],
                [
                    'code' => 2, // IVA
                    'rate_code' => 2,
                    'discount' => 5,
                    'base' => 309750,
                    'amount' => 37169.4,
                ],
                [
                    'code' => 5,
                    'rate_code' => 5001,
                    'base' => 12000,
                    'amount' => 240,
                ],
            ],
            'payments' => [
                [
                    'method' => '01',
                    'amount' => 347159.4,
                    'due_days' => 30,
                ],
            ],
        ]);
    }
}
