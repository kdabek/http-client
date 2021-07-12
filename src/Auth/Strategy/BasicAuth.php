<?php

declare(strict_types=1);

namespace Kdabek\HttpClient\Auth\Strategy;

use Kdabek\HttpClient\Header\Header;

class BasicAuth implements AuthorizationStrategyInterface
{
    public function __construct(private string $login, private string $password)
    {
    }

    public function getCredentials(): array
    {
        return [
            Header::AUTHORIZATION => 'Basic ' . base64_encode($this->login . ':' . $this->password)
        ];
    }
}
