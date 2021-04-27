<?php

namespace Veronica\Tests;

use Veronica\Invoice;
use Veronica\Transport\Request;


class InvoiceTest extends TestCase
{
    public function _testFirst()
    {
        $username = $_ENV['API_USER'];
        $password = $_ENV['API_PASSWORD'];
        $clientId = $_ENV['API_CLIENT_ID'];
        $clientSecret = $_ENV['API_CLIENT_SECRET'];
        $invoice = Invoice::fromArray([
            'environment' => 1,
            'currency' => 'DOLAR',
            'date' => '23/04/2021',
            'prefix' => '001-002', // es la serie
            'number' => '000000021',
            'net' => '600.00',
            'discount' => '0.00',
            'total' => '600.00',
            'id' => 41530761, // pude ser el id interna de la transaccion
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
                'withholding_agent' => false,
            ],
            'customer' => [
                'name' => 'ioet Inc.',
                'email' => 'loduis@myabakus.com',
                'phone' => '31678969',
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
            'comments' => 'Esta es una factura de prueba.'
        ]);
        $request = new Request($username, $password, $clientId, $clientSecret);
        $token = json_decode('{
            "access_token": "f8aab05f-fd0b-400e-a32b-9f0e0bd6a010",
            "token_type": "bearer",
            "refresh_token": "403381b0-b05c-460c-b1a1-c78c0ccc3cf5",
            "expires_in": 16514,
            "scope": "read write",
            "time": 1619213507
        }', true);
        $token  = null;
        $request->setToken($token);
        $token = $request->getToken();
        echo json_encode($token, JSON_PRETTY_PRINT);
        $response = $request->send($invoice);
        print_r($response);
        // $data = $invoice->toArray();
        // echo json_encode($data, JSON_PRETTY_PRINT);
    }

    public function test2()
    {

        $invoice = Invoice::fromArray([
            'environment' => 1,
            'currency' => 'DOLAR',
            'date' => '21/04/2021',
            'prefix' => '001-001', // es la serie
            'number' => '000003571',
            'net' => '53150.00',
            'discount' => '0.00',
            'total' => '59528.00',
            'id' => '00003570',
            // 'withholding_agent' => false,
            'supplier' => [
                'name' => 'INMOBILIARIA CALDARIO SA',
                'tradename' => 'INMOBILIARIA CALDARIO SA',
                'identification' => [
                    'number' => '0503501215001'
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

        $username = $_ENV['API_USER'];
        $password = $_ENV['API_PASSWORD'];
        $clientId = $_ENV['API_CLIENT_ID'];
        $clientSecret = $_ENV['API_CLIENT_SECRET'];

        $request = new Request($username, $password, $clientId, $clientSecret);
        $token = json_decode('{
            "access_token": "f8aab05f-fd0b-400e-a32b-9f0e0bd6a010",
            "token_type": "bearer",
            "refresh_token": "403381b0-b05c-460c-b1a1-c78c0ccc3cf5",
            "expires_in": 16514,
            "scope": "read write",
            "time": 1619213507
        }', true);
        $token  = null;
        $request->setToken($token);
        $token = $request->getToken();
        echo json_encode($token, JSON_PRETTY_PRINT);
        $response = $request->send($invoice);
        print_r($response);
    }
}
