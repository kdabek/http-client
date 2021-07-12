<?php

declare(strict_types=1);

namespace Kdabek\HttpClient\Auth\Factory;

use Kdabek\HttpClient\Auth\Credentials\CredentialsInterface;
use Kdabek\HttpClient\Auth\Strategy\AuthorizationStrategyInterface;

interface AuthorizationFactoryInterface
{
    public function createFrom(CredentialsInterface $credentials): AuthorizationStrategyInterface;
}
