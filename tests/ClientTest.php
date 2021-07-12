<?php

declare(strict_types=1);

namespace Kdabek\HttpClient;

use Kdabek\HttpClient\Header\Header;
use Kdabek\HttpClient\Header\MimeType;
use Kdabek\HttpClient\Response\ResponseInterface;
use Kdabek\HttpClient\Transport\TransportInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface as PsrResponse;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class ClientTest extends TestCase
{
    private RequestFactoryInterface $requestFactory;
    private TransportInterface $transport;
    private Client $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->requestFactory = $this->getMockForAbstractClass(RequestFactoryInterface::class);
        $this->transport = $this->getMockForAbstractClass(TransportInterface::class);
        $this->client = new Client($this->requestFactory, $this->transport);
    }

    public function testGet()
    {
        $this->expectRequest('GET', 'http://example.com');
        $response = $this->client->get('http://example.com');
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    public function testPost()
    {
        $this->expectRequest('POST', 'http://example.com', ['name' => 'John']);
        $response = $this->client->post('http://example.com', ['name' => 'John']);
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    public function testPut()
    {
        $this->expectRequest('PUT', 'http://example.com', ['name' => 'Robert']);
        $response = $this->client->put('http://example.com', ['name' => 'Robert']);
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    public function testDelete()
    {
        $this->expectRequest('DELETE', 'http://example.com', ['name' => 'Robert']);
        $response = $this->client->delete('http://example.com', ['name' => 'Robert']);
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    public function testWithHeaders()
    {
        $headers = [
            'Accept-Language' => 'en-US'
        ];
        $this->expectRequest('POST', 'http://example.com', ['name' => 'John'], $headers);
        $response = $this->client->withHeaders($headers)->post('http://example.com', ['name' => 'John']);
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    private function expectRequest(string $method, string $url, array $data = [], array $headers = [])
    {
        $defaultHeaders = [
            Header::ACCEPT       => MimeType::JSON,
            Header::CONTENT_TYPE => MimeType::JSON
        ];
        $headers = array_merge($defaultHeaders, $headers);
        $streamInterface = $this->getMockForAbstractClass(StreamInterface::class);
        $streamInterface->method('__toString')->willReturn(json_encode($data));
        $request = $this->getMockForAbstractClass(RequestInterface::class);
        $request->method('getBody')->willReturn($streamInterface);

        $request
            ->expects($this->atLeastOnce())
            ->method('withAddedHeader')
            ->withConsecutive(...array_map(function ($name, $value) {
                return [$name, $value];
            }, array_keys($headers), array_values($headers)))
            ->willReturnSelf();

        $this->requestFactory
            ->expects($this->once())
            ->method('createRequest')
            ->with($method, $url)
            ->willReturn($request);

        $this->transport
            ->expects($this->once())
            ->method('request')
            ->with($request)
            ->willReturn($this->getMockForAbstractClass(PsrResponse::class));
    }
}
