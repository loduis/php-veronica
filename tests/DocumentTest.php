<?php

namespace Veronica\Tests;

use Veronica\Document;

class DocumentTest extends TestCase
{
    public function testExample1()
    {
        $doc = Document::fromArray([
            'type' => '01',
            'environment' => 1,
            'currency' => 'DOLLAR',
            'date' => '21/10/2012',
            'prefix' => '001001', // es la serie
            'number' => '000000001',
            'reference' => '001-001-000000001',
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
        $this->assertMatchesJsonSnapshot($doc->toJson());
    }

    public function testExample2()
    {
        $doc = Document::fromArray([
            'type' => '01',
            'environment' => 1,
            'currency' => 'DOLLAR',
            'date' => '23/03/2021',
            'prefix' => '001002', // es la serie
            'number' => '000000020',
            'net' => 600,
            'discount' => 0,
            'total' => 600,
            'software_code' => 41530761,
            // 'withholding_agent' => false,
            'supplier' => [
                'name' => 'Distribuidora de Suministros Nacional S.A.',
                'tradename' => 'israteneda',
                'identification' => [
                    'number' => '0503501215001'
                ],
                'address' => [
                    'main' => 'Rio Tigre y Rio Ana Tenorio, Salcedo, Cotopaxi',
                ],
            ],
            'customer' => [
                'name' => 'ioet Inc.',
                'identification' => [
                    'type' => '08',
                    'number' => '47-10803393',
                ],
                'address' => [
                    'main' => '1491 Cypress Drive. Suite #853. Pebble Beach, California 93953'
                ],
            ],
            'items' => [
                [
                    'code' => '831429900',
                    'description' => 'Otros Servicios de Diseño y Desarrollo de la Tecnología de la Información (IT) Para Redes y Sistemas, N.C.P. (831429900)',
                    'qty' => 1,
                    'price' => 600,
                    'net_price' => 600,
                    'discount' => 0,
                    'taxes' => [
                        [
                            'code' => 2, // IVA
                            'rate_code' => 0,
                            'discount' => 0,
                            'base' => 0,
                            'rate' => 0,
                            'amount' => 0,
                        ],
                    ],
                ],
            ],
            'taxes' => [
                [
                    'code' => 2, // IVA
                    'rate_code' => 0,
                    'discount' => 0,
                    'base' => 0,
                    'rate' => 0,
                    'amount' => 0,
                ],
            ],
            'payments' => [
                [
                    'method' => '20',
                    'amount' => 600,
                    'due_days' => 0,
                ],
            ],
        ]);
        $this->assertMatchesJsonSnapshot($doc->toJson());
    }
}
