<?php

declare(strict_types=1);

namespace Veronica\Transport;

use ArrayObject;
use RuntimeException;
use Throwable;
use Veronica\Document\Contract  as Document;
use Veronica\Transport\Exception\RequestException;
use const Veronica\ENV_TEST;
use const Veronica\STATUS_AUTHORIZED;
use const Veronica\STATUS_NOT_AUTHORIZED;
use const Veronica\STATUS_REJECTED;

use function Veronica\arr_obj;

class Request
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

    public function send(Document $doc): bool
    {
        return $this->sendTo('sri', $doc);
    }

    protected function sendTo($path, Document $doc)
    {
        $res = $this->request(POST, $path, [
            'xml' => (string) $doc->pretty() . PHP_EOL . PHP_EOL
        ]);

        $method = 'responseTo' . ucfirst($path);

        return $this->$method($res, $doc);
    }

    protected function responseToSri(iterable $req, Document $doc): string
    {
        if (($key = $req['claveAccesoConsultada']) == $doc->key) {
            throw new RuntimeException('Las claves no coinciden: ' . $key . ' <> ' . $doc->key);
        }
        if ($req['numeroComprobantes'] != 1) {
            throw new RuntimeException('Numero de comprobantes invalidos: ' . $key);
        }
        $errors= [];
        $res = $req['autorizaciones'][0];
        if ($res['estado'] == STATUS_NOT_AUTHORIZED) {
            foreach ($res['mensajes'] as $mess) {
                $errors[] = $mess['tipo'] . ': ' .
                    $mess['identificador'] . ' ' .
                    $mess['mensaje'] . ' ' . $mess['informacionAdicional'];
            }
        }
        if ($errors) {
            throw new RuntimeException(implode(PHP_EOL, $errors));
        }

        return $res['estado'] == STATUS_AUTHORIZED ? STATUS_AUTHORIZED : STATUS_REJECTED;
    }

    protected function responseToComprobantes(iterable $res, Document $doc): string
    {
        return $res['claveAcceso'] == $doc->key ? STATUS_AUTHORIZED : STATUS_REJECTED;
    }

    public function delete(string $trackId)
    {
        return $this->request(DELETE, 'comprobantes/' . $trackId);
    }

    public function first(): iterable
    {
        return $this->all(['page' => 0, 'size' => 1])['content'][0] ?? [];
    }

    public function setToken(?iterable $token = null): iterable
    {
        if (!$token) {
            $this->requestTokenWithPassword();
        } else {
            $token = arr_obj($token);
            time() >= ($token['time'] + $token['expires_in']) ? $this->requestToken([
                'grant_type' => 'refresh_token',
                'refresh_token' => $token['refresh_token'],
            ]) : $this->useToken($token);
        }

        return $this->getToken();
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
