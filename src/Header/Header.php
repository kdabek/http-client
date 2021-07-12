<?php

declare(strict_types=1);

namespace Kdabek\HttpClient\Header;

use ArrayObject;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Header extends ArrayObject
{
    public const ACCEPT        = 'Accept';
    public const AUTHORIZATION = 'Authorization';
    public const CONTENT_TYPE  = 'Content-Type';

    public function bindTo(RequestInterface | ResponseInterface $message): RequestInterface | ResponseInterface
    {
        foreach ($this->getIterator() as $name => $values) {
            $message = $message->withAddedHeader($name, $values);
        }
        return $message;
    }

    public function toHeaderLines(): array
    {
        $headerLines = [];
        foreach ($this->getIterator() as $name => $values) {
            $headerLines[] = $name . ": " . implode(", ", $values);
        }

        return $headerLines;
    }
}
