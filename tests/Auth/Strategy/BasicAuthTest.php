<?php

declare(strict_types=1);

namespace Kdabek\HttpClient\Auth\Strategy;

use PHPUnit\Framework\TestCase;

class BasicAuthTest extends TestCase
{
    private BasicAuth $strategy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->strategy = new BasicAuth('John', 'secret');
    }

    public function testCredentials()
    {
        $this->assertEquals(['Authorization' => 'Basic Sm9objpzZWNyZXQ='], $this->strategy->getCredentials());
    }
}
