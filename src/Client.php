<?php

declare(strict_types=1);

namespace Kdabek\HttpClient;

use Kdabek\HttpClient\Header\Header;
use Kdabek\HttpClient\Header\MimeType;
use Kdabek\HttpClient\Response\Response;
use Kdabek\HttpClient\Response\ResponseInterface;
use Kdabek\HttpClient\Transport\TransportInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

class Client
{
    private const DEFAULT_HEADERS = [
        Header::ACCEPT       => MimeType::JSON,
        Header::CONTENT_TYPE => MimeType::JSON
    ];
    private RequestFactoryInterface $requestFactory;
    private UriFactoryInterface $uriFactory;
    private TransportInterface $transport;
    private Header $headers;

    public function __construct(
        RequestFactoryInterface $requestFactory,
        UriFactoryInterface $uriFactory,
        TransportInterface $transport
    ) {
        $this->requestFactory = $requestFactory;
        $this->uriFactory = $uriFactory;
        $this->transport = $transport;
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

    protected function request(string $method, string $url, array $data = []): ResponseInterface
    {
        $request = $this->headers->bindTo(
            $this->requestFactory->createRequest($method, $this->uriFactory->createUri($url))
        );
        $request->getBody()->write(json_encode($data));

        return new Response($this->transport->request($request));
    }
}
