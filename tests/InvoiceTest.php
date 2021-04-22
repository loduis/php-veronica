<?php

namespace Veronica\Tests;

use Veronica\Invoice;
// use GuzzleHttp\Client;

const BASE_PATH = 'https://api-sbox.veronica.ec/api/v1.0/';

class InvoiceTest extends TestCase
{
    public function testResolvePath()
    {
        // $this->assertEquals('comprobantes/facturas', Invoice::resolvePath());
    }

    public function testFirst()
    {
        // $data = Invoice::first();
        // print_r($data);
        //
        /*
        $client = new Client([
            'base_uri' => 'https://api-sbox.veronica.ec/api/v1.0/',
            'heeders' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $_ENV['API_TOKEN']
            ]
        ]);
        $res  = $client->request('GET', 'comprobantes/facturas', [
            'query' => [
                'page' => 0,
                'size' => 1
            ]
        ]);
        echo (string) $res->getBody();
         */
        // $url = BASE_PATH . 'comprobantes/facturas?page=0&size=1';

        // $token = 'ce276f96-23ce-4d40-97c9-521a796dc643';
        /*
        $token = 'f77419c4-529e-42db-8e06-6a6276b6e604';
        $username = $_ENV['API_USER'];
        $password = $_ENV['API_PASSWORD'];
        $clientId = $_ENV['API_CLIENT_ID'];
        $clientSecret = $_ENV['API_CLIENT_SECRET'];

        $client =  new HttpClient(
            $token,
            $username,
            $password,
            $clientId,
            $clientSecret
        );
        $res = $client->get('comprobantes/facturas', [
            'page' => 0,
            'size' => 1,
        ]);
        print_r($res);
         */
        $username = $_ENV['API_USER'];
        $password = $_ENV['API_PASSWORD'];
        $clientId = $_ENV['API_CLIENT_ID'];
        $clientSecret = $_ENV['API_CLIENT_SECRET'];
        $invoice = new Invoice($username, $password, $clientId, $clientSecret);
        $token = json_decode('{"access_token":"c67f9b11-3ada-4a54-b8e7-65c57f049771","token_type":"bearer","refresh_token":"403381b0-b05c-460c-b1a1-c78c0ccc3cf5","expires_in":43199,"scope":"read write","time":1618584256}', true);
        $invoice->setToken($token);
        // echo json_encode($invoice->getToken()), PHP_EOL;
        $data = $invoice->find('2303202101050350121500110010020000000204153076117');
        // $data = $invoice->first();
        print_r($data);

        /*
        $token = 'ce276f96-23ce-4d40-97c9-521a796dc643';

        $url = BASE_PATH . 'oauth/token';
        $handle = curl_init($url);
        $username = $_ENV['API_USER'];
        $password = $_ENV['API_PASSWORD'];
        $clientId = $_ENV['API_CLIENT_ID'];
        $clientSecret = $_ENV['API_CLIENT_SECRET'];

        curl_setopt($handle, CURLOPT_HEADER, false);
        curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_USERPWD, $clientId . ':' . $clientSecret);
        $headers = [
            'Accept: application/json',
            // 'Content-Type: application/json',
        ];
        // $headers[] = 'Authorization: Bearer ' . $_ENV['API_TOKEN'];
        $request = [
            'username' => $username,
            'password' => $password,
            'grant_type' => 'password',
        ];

        // $request = json_encode($request);
        $request = http_build_query($request);
        echo $request;
        $headers[] = 'Content-length: ' . mb_strlen($request);
        curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $request);
        // curl_seto
        $response = curl_exec($handle);

        echo $response;
        */
    }
}

class HttpClient
{
    const BASE_API = 'https://api-sbox.veronica.ec/api/v1.0/';

    private ?string $token;

    private string $username;

    private string $password;

    private string $clientId;

    private string $clientSecret;

    private $curl;

    public function __construct(?string $token, string $username, string $password, string $clientId, string $clientSecret)
    {
        $this->token = $token;
        $this->username = $username;
        $this->password = $password;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_HEADER, false);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
    }

    public function auth()
    {
        curl_setopt($this->curl, CURLOPT_USERPWD, $this->clientId . ':' . $this->clientSecret);

        return $this->post('oauth/token', [
            'username' => $this->username,
            'password' => $this->password,
            'grant_type' => 'password',
        ]);
    }

    public function post(string $path, iterable $params = [])
    {
        return $this->request('POST', $path, $params);
    }

    public function get(string $path, iterable $params = [])
    {
        return $this->request('GET', $path, $params);
    }

    private function request(string $method, string $path, iterable $params = [])
    {
        $headers = [
            'Accept: application/json',
        ];
        if ($method === 'POST') {
            if ($this->token) {
                $headers[] =  'Content-Type: application/json';
                $request = json_encode($params);
            } else {
                $request = http_build_query($params);
            }
            $headers[] = 'Content-length: ' . mb_strlen($request);
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $request);
        } elseif ($params) {
            $path .= '?' . http_build_query($params);
        }
        if ($this->token) {
            $headers[] =  'Authorization: Bearer ' . $this->token;
        }
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($this->curl, CURLOPT_URL, static::BASE_API . $path);
        $response = curl_exec($this->curl);
        if (($no = curl_errno($this->curl))) {
            $error = new \RuntimeException(curl_error($this->curl), $no);
            $error->request = $request;
            throw $error;
        }
        return json_decode($response, true);
    }

}
