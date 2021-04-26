<?php

namespace Veronica\Tests;

use Veronica\Invoice;
use Veronica\Transport\Request;


class InvoiceTest extends TestCase
{
    public function testFirst()
    {
        $username = $_ENV['API_USER'];
        $password = $_ENV['API_PASSWORD'];
        $clientId = $_ENV['API_CLIENT_ID'];
        $clientSecret = $_ENV['API_CLIENT_SECRET'];
        $invoice = Invoice::fromArray([
            'environment' => 1,
            'currency' => 'DOLAR',
            'date' => '23/03/2021',
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
                'acco'
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
        $request->setToken($token);
        // $token = $request->getToken();
        // echo json_encode($token, JSON_PRETTY_PRINT);
        $response = $request->send($invoice);
        print_r($response);
    }
}
