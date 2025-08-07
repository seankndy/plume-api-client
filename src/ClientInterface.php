<?php

namespace SeanKndy\PlumeApi;

use Psr\Http\Message\ResponseInterface;

interface ClientInterface
{
    public function request(string $method, string $url, $body = null, array $headers = []): ResponseInterface;

    public function authenticatedRequest(string $method, string $url, $body = null, array $headers = []): ResponseInterface;
}