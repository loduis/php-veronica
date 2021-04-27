<?php

namespace Veronica\Tests;

use Veronica\Document;
use Veronica\Invoice;

class DocumentTest extends TestCase
{
    public function _testExample1()
    {
        $doc = Document::fromArray([
            'type' => '01',
            'environment' => 1,
            'currency' => 'DOLAR',
            'date' => '21/10/2012',
            'prefix' => '001001', // es la serie
            'number' => '000000001',
            'reference' => '001-001-000000001',
            'net' => 295000,
            'discount' => 5005,
            'total' => 347159.4,
            'id' => 41261533,
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
        $doc = Invoice::fromArray([
            'environment' => 1,
            'currency' => 'DOLAR',
            'date' => '23/03/2021',
            'prefix' => '001-002', // es la serie
            'number' => '000000020',
            'net' => '600.00',
            'discount' => '0.00',
            'total' => '600.00',
            'id' => 41530761,
            // 'withholding_agent' => false,
            'supplier' => [
                'name' => 'Francisco Israel Teneda Gallardo',
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
                'email' => 'loduis@myabakus.com',
                'phone' => '31678969',
            ],
            'items' => [
                [
                    'code' => '831429900',
                    'description' => 'Otros Servicios de Diseño y Desarrollo de la Tecnología de la Información (IT) Para Redes y Sistemas, N.C.P. (831429900)',
                    'qty' => 1,
                    'price' => '600.00',
                    'net_price' => '600.00',
                    'discount' => '0.00',
                    'taxes' => [
                        [
                            'code' => 2, // IVA
                            'rate_code' => 0,
                            'discount' => '0.00',
                            'base' => '600.00',
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
                    'discount' => '0.00',
                    'base' => '600.00',
                    'rate' => 0,
                    'amount' => '0.00',
                ],
            ],
            'payments' => [
                [
                    'method' => '20',
                    'amount' => '600.00',
                    'due_days' => 0,
                ],
            ],
            'comments' => 'Esto es una factura de prueba.'
        ]);
        $this->assertMatchesXmlSnapshot($doc->pretty());
        // $this->assertMatchesXmlSnapshot($doc->toJson());
    }

    public function testExample3()
    {

        $doc = Invoice::fromArray([
            'environment' => 1,
            'currency' => 'DOLAR',
            'date' => '21/04/2021',
            'prefix' => '001-001', // es la serie
            'number' => '000003570',
            'net' => '53150.00',
            'discount' => '0.00',
            'total' => '59528.00',
            'id' => '000003570',
            // 'withholding_agent' => false,
            'supplier' => [
                'name' => 'INMOBILIARIA CALDARIO SA',
                'tradename' => 'INMOBILIARIA CALDARIO SA',
                'identification' => [
                    'number' => '1790645231001'
                ],
                'address' => [
                    'main' => 'AMAZONAS Y PASAJE GUAYAS E3-131 EDF. RUMINAHUI PISO 8',
                ],
                'required_accounting' => true,
                'special_taxpayer' => '000'
            ],
            'customer' => [
                'name' => 'INMOBILIARIA MOTKE S.A.',
                'identification' => [
                    'type' => '04',
                    'number' => '0990995184001',
                ],
                'address' => [
                    'main' => 'AV. 9 DE OCTUBRE 729 Y  BOYACA'
                ],
                'email' => 'rolando.roc@gmail.com',
                'phone' => '042322000',
            ],
            'items' => [
                [
                    'code' => 'HONMOTKE',
                    'description' => 'HONORARIOS POR ADMINISTRACION, DIRECCION Y RESPONSABILIDAD TECNICA CUOTA 11 DEL 01 AL 28 MARZO DE 2021 PROYECTO RIOCENTRO QUITO',
                    'qty' => 1,
                    'price' => '53150.00',
                    'net_price' => '53150.00',
                    'discount' => '0.00',
                    'taxes' => [
                        [
                            'code' => 2, // IVA
                            'rate_code' => 2,
                            'discount' => '0.00',
                            'base' => '53150.00',
                            'rate' => 12,
                            'amount' => '6378.00',
                        ],
                    ],
                ],
            ],
            'taxes' => [
                [
                    'code' => 2, // IVA
                    'rate_code' => 2,
                    'discount' => '0.00',
                    'base' => '53150.00',
                    'rate' => 12,
                    'amount' => '6378.00',
                ],
            ],
            'payments' => [
                [
                    'method' => '20',
                    'amount' => '59528.00',
                ],
            ],
            'comments' => 'HONORARIOS POR ADMINISTRACION, DIRECCION Y RESPONSABILIDAD TECNICA CUOTA 11 DEL 01 AL 28 DE MARZO DE 2021 PROYECTO RIOCENTRO QUITO DIRECCION: AV. 6 DE DICIEMBRE N21-245 TOMAS DE BERLANGA, CALLE PINZON'
        ]);
        $this->assertMatchesXmlSnapshot($doc->pretty());
        // $this->assertMatchesXmlSnapshot($doc->toJson());
    }
}
