<?php

declare(strict_types=1);

namespace Kdabek\HttpClient\Transport;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use Symfony\Component\Process\Process;

class CurlTest extends TestCase
{
    private ResponseFactoryInterface $responseFactory;
    private Curl $curl;
    private Process $process;

    protected function setUp(): void
    {
        parent::setUp();

        $this->responseFactory = $this->getMockForAbstractClass(ResponseFactoryInterface::class);
        $this->curl = new Curl($this->responseFactory);
        $this->process = new Process(['php', '-S', 'localhost:8000', '-t', 'tests/_server/']);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function testResponse()
    {
        $this->process->setTimeout(5);
        $this->process->start();

        $this->process->waitUntil(function ($type, $output) {
            return strpos($output, 'Development Server (http://localhost:8000) started');
        });

        $request = $this->createRequest(
            'http://localhost:8000',
            'GET',
            [
                'Accept' => ['application/json'],
                'Content-Type' => ['application/json']
            ]
        );

        $streamInterface = $this->getMockForAbstractClass(StreamInterface::class);
        $streamInterface->method('write')->with('{"first_name": "John", "last_name": "Doe"}');
        $streamInterface->method('__toString')->willReturn('{"first_name": "John", "last_name": "Doe"}');

        $response = $this->getMockForAbstractClass(ResponseInterface::class);
        $response->expects($this->atLeastOnce())->method('withAddedHeader')->willReturnSelf();
        $response->method('getBody')->willReturn($streamInterface);
        $response->method('getStatusCode')->willReturn(200);
        $this->responseFactory->method('createResponse')->with(200)->willReturn($response);

        $response = $this->curl->request($request);

        $this->process->stop(1, 2);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getBody()->__toString());
    }

    private function createRequest(string $uri, string $method, array $headers, array $data = []): RequestInterface
    {
        $uriInterface = $this->getMockForAbstractClass(UriInterface::class);
        $uriInterface->method('__toString')->willReturn($uri);

        $streamInterface = $this->getMockForAbstractClass(StreamInterface::class);
        $streamInterface->method('__toString')->willReturn(json_encode($data));

        $request = $this->getMockForAbstractClass(RequestInterface::class);
        $request->method('getUri')->willReturn($uriInterface);
        $request->method('getBody')->willReturn($streamInterface);
        $request->method('getMethod')->willReturn($method);
        $request->method('getHeaders')->willReturn($headers);

        return $request;
    }
}
