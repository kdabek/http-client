<?php

declare(strict_types=1);

namespace Kdabek\HttpClient;

use Kdabek\HttpClient\Auth\Credentials\CredentialsInterface;
use Kdabek\HttpClient\Auth\Credentials\LoginAndPassword;
use Kdabek\HttpClient\Auth\Credentials\Token;
use Kdabek\HttpClient\Auth\Factory\AuthorizationFactoryInterface;
use Kdabek\HttpClient\Header\Header;
use Kdabek\HttpClient\Header\MimeType;
use Kdabek\HttpClient\Response\Response;
use Kdabek\HttpClient\Response\ResponseInterface;
use Kdabek\HttpClient\Transport\TransportInterface;
use Psr\Http\Message\RequestFactoryInterface;

class Client
{
    private const DEFAULT_HEADERS = [
        Header::ACCEPT       => MimeType::JSON,
        Header::CONTENT_TYPE => MimeType::JSON
    ];
    private RequestFactoryInterface $requestFactory;
    private TransportInterface $transport;
    private AuthorizationFactoryInterface $authorizationFactory;
    private Header $headers;

    public function __construct(
        RequestFactoryInterface $requestFactory,
        TransportInterface $transport,
        AuthorizationFactoryInterface $authorizationFactory
    ) {
        $this->requestFactory = $requestFactory;
        $this->transport = $transport;
        $this->authorizationFactory = $authorizationFactory;
        $this->headers = new Header(self::DEFAULT_HEADERS);
    }

    public function get(string $url): ResponseInterface
    {
        return $this->request('GET', $url);
    }

    public function post(string $url, array $data = []): ResponseInterface
    {
        return $this->request('POST', $url, $data);
    }

    public function put(string $url, array $data = []): ResponseInterface
    {
        return $this->request('PUT', $url, $data);
    }

    public function delete(string $url, array $data = []): ResponseInterface
    {
        return $this->request('DELETE', $url, $data);
    }

    public function withHeaders(array $headers): self
    {
        $this->headers->exchangeArray(array_merge($this->headers->getArrayCopy(), $headers));

        return $this;
    }

    public function clearHeaders(): self
    {
        $this->headers->exchangeArray(self::DEFAULT_HEADERS);

        return $this;
    }

    public function withBasicAuth(string $login, string $password): self
    {
        $this->setAuth(new LoginAndPassword($login, $password));

        return $this;
    }

    public function withToken(string $token): self
    {
        $this->setAuth(new Token($token));

        return $this;
    }

    public function clearAuth(): self
    {
        $this->headers->offsetUnset(Header::AUTHORIZATION);

        return $this;
    }

    private function setAuth(CredentialsInterface $credentials): void
    {
        $strategy = $this->authorizationFactory->createFrom($credentials);
        $this->withHeaders($strategy->getCredentials());
    }

    protected function request(string $method, string $url, array $data = []): ResponseInterface
    {
        $request = $this->headers->bindTo(
            $this->requestFactory->createRequest($method, $url)
        );
        $request->getBody()->write(json_encode($data));

        return new Response($this->transport->request($request));
    }
}
