<?php

declare(strict_types=1);

namespace Veronica\Transport\Exception;

use ArrayObject;

class RequestException extends \RuntimeException
{
    public const INVALID_TOKEN = 'invalid_token';

    public const NOT_FOUND = 'NOT_FOUND';

    public ArrayObject $response;

    public ?string $request;
}
