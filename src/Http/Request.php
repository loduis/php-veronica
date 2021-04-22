<?php

declare(strict_types=1);

namespace Veronica\Http;

use ArrayObject;
use Throwable;
use Veronica\Http\Exception\RequestException;
use const Veronica\ENV_TEST;
use function Veronica\arr_obj;

abstract class Request
{
    private Client $client;

    private ?ArrayObject $token;

    private string $username;

    private string $password;

    private string $clientId;

    private string $clientSecret;

    protected int $environment;

    public function __construct(string $username, string $password, string $clientId, string $clientSecret, int $environment = ENV_TEST)
    {
        $this->client = new Client($environment);
        $this->username = $username;
        $this->password = $password;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->environment = $environment;
    }

    public function find(string $trackId): ?iterable
    {
        try {
            $res = $this->request(GET, 'comprobantes/' . $trackId);
        } catch (RequestException $e) {
            if ($e->response->error == RequestException::NOT_FOUND) {
                return null;
            }
            throw $e;
        }

        return arr_obj([
            'type' => $res['tipoDocumento'],
            'status' => $res['estatusInterno'],
            'date' => [
                'issue' => $this->toISO($res['fechaEmision']),
                'authorization' => $this->toISO($res['fechaAutorizacion']),
                'cancel' => $this->toISO($res['fechaAnulacion'])
            ],
            'message' => $res['mensajesSri']
        ]);
    }

    public function first(): iterable
    {
        return $this->all(['page' => 0, 'size' => 1])['content'][0] ?? [];
    }

    public function setToken(?iterable $token = null): void
    {
        if (!$token) {
            $this->requestTokenWithPassword();
            return;
        }
        $token = arr_obj($token);
        time() >= ($token['time'] + $token['expires_in']) ? $this->requestToken([
            'grant_type' => 'refresh_token',
            'refresh_token' => $token['refresh_token'],
        ]) : $this->useToken($token);
    }

    public function getToken(): iterable
    {
        return $this->token->getArrayCopy();
    }

    public function all(iterable $params = []): iterable
    {
        return $this->request(GET, $this->path, $params);
    }

    protected function toISO(string $date): ?string
    {
        if ($date) {
            [$day, $month, $year] = explode('/', $date);
            return "$year-$month-$day";
        }

        return null;
    }

    protected function request(string $method, string $path, iterable $params = []): iterable
    {
        try {
            return $this->client->request($method, $path, $params);
        } catch (RequestException $e) {
            if ($e->response->error == RequestException::INVALID_TOKEN) {
                $this->requestTokenWithPassword();
                return $this->client->request($method, $path, $params);
            }
            throw $e;
        } catch (Throwable $e) {
            throw $e;
        }
    }

    protected function requestToken(iterable $params)
    {
        $time = time();
        $token = $this->client->getToken('oauth/token', [
            'user' => $params,
            'client' => [
                $this->clientId,
                $this->clientSecret
            ]
        ]);
        $token['time'] = $time;

        $this->useToken($token);
    }

    protected function requestTokenWithPassword(): void
    {
        $this->requestToken([
            'username' => $this->username,
            'password' => $this->password,
            'grant_type' => 'password',
        ]);
    }

    protected function useToken(iterable $token): void
    {
        $this->client->setToken($token['access_token']);
        $this->token = $token;
    }
}