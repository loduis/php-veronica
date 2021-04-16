<?php

namespace Veronica;

use Illuminate\Support\Arr;
use Illuminate\Api\Http\Api as HttpApi;

class Api
{
    /**
     * The current version of package
     *
     * @var int
     */
    private const VERSION  = 'v1.0';

    /**
     * Custom options of http client
     *
     * @var array
     */
    private static $clientOptions = [];

    /**
     * The base path of api
     *
     * @var string
     */
    private const BASE_URI = 'https://api-sbox.veronica.ec/api/';

    public static function auth($params)
    {
        HttpApi::baseUri(static::BASE_URI . static::VERSION . '/');
        $options = static::$clientOptions;
        $options['headers']['User-Agent'] = 'Php Veronica/' . static::VERSION;
        $response = null;
        if (is_array($params) && Arr::isAssoc($params)) {
            $client = HttpApi::createClient(array_merge($options, [
                'auth' => [$params['client_id'], $params['client_secret']],
                'form_params' => [
                    'username' => $params['username'],
                    'password' => $params['password'],
                    'grant_type' => 'password'
                ]
            ]));
            $response = $client::toArray('post', 'oauth/token');
            $params = $response['access_token'];
        }
        $options['headers']['Authorization'] = 'Bearer ' . $params;
        print_r($options);

        HttpApi::createClient($options);

        return $response;
    }

    /**
     * Set the custom options for create http client
     *
     * @param  array  $options
     * @return void
     */
    public static function clientOptions(array $options)
    {
        static::$clientOptions = $options;
    }
}
