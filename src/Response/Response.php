<?php

declare(strict_types=1);

namespace Kdabek\HttpClient\Response;

use Kdabek\HttpClient\Header\Header;
use Kdabek\HttpClient\Header\InvalidContentTypeException;
use Kdabek\HttpClient\Header\MimeType;
use Psr\Http\Message\ResponseInterface as PsrResponse;

class Response implements ResponseInterface
{
    private PsrResponse $response;

    public function __construct(PsrResponse $response)
    {
        $this->response = $response;
    }

    public function header(string $name): string
    {
        return $this->response->getHeaderLine($name);
    }

    public function body(): string
    {
        return $this->response->getBody()->__toString();
    }

    public function json(): array
    {
        if (!$this->isValidContentType(MimeType::JSON)) {
            throw new InvalidContentTypeException(
                MimeType::JSON,
                $this->header(Header::CONTENT_TYPE)
            );
        }

        if (empty($this->body())) {
            return [];
        }

        return json_decode($this->body(), true);
    }

    public function status(): int
    {
        return $this->response->getStatusCode();
    }

    public function successful(): bool
    {
        return $this->status() >= 200 && $this->status() < 300;
    }

    public function toPsr(): PsrResponse
    {
        return $this->response;
    }

    public function isValidContentType(string $mimeType): bool
    {
        return in_array($mimeType, explode(';', $this->header(Header::CONTENT_TYPE)), true);
    }
}
