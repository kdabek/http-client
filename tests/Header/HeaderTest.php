<?php

declare(strict_types=1);

namespace Kdabek\HttpClient\Header;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

class HeaderTest extends TestCase
{
    private Header $header;

    protected function setUp(): void
    {
        parent::setUp();

        $this->header = new Header([
            'Accept' => ['application/json'],
            'Content-Encoding' => ['gzip']
        ]);
    }

    public function testBind()
    {
        $request = $this->getMockForAbstractClass(RequestInterface::class);
        $request->expects($this->exactly(2))
            ->method('withAddedHeader')
            ->withConsecutive(
                ['Accept', ['application/json']],
                ['Content-Encoding', ['gzip']]
            )
            ->willReturnSelf();

        $this->assertInstanceOf(RequestInterface::class, $this->header->bindTo($request));
    }

    public function testConvertToHeaderLines()
    {
        $expectedHeaders = [
            'Accept: application/json',
            'Content-Encoding: gzip'
        ];

        $this->assertEquals($expectedHeaders, $this->header->toHeaderLines());
    }
}
