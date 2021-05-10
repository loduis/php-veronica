<?php

namespace Veronica\Tests;

use Veronica\Invoice;

class InvoiceTest extends TestCase
{

    public function testExample1()
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
            // 'id' => 41530761,
            'key' => '2303202101050350121500110010020000000204153076118',
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
                    'net' => '600.00',
                    'discount' => '0.00',
                    'taxes' => [
                        [
                            'code' => 2, // IVA
                            'rate_code' => 0,
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
    }

    public function testExample2()
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
            // 'id' => '000003570',
            'key' => '21042021011790645231001100100100000357000000357012',
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
                    'net' => '53150.00',
                    'discount' => '0.00',
                    'taxes' => [
                        [
                            'code' => 2, // IVA
                            'rate_code' => 2,
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
    }
}
