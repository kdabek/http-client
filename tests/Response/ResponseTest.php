<?php

declare(strict_types=1);

namespace Kdabek\HttpClient\Response;

use Kdabek\HttpClient\Header\InvalidContentTypeException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class ResponseTest extends TestCase
{
    private ResponseInterface $responseInterface;
    private Response $response;

    protected function setUp(): void
    {
        parent::setUp();

        $this->responseInterface = $this->getMockForAbstractClass(ResponseInterface::class);
        $this->response = new Response($this->responseInterface);
    }

    public function testHeader()
    {
        $this->expectContentType('application/json');
        $this->assertEquals('application/json', $this->response->header('Content-Type'));
    }

    public function testBody()
    {
        $this->expectBody('{"name" : "John"}');

        $body = $this->response->body();
        $this->assertJson($body);
        $this->assertEquals('{"name" : "John"}', $body);
    }

    public function testInvalidContentTypeWhenTryReturnJson()
    {
        $this->expectContentType('application/xml');
        $this->expectException(InvalidContentTypeException::class);
        $this->expectExceptionMessage('Expects "application/json" content type got "application/xml"');

        $this->response->json();
    }

    public function testEmptyBodyWhenTryReturnJson()
    {
        $this->expectContentType('application/json');
        $this->expectBody('');

        $json = $this->response->json();
        $this->assertIsArray($json);
        $this->assertCount(0, $json);
    }

    public function testJson()
    {
        $this->expectContentType('application/json');
        $this->expectBody('{"name" : "John"}');

        $json = $this->response->json();
        $this->assertIsArray($json);
        $this->assertEquals(['name' => 'John'], $json);
    }

    public function testStatus()
    {
        $this->expectStatus(200);
        $this->assertEquals(200, $this->response->status());
    }

    public function testSuccessful()
    {
        $this->expectStatus(201);
        $this->assertTrue($this->response->successful());
    }

    public function testFailed()
    {
        $this->expectStatus(400);
        $this->assertFalse($this->response->successful());
    }

    public function testGetPsrRequest()
    {
        $this->assertInstanceOf(ResponseInterface::class, $this->response->toPsr());
    }

    private function expectContentType(string $type): void
    {
        $this->responseInterface->method('getHeaderLine')->with('Content-Type')->willReturn($type);
    }

    private function expectBody(string $body): void
    {
        $streamInterface = $this->getMockForAbstractClass(StreamInterface::class);
        $streamInterface->method('__toString')->willReturn($body);
        $this->responseInterface->method('getBody')->willReturn($streamInterface);
    }

    private function expectStatus(int $status): void
    {
        $this->responseInterface->method('getStatusCode')->willReturn($status);
    }
}
