<?php

declare(strict_types=1);

namespace Kdabek\HttpClient\Response;

use Kdabek\HttpClient\Header\InvalidContentTypeException;
use Psr\Http\Message\ResponseInterface as PsrResponse;

interface ResponseInterface
{
    public function header(string $name): string;

    public function body(): string;

    /**
     * @return array
     * @throws InvalidContentTypeException
     */
    public function json(): array;

    public function status(): int;

    public function successful(): bool;

    public function toPsr(): PsrResponse;
}
