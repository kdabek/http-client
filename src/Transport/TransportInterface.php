<?php

declare(strict_types=1);

namespace Kdabek\HttpClient\Transport;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface TransportInterface
{
    public function request(RequestInterface $request): ResponseInterface;
}
