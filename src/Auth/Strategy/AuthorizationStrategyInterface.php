<?php

declare(strict_types=1);

namespace Kdabek\HttpClient\Auth\Strategy;

interface AuthorizationStrategyInterface
{
    public function getCredentials(): array;
}
