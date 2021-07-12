<?php

declare(strict_types=1);

namespace Kdabek\HttpClient\Auth\Credentials;

final class Token implements CredentialsInterface
{
    public function __construct(private string $token)
    {
    }

    public function getToken(): string
    {
        return $this->token;
    }
}
