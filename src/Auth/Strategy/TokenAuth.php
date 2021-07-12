<?php

declare(strict_types=1);

namespace Kdabek\HttpClient\Auth\Strategy;

use Kdabek\HttpClient\Header\Header;

class TokenAuth implements AuthorizationStrategyInterface
{
    public function __construct(private string $token)
    {
    }

    public function getCredentials(): array
    {
        return [
            Header::AUTHORIZATION => 'Bearer ' . $this->token
        ];
    }
}
