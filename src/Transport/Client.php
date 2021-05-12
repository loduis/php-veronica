<?php

declare(strict_types=1);

namespace Veronica\Transport;

use Veronica\Transport\Exception\RequestException;
use const Veronica\{
    ENV_PRO
};
use function Veronica\{
    arr_obj
};

const POST = 'POST';
const GET = 'GET';

class Client
{
    private string $baseApi = 'https://api-sbox.veronica.ec/api/v1.0/';

    private ?string $token;

    private $handle;

    private const DEFAULT_OPTIONS = [
        CURLOPT_HEADER => false,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => false
    ];

    public function __construct(int $environment)
    {
        if ($environment === ENV_PRO) {
            $this->baseApi = str_replace('-sbox', '', $this->baseApi);
        }
        $this->handle = curl_init();
    }

    public function __destruct()
    {
        if (is_resource($this->handle)) {
            curl_close($this->handle);
        }
    }

    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    public function getToken(string $path, iterable $params): iterable
    {
        $this->token = null;

        $token = $this->request(POST, $path, $params['user'], [
            CURLOPT_USERPWD =>  implode(':', $params['client']),
        ]);
        $this->token = $token['access_token'];

        return $token;
    }

    public function request(string $method, string $path, iterable $params = [], iterable $options = []): ?iterable
    {
        $options = static::DEFAULT_OPTIONS + $options;
        $headers = [
            'Accept: application/json',
        ];
        if ($method == POST) {
            if ($this->token) {
                if (($params['xml'] ?? false)) {
                    $headers[] =  'Content-Type: application/atom+xml';
                    $request = $params['xml'];
                } else {
                    $headers[] =  'Content-Type: application/json';
                    $request = json_encode($params);
                }
            } else {
                $request = http_build_query($params);
            }
            $headers[] = 'Content-length: ' . mb_strlen($request);
            $options[CURLOPT_POSTFIELDS] = $request;
        } elseif ($params) {
            $path .= '?' . http_build_query($params);
        }
        if ($this->token) {
            $headers[] =  'Authorization: Bearer ' . $this->token;
        }
        $options[CURLOPT_HTTPHEADER] = $headers;
        $options[CURLOPT_URL] = $this->baseApi . $path;
        curl_setopt_array($this->handle, $options);
        $response = curl_exec($this->handle);
        curl_reset($this->handle);
        if (($no = curl_errno($this->handle))) {
            $this->throwError($no, curl_error($this->handle), $request, [
                'message' => $response,
                'error' => 'request'
            ]);
        }
        $result = json_decode($response, true);
        if (($result['success'] ?? null) === false) {
            $result = $result['result'];
            $this->throwError(0, $result['message'], $request ?? null, [
                'error' => $result['status'],
                'message' => $result['message'],
                'errors' => $result['subErrors'] ?? []
            ]);
        }
        if (isset($result['error'])) {
            $error = $result['error_description'] ?? $result['message'];
            if ($path = ($result['path'] ?? false)) {
                $error .= ' ' . $path;
            }
            $this->throwError(
                0,
                html_entity_decode($error),
                $request ?? null,
                $result
            );
        }

        return arr_obj($result['result'] ?? $result);
    }

    private function throwError(int $code, string $message, ?string $request, iterable $response): void
    {
        $error = new RequestException($message, $code);
        $error->request = $request;
        $error->response = arr_obj($response);
        throw $error;
    }
}
