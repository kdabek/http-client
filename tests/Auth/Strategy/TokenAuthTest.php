<?php

declare(strict_types=1);

namespace Kdabek\HttpClient\Auth\Strategy;

use PHPUnit\Framework\TestCase;

class TokenAuthTest extends TestCase
{
    private TokenAuth $strategy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->strategy = new TokenAuth('secret');
    }

    public function testCredentials()
    {
        $this->assertEquals(['Authorization' => 'Bearer secret'], $this->strategy->getCredentials());
    }
}
