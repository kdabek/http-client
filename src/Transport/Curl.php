<?php

declare(strict_types=1);

namespace Kdabek\HttpClient\Transport;

use CurlHandle;
use Kdabek\HttpClient\Header\Header;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

class Curl implements TransportInterface
{
    private ?CurlHandle $handle = null;
    private Header $requestHeaders;
    private Header $responseHeaders;
    private ResponseFactoryInterface $responseFactory;

    public function __construct(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
        $this->requestHeaders = new Header();
        $this->responseHeaders = new Header();
    }

    public function request(RequestInterface $request): ResponseInterface
    {
        $this->connect();
        $this->requestHeaders->exchangeArray($request->getHeaders());

        curl_setopt($this->handle, CURLOPT_URL, $request->getUri()->__toString());
        curl_setopt($this->handle, CURLOPT_HTTPHEADER, $this->requestHeaders->toHeaderLines());
        curl_setopt($this->handle, CURLOPT_CUSTOMREQUEST, $request->getMethod());
        curl_setopt($this->handle, CURLOPT_POSTFIELDS, $request->getBody()->__toString());
        curl_setopt($this->handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->handle, CURLOPT_HEADERFUNCTION, [$this, 'setResponseHeaders']);

        $result = curl_exec($this->handle);
        $httpCode = curl_getinfo($this->handle, CURLINFO_HTTP_CODE);

        $response = $this->responseHeaders->bindTo(
            $this->responseFactory->createResponse($httpCode)
        );
        $response->getBody()->write($result);

        $this->disconnect();

        return $response;
    }

    /**
     * @codeCoverageIgnore
     */
    private function connect(): void
    {
        if ($this->isConnected()) {
            $this->disconnect();
        }
        $this->handle = curl_init();
    }

    /**
     * @codeCoverageIgnore
     */
    private function disconnect(): void
    {
        if ($this->isConnected()) {
            curl_close($this->handle);
        }
        $this->handle = null;
    }

    /**
     * @codeCoverageIgnore
     */
    private function isConnected(): bool
    {
        return !is_null($this->handle) && $this->handle instanceof CurlHandle;
    }

    /**
     * @param resource $handle
     * @param string $header
     * @return int
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.UnusedPrivateMethods)
     */
    private function setResponseHeaders($handle, string $header): int
    {
        $len = strlen($header);
        $header = explode(':', $header, 2);
        if (count($header) < 2) {
            return $len;
        }

        $this->responseHeaders->offsetSet(trim($header[0]), trim($header[1]));

        return $len;
    }
}
