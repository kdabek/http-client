<?php

declare(strict_types=1);

namespace Kdabek\HttpClient\Auth\Factory;

use Kdabek\HttpClient\Auth\Credentials\CredentialsInterface;
use Kdabek\HttpClient\Auth\Credentials\LoginAndPassword;
use Kdabek\HttpClient\Auth\Credentials\Token;
use Kdabek\HttpClient\Auth\Strategy\BasicAuth;
use Kdabek\HttpClient\Auth\Strategy\TokenAuth;
use Kdabek\HttpClient\Auth\Strategy\UnsupportedStrategyException;
use PHPUnit\Framework\TestCase;

class AuthorizationFactoryTest extends TestCase
{
    private AuthorizationFactory $authorizationFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authorizationFactory = new AuthorizationFactory();
    }

    public function testCreateBasicAuth()
    {
        $strategy = $this->authorizationFactory->createFrom(new LoginAndPassword('John', 'secret'));
        $this->assertInstanceOf(BasicAuth::class, $strategy);
    }

    public function testCreateTokenAuth()
    {
        $strategy = $this->authorizationFactory->createFrom(new Token('secret'));
        $this->assertInstanceOf(TokenAuth::class, $strategy);
    }

    public function testUnsupportedStrategy()
    {
        $this->expectException(UnsupportedStrategyException::class);
        $unsupportedCredentials = $this->getMockForAbstractClass(CredentialsInterface::class);
        $this->authorizationFactory->createFrom($unsupportedCredentials);
    }
}
