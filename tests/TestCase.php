<?php

namespace Veronica\Tests;

use Veronica\Api;
use Illuminate\Api\Testing\TestCase as ApiTestCase;

/**
 * Base class for Alegra test cases, provides some utility methods for creating
 * objects.
 */
abstract class TestCase extends ApiTestCase
{
    protected function setUp(): void
    {
        /*
        $params = $_ENV['API_TOKEN'] ?? [
            'username' => $_ENV['API_USER'],
            'password' => $_ENV['API_PASSWORD'],
            'client_id' => $_ENV['API_CLIENT_ID'],
            'client_secret' => $_ENV['API_CLIENT_SECRET']
        ];
        $response = Api::auth($params);
        print_r($response);
        */
    }
}

