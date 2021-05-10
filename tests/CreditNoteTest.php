<?php

namespace Veronica\Tests;

use Veronica\CreditNote;

class CreditNoteTest extends TestCase
{
    public function _testExample1()
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
            // 'id' => 41261533,
            'key' => '230420210410611019912011001003000002129117',
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
        $this->assertMatchesXmlSnapshot($doc->pretty());
    }

    public function testExample2()
    {
        $doc = CreditNote::fromArray([
            'environment' => 1,
            'currency' => 'DOLAR',
            'date' => '23/04/2021',
            'prefix' => '001-003',
            'number' => '000002129',
            'net' => 8.28,
            'total' => 8.28,
            'key' => '230420210410611019912011001003000002129117',
            //'id' => 1,
            'supplier' => [
                'name' => 'EMPRESA PRUEBA CIA LTDA',
                'tradename' => 'EMPRESA PRUEBA CIA LTDA',
                'identification' => [
                    'number' => '1061101991201'
                ],
                'address' => [
                    'main' => '13 de Abril e Ibarra - Huertos Familiares Azaya',
                ],
                'required_accounting' => true,
                'special_taxpayer' => '393'
            ],
            'customer' => [
                'name' => 'QUILUMBANGO PERUGACHI HERMINIA INES',
                'identification' => [
                    'type' => '05',
                    'number' => '1003590344',
                ],
                'address' => [
                    'main' => 'salinas y santiago'
                ],
                'email' => 'rolando.roc@gmail.com',
                'phone' => '042322000',
            ],
            'reference' => [
                'type' => '01',
                'date' => '20/04/2021',
                'id' => '001-001-000230142'
            ],
            'reason' => 'MAL SACADO',
            'items' => [
                [
                    'code' => '50470',
                    'description' => 'ALCOHOL ANTISEPT FARMANOVA SPRAY 250 ML',
                    'qty' => 6,
                    'price' => '1.38',
                    'net' => '8.28',
                    'discount' => '0.00',
                    'taxes' => [
                        [
                            'code' => 2, // IVA
                            'rate_code' => 0,
                            'base' => '8.28',
                            'rate' => 0,
                            'amount' => '0.00',
                        ],
                    ],
                ],
            ],
            'taxes' => [
                [
                    'code' => 2, // IVA
                    'rate_code' => 0,
                    'base' => '8.28',
                    'rate' => 0,
                    'amount' => '0.00',
                ],
            ],
        ]);
        $this->assertMatchesXmlSnapshot($doc->pretty());
    }
}
