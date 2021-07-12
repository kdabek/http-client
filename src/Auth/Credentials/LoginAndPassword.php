<?php

declare(strict_types=1);

namespace Kdabek\HttpClient\Auth\Credentials;

final class LoginAndPassword implements CredentialsInterface
{
    public function __construct(private string $login, private string $password)
    {
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
