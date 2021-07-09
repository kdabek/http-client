<?php

declare(strict_types=1);

namespace Kdabek\HttpClient\Header;

use RuntimeException;
use Throwable;

class InvalidContentTypeException extends RuntimeException
{
    public function __construct(
        string $expectedContentType,
        string $actualContentType,
        $code = 0,
        Throwable $previous = null
    ) {
        $message = sprintf('Expects "%s" content type got "%s"', $expectedContentType, $actualContentType);

        parent::__construct($message, $code, $previous);
    }
}
