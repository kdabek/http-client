<?php

declare(strict_types=1);

namespace Kdabek\HttpClient\Auth\Credentials;

use PHPUnit\Framework\TestCase;

class LoginAndPasswordTest extends TestCase
{
    private LoginAndPassword $credentials;

    protected function setUp(): void
    {
        parent::setUp();

        $this->credentials = new LoginAndPassword('John', 'secret');
    }

    public function testGetters()
    {
        $this->assertEquals('John', $this->credentials->getLogin());
        $this->assertEquals('secret', $this->credentials->getPassword());
    }
}
