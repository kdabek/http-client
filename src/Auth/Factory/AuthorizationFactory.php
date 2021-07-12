<?php

declare(strict_types=1);

namespace Kdabek\HttpClient\Auth\Factory;

use Kdabek\HttpClient\Auth\Credentials\CredentialsInterface;
use Kdabek\HttpClient\Auth\Credentials\LoginAndPassword;
use Kdabek\HttpClient\Auth\Credentials\Token;
use Kdabek\HttpClient\Auth\Strategy\AuthorizationStrategyInterface;
use Kdabek\HttpClient\Auth\Strategy\BasicAuth;
use Kdabek\HttpClient\Auth\Strategy\TokenAuth;
use Kdabek\HttpClient\Auth\Strategy\UnsupportedStrategyException;

class AuthorizationFactory implements AuthorizationFactoryInterface
{
    /**
     * @param CredentialsInterface $credentials
     * @return AuthorizationStrategyInterface
     * @throws UnsupportedStrategyException
     */
    public function createFrom(CredentialsInterface $credentials): AuthorizationStrategyInterface
    {
        return match (true) {
            $credentials instanceof LoginAndPassword => new BasicAuth(
                $credentials->getLogin(),
                $credentials->getPassword()
            ),
            $credentials instanceof Token => new TokenAuth($credentials->getToken()),
            default => throw new UnsupportedStrategyException(),
        };
    }
}
