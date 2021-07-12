<?php

declare(strict_types=1);

namespace Kdabek\HttpClient\Auth\Credentials;

use PHPUnit\Framework\TestCase;

class TokenTest extends TestCase
{
    private Token $credentials;

    protected function setUp(): void
    {
        parent::setUp();

        $this->credentials = new Token('secret');
    }

    public function testGetters()
    {
        $this->assertEquals('secret', $this->credentials->getToken());
    }
}
